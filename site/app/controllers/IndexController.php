<?php declare(strict_types=1);

namespace app\controllers;

use Datto\JsonRpc\Client;
use Exception;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;
use Phalcon\Http\Client\Request;
use Phalcon\Mvc\View;
use Phalcon\Mvc\ViewInterface;
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

    public function indexAction()
    {
        $this->view->form = $this->createForm();
        return $this->view->render('index', 'index');
    }

    public function authAction()
    {
        $form = $this->createForm();
        $this->view->form = $form;

        if (!$form->isValid($this->request->getPost())) {
            foreach ($form->getMessages() as $message) {
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
            $message
        );

        if ($response->header->statusCode !== 200) {
            $this->view->message = 'Fatal error! Status code: ' . $response->header->status;
            $this->view->response = $response->body;
            return $this->view->render('index', 'index');
        }

        $result = json_decode($response->body, true);
        if (!is_array($result)) {
            $this->view->message = 'Fatal error! Body is empty';
            $this->view->response = $response->body;
            return $this->view->render('index', 'index');
        }
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
