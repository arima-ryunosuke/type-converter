<?php

namespace ryunosuke\TypeConverter;

/**
 * phpのネイティブ配列とyaml形式を相互に変換するクラス
 *
 * @author ryunosuke
 */
class Yaml extends AbstractConverter
{
    protected $mimetype = 'text/x-yaml';

    public function convert($data)
    {
        return yaml_emit($data);
    }

    public function deconvert($data)
    {
        $result = yaml_parse($data);

        return $result;
    }
}
