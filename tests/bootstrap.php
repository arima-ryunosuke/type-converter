<?php

$loader = require __DIR__ . '/../vendor/autoload.php';

// Windows 用
if (!function_exists('yaml_emit'))
{
    function yaml_emit($data)
    {
        return json_encode($data);
    }
}
if (!function_exists('yaml_parse'))
{
    function yaml_parse($data)
    {
        return json_decode($data, true);
    }
}

// テスト用の具象クラス
class Concrete extends ryunosuke\TypeConverter\AbstractConverter
{
    public function convert($data)
    {
    }

    public function deconvert($data)
    {
    }
}
