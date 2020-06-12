<?php
declare(strict_types=1);

namespace Wumvi\Utils;

/**
 *
 *
 * @author Козленко В.Л.
 */
class Response
{
    public const FIELD_STATUS = 'status';
    public const FIELD_MSG = 'msg';
    public const FIELD_DATA = 'data';
    public const STATUS_OK = 'ok';
    public const STATUS_ERROR = 'error';
    public const CONTENT_TYPE_JSON = 'Content-Type: application/json';

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
        header($data[0]);
        echo $data[1];
    }
}
