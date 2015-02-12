<?php

namespace ryunosuke\Test\TypeConverter;
use ryunosuke\TypeConverter\Json;

class JsonTest extends \PHPUnit_Framework_TestCase
{
    function provideArray()
    {
        return array(
            array(
                array(
                    'key1' => 'value1',
                    'key2' => 'value2',
                ),
                '{
    "key1": "value1",
    "key2": "value2"
}',
            ),
            array(
                array(
                    'holder' => array(
                        array(
                            'key1' => 'value1',
                            'key2' => 'value2',
                        ),
                        array(
                            'key1' => 'value3',
                            'key2' => 'value4',
                        )
                    ),
                ),
                '{
    "holder": [
        {
            "key1": "value1",
            "key2": "value2"
        },
        {
            "key1": "value3",
            "key2": "value4"
        }
    ]
}',
            ),
        );
    }

    /**
     * @test
     * @dataProvider provideArray
     */
    function test($input, $output)
    {
        $converter = new Json();
        $this->assertEquals($output, $converter->convert($input));
        $this->assertEquals($input, $converter->deconvert($output));
        $this->assertEquals($input, $converter->deconvert($converter->convert($input)));
        $this->assertEquals($output, $converter->convert($converter->deconvert($output)));
    }

    /**
     * @test
     */
    function option()
    {
        $option = array(
            JSON_PRETTY_PRINT      => false,
            JSON_UNESCAPED_UNICODE => false,
        );
        $converter = new Json($option);
        $input = array(
            'key1' => 'あいうえお',
            'key2' => 'かきくけこ',
        );
        $output = '{"key1":"\u3042\u3044\u3046\u3048\u304a","key2":"\u304b\u304d\u304f\u3051\u3053"}';
        $this->assertEquals($output, $converter->convert($input));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    function invalid()
    {
        $converter = new Json();
        $converter->deconvert("[[");
    }
}
