<?php

namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{

    public $errors = [];

    public function __construct($message = null, $code = 400, $errors = []) {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function render( $request ) {
        return response()->json([
            'message' => $this->getMessage(),
            'errors' => $this->errors
        ], $this->getCode());
    }

}
