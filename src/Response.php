<?php
declare(strict_types=1);

namespace Wumvi\Utils;

use Wumvi\Utils\Sign;

/**
 *
 *
 * @author Козленко В.Л.
 */
class Response
{
    public const HEADER = 0;
    public const DATA = 1;
    public const FIELD_STATUS = 'status';
    public const FIELD_MSG = 'msg';
    public const FIELD_DATA = 'data';
    public const FIELD_SIGN = 'sign';
    public const STATUS_OK = 'ok';
    public const STATUS_ERROR = 'error';
    public const CONTENT_TYPE_JSON = 'Content-Type: application/json';
    public const CONTENT_TYPE_BINARY = 'Content-Type: application/octet-stream';

    private string $algo;
    private string $salt;

    public function __construct(string $algo, string $salt)
    {
        $this->algo = $algo;
        $this->salt = $salt;
    }

    public function successSign(array $data, bool $isBinary = false): array
    {
        $data = $isBinary ? self::binarySuccess($data) : self::jsonSuccess($data);
        $data[self::DATA] = Sign::getSignWithData($data[self::DATA], $this->salt, $this->algo);

        return $data;
    }

    public static function binarySuccess(array $data): array
    {
        return [
            self::CONTENT_TYPE_BINARY,
            igbinary_serialize([self::FIELD_STATUS => self::STATUS_OK, self::FIELD_DATA => $data,]),
        ];
    }

    public static function binaryError(string $msg): array
    {
        return [
            self::CONTENT_TYPE_BINARY,
            igbinary_serialize([self::FIELD_STATUS => self::STATUS_ERROR, self::FIELD_MSG => $msg,]),
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public static function jsonSuccess(array $data): array
    {
        return [
            self::CONTENT_TYPE_JSON,
            json_encode([self::FIELD_STATUS => self::STATUS_OK, self::FIELD_DATA => $data,]),
        ];
    }

    public static function jsonError(string $msg): array
    {
        return [
            self::CONTENT_TYPE_JSON,
            json_encode([self::FIELD_STATUS => self::STATUS_ERROR, self::FIELD_MSG => $msg,]),
        ];
    }

    /**
     * @codeCoverageIgnore
     * @param array $data
     */
    public static function flush(array $data): void
    {
        header($data[self::HEADER]);
        echo $data[self::DATA];
    }
}
