<?php

namespace ryunosuke\TypeConverter;

/**
 * 各タイプの相互変換クラス
 *
 * @author ryunosuke
 */
class Mediator extends AbstractConverter
{
    private $_from;
    private $_to;

    public function __construct($from, $to = null, $from_option = array(), $to_option = array())
    {
        if (func_num_args() === 1 && is_array($from))
        {
            $mixed = $from;
            reset($mixed);

            $arg = each($mixed);
            $from = self::factory($arg[0], $arg[1]);
            $arg = each($mixed);
            $to = self::factory($arg[0], $arg[1]);
        }
        if (is_string($from))
        {
            $from = self::factory($from, $from_option);
        }
        if (is_string($to))
        {
            $to = self::factory($to, $to_option);
        }

        if (!($from instanceof AbstractConverter) || !($to instanceof AbstractConverter))
        {
            throw new \InvalidArgumentException('$from or $to are not AbstractConverter instance');
        }

        $this->_from = $from;
        $this->_to = $to;

        parent::__construct(array());
    }

    public function getMimeType()
    {
        return $this->_to->getMimeType();
    }

    public function convert($data)
    {
        return $this->_to->convert($this->_from->deconvert($data));
    }

    public function deconvert($data)
    {
        return $this->_from->convert($this->_to->deconvert($data));
    }
}
