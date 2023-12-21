<?php

use \PHPUnit\Framework\TestCase;
use function \Brain\Monkey\setUp;
use function \Brain\Monkey\tearDown;

class TestClassBrainMonkey extends TestCase {

    function setUp(): void
    {
        parent::setUp();
        setUp();
    }

    function tearDown(): void
    {
        tearDown();
        parent::tearDown();
    }

}
