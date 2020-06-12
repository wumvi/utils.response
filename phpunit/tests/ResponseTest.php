<?php

use PHPUnit\Framework\TestCase;
use Wumvi\Utils\Response;

class ResponseTest extends TestCase
{
    public function testGetCryptSalt(): void
    {
        $this->assertEquals(
            Response::jsonSuccess([1]),
            ['Content-Type: application/json', '{"status":"ok","data":[1]}'],
            'Json Success'
        );

        $this->assertEquals(
            Response::jsonError("test"),
            ['Content-Type: application/json', '{"status":"error","msg":"test"}'],
            'Json Error'
        );
    }
}