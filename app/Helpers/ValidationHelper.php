<?php

namespace App\Helpers;

class ValidationHelper
{

    /**
     * @param array $errors
     * @return array
     */
    public static function formatErrors(array $errors): array {
        $response = [];

        foreach ($errors as $key => $error) {
            $response[] = [
                'field' => $key,
                'message' => $error[0]
            ];
        }

        return $response;
    }
}
