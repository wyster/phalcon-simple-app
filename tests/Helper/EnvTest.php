<?php declare(strict_types=1);

namespace app\Helper;

use Exception;
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    public function testGetFromEnv():  void
    {
        $name = 'USERS_CONTAINER_NAME';
        $_ENV[$name] = null;
        $this->assertNull(Env::getFromEnv($name));

        $_ENV[$name] = 'value';
        $this->assertSame($_ENV[$name], Env::getFromEnv($name));
    }

    public function testGetFromEnvException():  void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Env USERS_CONTAINER_NAME not setted');
        $name = 'USERS_CONTAINER_NAME';
        unset($_ENV[$name]);
        Env::getFromEnv($name);
    }
}
