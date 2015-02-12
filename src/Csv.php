<?php

namespace ryunosuke\TypeConverter;

/**
 * phpのネイティブ配列とCSV形式を相互に変換するクラス
 *
 * @author ryunosuke
 */
class Csv extends AbstractConverter
{
    const USE_FIRST_LINE = 1;
    const DELIMITER = 11;
    const ENCLOSURE = 12;
    const ESCAPE = 13;

    protected $mimetype = 'text/csv';

    public function __construct($option = array())
    {
        $option = $option + array(
            self::USE_FIRST_LINE => true,
            self::DELIMITER      => ',',
            self::ENCLOSURE      => '"',
            self::ESCAPE         => "\\",
        );

        parent::__construct($option);
    }

    private function _getOption()
    {
        return array(
            $this->option[self::USE_FIRST_LINE],
            $this->option[self::DELIMITER],
            $this->option[self::ENCLOSURE],
            $this->option[self::ESCAPE],
        );
    }

    public function convert($data)
    {
        list($USE_FIRST_LINE, $DELIMITER, $ENCLOSURE) = $this->_getOption();

        $handle = tmpfile();
        foreach ($data as $row)
        {
            if ($USE_FIRST_LINE)
            {
                if (!isset($first) && $first = true)
                {
                    if (!self::isHashArray($row))
                    {
                        throw new \InvalidArgumentException('first line is not hash array');
                    }

                    $header = array_keys($row);
                    fputcsv($handle, $header, $DELIMITER, $ENCLOSURE);
                }

                $tmp = array();
                foreach ($header as $n => $head)
                {
                    if (! isset($row[$head]))
                    {
                        throw new \InvalidArgumentException("undefined header '$head'");
                    }
                    $tmp[$head] = $row[$head];
                }
                $row = $tmp;
            }

            fputcsv($handle, $row, $DELIMITER, $ENCLOSURE);
        }
        rewind($handle);
        $result = stream_get_contents($handle);
        fclose($handle);

        return $result;
    }

    public function deconvert($data)
    {
        list($USE_FIRST_LINE, $DELIMITER, $ENCLOSURE, $ESCAPE) = $this->_getOption();

        $header = array();
        $result = array();

        foreach (str_getcsv($data, "\n", $ENCLOSURE, $ESCAPE) as $row)
        {
            $fields = str_getcsv($row, $DELIMITER, $ENCLOSURE, $ESCAPE);

            if ($USE_FIRST_LINE)
            {
                if (!isset($first) && $first = true)
                {
                    $header = $fields;
                    continue;
                }

                $tmp = array();
                foreach ($fields as $n => $field)
                {
                    if (!isset($header[$n]))
                    {
                        throw new \InvalidArgumentException("undefined header number '$n'");
                    }
                    $tmp[$header[$n]] = $field;
                }
                $fields = $tmp;
            }

            $result[] = $fields;
        }

        return $result;
    }
}
