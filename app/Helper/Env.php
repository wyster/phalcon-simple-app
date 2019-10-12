<?php declare(strict_types=1);

namespace app\Helper;
use Exception;
use function array_key_exists;

/**
 * @author Ilya Zelenin <wyster@make.im>
 */
class Env
{
    public static function getFromEnv(string $name)
    {
        if (!array_key_exists($name, $_ENV)) {
            throw new Exception("Env {$name} not setted");
        }

        return $_ENV[$name];
    }
}
