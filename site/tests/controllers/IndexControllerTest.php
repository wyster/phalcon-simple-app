<?php declare(strict_types=1);

namespace Test\Controllers;

use Phalcon\Mvc\View;
use Test\UnitTestCase;

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
        $controller = new \app\controllers\IndexController();
        $content = $controller->indexAction()->getContent();
        $this->assertStringContainsString('<form', $content);
        $this->assertStringContainsString('name="login"', $content);
        $this->assertStringContainsString('name="password"', $content);
        $this->assertStringContainsString('name="csrf"', $content);
        $this->assertStringContainsString('type="submit"', $content);
    }
}
