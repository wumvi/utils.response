<?php

use PHPUnit\Framework\TestCase;
use Wumvi\Utils\Response;

class ResponseTest extends TestCase
{
    public function testSimple(): void
    {
        $this->assertEquals(
            ['Content-Type: application/json', '{"status":"ok","data":[1]}'],
            Response::jsonSuccess([1]),
            'Json Success'
        );

        $this->assertEquals(
            ['Content-Type: application/json', '{"status":"error","msg":"test"}'],
            Response::jsonError("test"),
            'Json Error'
        );

        $act = Response::binarySuccess([1]);
        $act[1] = base64_encode($act[1]);
        $this->assertEquals(
            ['Content-Type: application/octet-stream', 'AAAAAhQCEQZzdGF0dXMRAm9rEQRkYXRhFAEGAAYB'],
            $act,
            'Binary Success'
        );

        $act = Response::binaryError('error');
        $act[1] = base64_encode($act[1]);
        $this->assertEquals(
            ['Content-Type: application/octet-stream', 'AAAAAhQCEQZzdGF0dXMRBWVycm9yEQNtc2cOAQ=='],
            $act,
            'Binary Error'
        );
    }

    public function testSign(): void
    {
        $response = new Response('m5', '123');
        $json = $response->successSign([1], false);
        $this->assertEquals(
            ['Content-Type: application/json', 'm516d1a2b7c86f137adf1d393ee596addc{"status":"ok","data":[1]}'],
            $json,
            'Json Success'
        );

        $act = $response->successSign([1], true);
        $act[1] = base64_encode($act[1]);
        $this->assertEquals(
            [
                'Content-Type: application/octet-stream',
                'bTVhZDM5ZTNjNmQyZGM2ODNmOGZlMDU2M2RkZGU3ZmUyNwAAAAIUAhEGc3RhdHVzEQJvaxEEZGF0YRQBBgAGAQ=='
            ],
            $act,
            'Binary Success'
        );
    }
}
