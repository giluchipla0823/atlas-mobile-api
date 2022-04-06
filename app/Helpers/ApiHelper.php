<?php

namespace App\Helpers;

class ApiHelper
{
    CONST IDX_STR_API_JSON = 'jsonapi';
    CONST IDX_STR_API_ENVIRONMENT = 'environment';
    CONST IDX_STR_API_VERSION = 'version';
    CONST IDX_STR_API_NAME = 'name';
    CONST IDX_STR_API_SUMMARY = 'summary';
    CONST IDX_STR_JSON_CODE = "code";
    CONST IDX_STR_JSON_MESSAGE = "message";
    CONST IDX_STR_JSON_ERRORS = "errors";
    CONST IDX_STR_JSON_DATA = "data";
    CONST MSG_SUCCESSFUL_OPERATION = 'Successful operation.';

    private static $response = [];

    /**
     * Estructura de respuesta JSON.
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @param array $extras
     * @return array
     */
    public static function response($data, string $message, int $code, array $extras = []): array
    {
        self::$response[self::IDX_STR_API_JSON] = [
            self::IDX_STR_API_ENVIRONMENT => config('app.env'),
            self::IDX_STR_API_VERSION => '1.0.0',
            self::IDX_STR_API_NAME => 'Bookstore Api',
            self::IDX_STR_API_SUMMARY => 'Api for obtain information about books, authors, publishers and genres.',
        ];
        self::$response[self::IDX_STR_JSON_CODE] = $code;
        self::$response[self::IDX_STR_JSON_MESSAGE] = $message;

        if(is_array($data) || is_object($data)){
            self::$response[self::IDX_STR_JSON_DATA] = $data;
        }

        foreach ($extras as $key => $value){
            self::$response[$key] = $value;
        }

        return self::$response;
    }
}
