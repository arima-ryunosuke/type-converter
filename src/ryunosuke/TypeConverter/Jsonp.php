<?php

namespace ryunosuke\TypeConverter;

/**
 * phpのネイティブ配列とJSONP形式を相互に変換するクラス
 *
 * @author ryunosuke
 */
class Jsonp extends Json
{
    protected $mimetype = 'application/javascript';

    protected $callback = 'callback';

    public function __construct($option = array())
    {
        //コールバック名だけ抜き出してあとは Json クラスとおなじ
        if (array_key_exists('callback', $option))
        {
            if (!preg_match('/^[a-z_][0-9a-z_]{0,63}$/i', $option['callback']))
            {
                throw new \InvalidArgumentException('jsonp callback is invalid');
            }

            $this->callback = $option['callback'];
            unset($option['callback']);
        }

        parent::__construct($option);
    }

    public function convert($data)
    {
        $callback = $this->callback;
        $result = parent::convert($data);
        return "{$callback}({$result})";
    }
}
