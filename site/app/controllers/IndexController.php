<?php declare(strict_types=1);

use Datto\JsonRpc\Client;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Form;
use Phalcon\Http\Client\Request;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;

class IndexController extends \Phalcon\Mvc\Controller
{
    private function getForm(): Form
    {
        $form = new Form();
        $form->add(new Text('login', ['useEmpty' => false, 'required' => true]));
        $form->add(new Text('password', ['useEmpty' => false, 'required' => true]));
        //$form->add(new Hidden('csrf', ['value' => $this->security->getToken()]));

        return $form;
    }

    public function index()
    {
        $this->view->form = $this->getForm();
        return $this->view->render('index', 'index');
    }

    public function auth()
    {
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
            $message = $result['error']['message'];
        } else {
            $message = 'Success auth, user id: ' . $result['result']['id'];
        }

        $this->view->form = $this->getForm();
        $this->view->message = $message;
        $this->view->response = $response->body;
        return $this->view->render('index', 'index');
    }
}

