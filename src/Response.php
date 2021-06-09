<?php
declare(strict_types=1);

namespace Wumvi\Utils;

use Wumvi\Sign\Encode;

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
    public const FIELD_HINT = 'hint';
    public const STATUS_OK = 'ok';
    public const STATUS_ERROR = 'error';
    public const CONTENT_TYPE_JSON = 'Content-Type: application/json';
    public const CONTENT_TYPE_BINARY = 'Content-Type: application/octet-stream';

    public function __construct(
        public string $algo,
        public string $saltName,
        public string $saltValue
    ) {
    }

    public function successSign(array $dataRaw, bool $isBinary = false): array
    {
        list ($contentType, $data) = $isBinary ? self::binarySuccess($dataRaw) : self::jsonSuccess($dataRaw);
        $data = Encode::createSignWithData(
            $data,
            $this->saltName,
            $this->saltValue,
            $this->algo
        );

        return [
            $contentType,
            $data
        ];
    }

    public static function binarySuccess(array $data): array
    {
        return [
            self::CONTENT_TYPE_BINARY,
            igbinary_serialize([
                self::FIELD_STATUS => self::STATUS_OK,
                self::FIELD_DATA => $data,
            ]),
        ];
    }

    public static function binaryError(string $msg): array
    {
        return [
            self::CONTENT_TYPE_BINARY,
            igbinary_serialize([
                self::FIELD_STATUS => self::STATUS_ERROR,
                self::FIELD_MSG => $msg,
            ]),
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
            json_encode([
                self::FIELD_STATUS => self::STATUS_OK,
                self::FIELD_DATA => $data,
            ]),
        ];
    }

    public static function jsonError(string $msg, string $hint = ''): array
    {
        return [
            self::CONTENT_TYPE_JSON,
            json_encode([
                self::FIELD_STATUS => self::STATUS_ERROR,
                self::FIELD_MSG => $msg,
                self::FIELD_HINT => $hint
            ]),
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
