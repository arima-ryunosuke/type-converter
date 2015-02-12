<?php
namespace ryunosuke\TypeConverter;

/**
 * phpのネイティブ配列とphpシリアライズ形式を相互に変換するクラス
 *
 * @author ryunosuke
 */
class Php extends AbstractConverter
{

    protected $mimetype = 'text/plain';

    public function convert($data)
    {
        return serialize($data);
    }

    public function deconvert($data)
    {
        $result = unserialize($data);
        
        //falseでかつ文字列表現が一致しないのであれば変換に失敗している
        if ($result === false && serialize(false) !== $data) {
            throw new \InvalidArgumentException('invalid serialized php format');
        }
        
        return $result;
    }
}
