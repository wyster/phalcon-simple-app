<?php

namespace Test;

use Phalcon\Di;
use Phalcon\Test\UnitTestCase as PhalconTestCase;
use PHPUnit\Framework\IncompleteTestError;

abstract class UnitTestCase extends PhalconTestCase
{
    /**
     * @var bool
     */
    private $_loaded = false;

    public function setUp(): void
    {
        parent::setUp();

        // Load any additional services that might be required during testing
        $di = Di::getDefault();

        // Get any DI components here. If you have a config, be sure to pass it to the parent

        $this->setDi($di);

        $this->_loaded = true;
    }

    /**
     * Check if the test case is setup properly
     *
     * @throws IncompleteTestError;
     */
    public function __destruct()
    {
        if (!$this->_loaded) {
            throw new IncompleteTestError(
                'Please run parent::setUp().'
            );
        }
    }
}
