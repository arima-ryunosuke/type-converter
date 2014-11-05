<?php

namespace ryunosuke\TypeConverter;

/**
 * phpのネイティブ配列と何らかの形式を相互に変換する抽象クラス
 *
 * @author ryunosuke
 */
abstract class AbstractConverter
{
    /**
     * 変換オプション
     *
     * このオプションの使い方は各々のクラスで異なる
     *
     * @var mixed
     */
    protected $option = null;

    /**
     * 自身のmimetype
     *
     * @var string
     */
    protected $mimetype = null;

    /**
     * 名前空間を返す。
     *
     * 動的に生成することが多いと思われるので作成
     * new AbstractConverter::getNamespace() . '\\' . ucfirst($string);
     * のようにして作成できる
     *
     * @return string 自身の名前空間
     */
    static public function getNamespace()
    {
        return __NAMESPACE__;
    }

    /**
     * 配列が連想配列なら true を返す
     *
     * @param array $array
     * @return boolean
     */
    static public function isHashArray(array $array)
    {
        $i = 0;
        foreach ($array as $k => $dummy)
        {
            if ($k !== $i++)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * 文字列を PascalCase にして返す。配列ならキーを変換して返す
     *
     * @param string|array $value
     * @param string $separator
     * @throws \InvalidArgumentException
     * @return string|array
     */
    static public function toPascalCase($value, $separator = '_')
    {
        if (is_array($value))
        {
            $result = array();
            foreach ($value as $k => $v)
            {
                $key = is_string($k) ? self::toPascalCase($k, $separator) : $k;
                $val = is_array($v) ? self::toPascalCase($v, $separator) : $v;
                $result[$key] = $val;
            }
            return $result;
        }
        else if (is_string($value))
        {
            $strings = explode($separator, trim($value, $separator));
            array_walk($strings, function (&$v, $k)
            {
                $v = $k === 0 ? $v : ucfirst($v);
            });
            return ucfirst(implode('', $strings));
        }
        else
        {
            throw new \InvalidArgumentException('$value is must be string or array');
        }
    }

    /**
     * 文字列を snake_case にして返す。配列ならキーを変換して返す
     *
     * @param string|array $value
     * @param string $separator
     * @throws \InvalidArgumentException
     * @return string|array
     */
    static public function toSnakeCase($value, $separator = '_')
    {
        if (is_array($value))
        {
            $result = array();
            foreach ($value as $k => $v)
            {
                $key = is_string($k) ? self::toSnakeCase($k, $separator) : $k;
                $val = is_array($v) ? self::toSnakeCase($v, $separator) : $v;
                $result[$key] = $val;
            }
            return $result;
        }
        else if (is_string($value))
        {
            $value = preg_replace_callback('/(?<=.)[A-Z]/us', function ($matches) use ($separator)
            {
                return $separator . strtolower($matches[0]);
            }, $value);
            return lcfirst($value);
        }
        else
        {
            throw new \InvalidArgumentException('$value is must be string or array');
        }
    }

    /**
     * コンストラクタ。
     *
     * 変換オプションを渡す
     *
     * @param mixed $option 変換オプション
     */
    public function __construct($option = null)
    {
        $this->option = $option;
    }

    /**
     * 型に基づいたmimetypeを返す
     *
     * @throws \LogicException
     */
    public function getMimeType()
    {
        if ($this->mimetype === null)
        {
            throw new \LogicException('mimetype is not set');
        }

        return $this->mimetype;
    }

    /**
     * 変換メソッド。子クラスで定義する
     *
     * @param array $data 変換するネイティブ配列
     * @return string 変換された文字列
     */
    abstract function convert($data);

    /**
     * 逆変換メソッド。子クラスで定義する
     *
     * @param string $data 逆変換する配列
     * @return array ネイティブ配列
     */
    abstract function deconvert($data);
}
