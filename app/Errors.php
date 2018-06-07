<?php

namespace App;

class Errors
{
    public static function generate($codes){
        return [
            [
                "code" => 1001,
                "message" => "token not found",
            ]
        ];
    }
}
