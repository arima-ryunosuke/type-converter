<?php
namespace ryunosuke\TypeConverter;

/**
 * phpのネイティブ配列とJSON形式を相互に変換するクラス
 *
 * @author ryunosuke
 */
class Json extends AbstractConverter
{

    protected $mimetype = 'application/json';

    public function __construct($option = array())
    {
        $option = $option + array(
            JSON_UNESCAPED_UNICODE => true,
            JSON_PRETTY_PRINT => true
        );
        
        parent::__construct($option);
    }

    public function convert($data)
    {
        $option = array_sum(array_keys(array_filter($this->option)));
        return json_encode($data, $option);
    }

    public function deconvert($data)
    {
        $result = json_decode($data, true);
        $error = json_last_error();
        
        if ($error !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('invalid json format', $error);
        }
        
        return $result;
    }
}
