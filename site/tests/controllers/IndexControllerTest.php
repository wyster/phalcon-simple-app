<?php declare(strict_types=1);

namespace app\test\controllers;

use Phalcon\Mvc\View;
use app\test\UnitTestCase;

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
}
