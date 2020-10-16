<?php

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    public function testAreWorking(): void
    {
        $this->assertEquals(2, 1+1);
    }
}