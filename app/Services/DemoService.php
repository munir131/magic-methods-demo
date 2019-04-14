<?php
namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class DemoService {

    public function __call($methodName, $args) {
        $argsString = implode(', ', $args);
        return "{$methodName} called with [{$argsString}]";
    }

    public static function __callStatic($methodName, $args) {
        $name = substr($methodName, 10);
        if ($name) {
            $snakeCase = Str::snake($name);
            $name = ucwords(str_replace('_', ' ', $snakeCase));
        }
        return "Hi, {$name}!";
    }
}