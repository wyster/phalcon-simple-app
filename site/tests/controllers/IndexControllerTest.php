<?php declare(strict_types=1);

namespace app\test\controllers;

use app\controllers\IndexController;
use app\test\UnitTestCase;
use Phalcon\Mvc\View;

/**
 * Class UnitTest
 */
class IndexControllerTest extends UnitTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $security = $this->createMock(\Phalcon\Security::class);
        $security->method('checkToken')->willReturn(true);
        $this->di->setShared('security', $security);
        $this->di->setShared('config', function () {
            return include APP_PATH . '/config/config.php';
        });
        $this->di->setShared('view', function () {
            $config = $this->getConfig();
            $view = new View();
            $view->setViewsDir($config->application->viewsDir);
            return $view;
        });
    }

    public function testIndex(): void
    {
        $controller = new IndexController();
        ob_start();
        $controller->indexAction()->getContent();
        $content = ob_get_contents();
        ob_end_clean();
        $this->assertStringContainsString('<form', $content);
        $this->assertStringContainsString('name="login"', $content);
        $this->assertStringContainsString('name="password"', $content);
        $this->assertStringContainsString('name="csrf"', $content);
        $this->assertStringContainsString('type="submit"', $content);
    }

    public function testAuthAction(): void
    {
        $request = $this->createMock(\Phalcon\Http\Request::class);
        $request->method('getPost')->willReturn([
            'login' => 'admin',
            'password' => 'admin',
            'csrf' => 'test'
        ]);
        $clientRequest = $this->createMock(\Phalcon\Http\Client\Provider\Curl::class);
        $clientRequest->method('post')->willReturn(new \Phalcon\Http\Client\Response());
        $this->di->set(\Phalcon\Http\Client\Request::class, $clientRequest);
        $this->di->setShared('request', $request);
        $this->di->setShared('flash', new \Phalcon\Flash\Direct());
        $this->di->set(\Phalcon\Translate\AdapterInterface::class, $this->createMock(\Phalcon\Translate\Adapter::class));
        $controller = new IndexController();
        ob_start();
        $controller->authAction();
        ob_end_clean();
        $this->assertTrue(true);
    }

    public function testAuthActionError(): void
    {
        $request = $this->createMock(\Phalcon\Http\Request::class);
        $request->method('getPost')->willReturn([]);
        $clientRequest = $this->createMock(\Phalcon\Http\Client\Provider\Curl::class);
        $clientRequest->method('post')->willReturn(new \Phalcon\Http\Client\Response());
        $this->di->set(\Phalcon\Http\Client\Provider\Curl::class, $clientRequest);
        $this->di->setShared('request', $request);
        $flash = $this->createMock(\Phalcon\Flash::class);
        $flash->expects($this->exactly(5))->method('error');
        $this->di->setShared('flash', $flash);
        $controller = new IndexController();
        ob_start();
        $controller->authAction();
        ob_end_clean();
    }

    public function testPrepareResponse(): void
    {
        $controller = new IndexController();
        $method = new \ReflectionMethod($controller, 'prepareResponse');
        $method->setAccessible(true);

        $response = new \Phalcon\Http\Client\Response();
        $response->header->statusCode = 500;
        $result = $method->invoke($controller, $response);
        $this->assertSame('Fatal error! Status code: 500', $result);

        $response->header->statusCode = 200;
        $result = $method->invoke($controller, $response);
        $this->assertSame('Fatal error! Body is empty', $result);

        $response->header->statusCode = 200;
        $response->body = '{"test"}';
        $result = $method->invoke($controller, $response);
        $this->assertSame('JSON exception: Syntax error', $result);

        $response->header->statusCode = 200;
        $response->body = '{"error":"Invalid request"}';
        $result = $method->invoke($controller, $response);
        $this->assertSame('Invalid response, not found section error.message', $result);

        $response->header->statusCode = 200;
        $response->body = '{"error":{}}';
        $result = $method->invoke($controller, $response);
        $this->assertSame('Invalid response, not found section error.message', $result);

        $response->header->statusCode = 200;
        $response->body = '{"error":{"message:": "Invalid request"}}';
        $result = $method->invoke($controller, $response);
        $this->assertSame('Invalid response, not found section error.message', $result);

        $response->header->statusCode = 200;
        $response->body = '{}';
        $result = $method->invoke($controller, $response);
        $this->assertSame('Invalid response, not found section result', $result);

        $response->header->statusCode = 200;
        $response->body = '{"result":{}}';
        $result = $method->invoke($controller, $response);
        $this->assertSame('Invalid response, not found section result.id', $result);

        $response->header->statusCode = 200;
        $response->body = '{"result":{"id":1}}';
        $result = $method->invoke($controller, $response);
        $this->assertSame('Success auth, user id: 1', $result);
    }
}
