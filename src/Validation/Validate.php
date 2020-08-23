<?php

namespace System\Validation;

use Rakit\Validation\Validator;
use System\Http\Request;
use System\Session\Session;
use System\Url\Url;

class Validate
{
    /**
     * 
     */
    private function __construct()
    {
    }
    /**
     * 
     */
    public static function Validate(array $rouls, $json)
    {
        $validator = new Validator;

        $validation = $validator->make($_POST + $_FILES, $rouls);
        $errors = $validation->errors();
        if ($validation->fails()) {
            if ($json) {
                return ['errors' => $errors->firstOfAll()];
            } else {
                Session::set("errors", $errors);
                Session::set("old_data", Request::All());
                return Url::redir(Url::ref_path());
            }
        }
    }
}
