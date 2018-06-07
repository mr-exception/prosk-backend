<?php

namespace App;

class Errors
{
    public static function generate($codes){
        $errors = [];
        foreach($codes as $code){
            array_push($errors, [
                "code"      => $code,
                "message"   => config('errors.' . $code)
            ]);
        }
        return $errors;
    }
}
