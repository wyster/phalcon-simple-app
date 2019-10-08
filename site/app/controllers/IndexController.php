<?php declare(strict_types=1);

use Datto\JsonRpc\Client;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;
use Phalcon\Http\Client\Request;
use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\PresenceOf;

class IndexController extends \Phalcon\Mvc\Controller
{
    /**
     * @url https://docs.phalcon.io/3.4/ru-ru/translate
     * @return NativeArray
     */
    private function getTranslation(): NativeArray
    {
        // Получение оптимального языка из браузера
        $language = $this->request->getBestLanguage();

        $translationFile = APP_PATH . '/messages/' . $language. '.php';

        // Проверка существования перевода для полученного языка
        if (file_exists($translationFile)) {
            $messages = require $translationFile;
        } else {
            // Переключение на язык по умолчанию
            $messages = require APP_PATH . '/messages/en.php';
        }

        // Возвращение объекта работы с переводом
        return new NativeArray(
            [
                'content' => $messages,
            ]
        );
    }

    private function createForm(): Form
    {
        return new \app\forms\Auth();
    }

    public function index()
    {
        $this->session->start();
        $this->view->form = $this->createForm();
        return $this->view->render('index', 'index');
    }

    public function auth()
    {
        $this->session->start();
        $form = $this->createForm();
        $this->view->form = $form;

        if (!$form->isValid($this->request->getPost())) {
            foreach($form->getMessages() as $message){
                $this->flash->error($message->getMessage());
            }
            return $this->view->render('index', 'index');
        }
        $client = new Client();
        $client->query(
            1,
            'auth.index',
            [
                'login' => $this->request->getPost('login'),
                'password' => $this->request->getPost('password')
            ]
        );

        $message = $client->encode();
        $this->view->request = $message;

        $provider = Request::getProvider();
        $provider->setBaseUri('http://users');
        $response = $provider->post(
            '/',
            [$message],
            false,
            [
                'Content-Type' => 'application/json'
            ]
        );

        $result = json_decode($response->body, true);
        if (array_key_exists('error', $result)) {
            $message = $this->getTranslation()->t($result['error']['message']);
        } else {
            $message = 'Success auth, user id: ' . $result['result']['id'];
        }

        $this->view->message = $message;
        $this->view->response = $response->body;
        return $this->view->render('index', 'index');
    }
}

