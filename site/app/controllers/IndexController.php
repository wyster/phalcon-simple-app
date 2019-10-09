<?php declare(strict_types=1);

namespace app\controllers;

use app\forms\Auth as FormAuth;
use Datto\JsonRpc\Client;
use Phalcon\Http\Client\Request as ClientRequest;
use Phalcon\Http\Client\Response as ClientResponse;
use Phalcon\Http\RequestInterface;
use Phalcon\Translate\AdapterInterface as Translate;
use function array_key_exists;
use function is_array;

class IndexController extends \Phalcon\Mvc\Controller
{
    private function getTranslation(): Translate
    {
        return $this->getDI()->get(Translate::class);
    }

    private function getForm(): FormAuth
    {
        return $this->getDI()->get(FormAuth::class);
    }

    public function indexAction()
    {
        $this->view->form = $this->getForm();
        return $this->view->render('index', 'index');
    }

    public function authAction()
    {
        $form = $this->getForm();
        $this->view->form = $form;

        if (!$form->isValid($this->request->getPost())) {
            foreach ($form->getMessages() as $body) {
                $this->flash->error($body->getMessage());
            }
            return $this->view->render('index', 'index');
        }

        $body = $this->prepareRequestBody($this->request);
        $response = $this->sendRequest($body);

        $this->view->request = $body;
        $this->view->response = $response->body;
        $this->view->message = $this->getTranslation()->t($this->prepareResponse($response));

        return $this->view->render('index', 'index');
    }

    private function prepareResponse(ClientResponse $response): string
    {
        if ($response->header->statusCode !== 200) {
            return 'Fatal error! Status code: ' . $response->header->statusCode;
        }

        if (!$response->body) {
            return 'Fatal error! Body is empty';
        }

        try {
            $result = json_decode($response->body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return 'JSON exception: ' . $e->getMessage();
        }
        if (array_key_exists('error', $result)) {
            if (!is_array($result['error']) || !array_key_exists('message', $result['error'])) {
                return 'Invalid response, not found section error.message';
            }
            return $result['error']['message'];
        }

        if (!array_key_exists('result', $result)) {
            return 'Invalid response, not found section result';
        }

        if (!array_key_exists('id', $result['result'])) {
            return 'Invalid response, not found section result.id';
        }

        return 'Success auth, user id: ' . $result['result']['id'];
    }

    private function prepareRequestBody(RequestInterface $request): string
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

        return $client->encode();
    }

    private function sendRequest(string $body): ClientResponse
    {
        $provider = $this->getDI()->get(ClientRequest::class);
        $response = $provider->post(
        // @todo вынести в env
            'http://users',
            $body
        );
        return $response;
    }
}
