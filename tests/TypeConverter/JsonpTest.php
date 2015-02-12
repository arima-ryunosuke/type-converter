<?php

namespace ryunosuke\Test\TypeConverter;
use ryunosuke\TypeConverter\Jsonp;

class JsonpTest extends \PHPUnit_Framework_TestCase
{
    function provideArray()
    {
        return array(
            array(
                array(
                    'key1' => 'value1',
                    'key2' => 'value2',
                ),
                'jquery1234567890({"key1":"value1","key2":"value2"})',
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
                'jquery1234567890({"holder":[{"key1":"value1","key2":"value2"},{"key1":"value3","key2":"value4"}]})',
            ),
        );
    }

    /**
     * @test
     */
    function mimetype()
    {
        $converter = new JsonP();
        $this->assertEquals('application/javascript', $converter->getMimeType());
    }

    /**
     * @test
     * @dataProvider provideArray
     */
    function test($input, $output)
    {
        $option = array(
            JSON_PRETTY_PRINT      => false,
            JSON_UNESCAPED_UNICODE => false,
            'callback'             => 'jquery1234567890',
        );
        $converter = new Jsonp($option);
        $this->assertEquals($output, $converter->convert($input));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    function invalid_callback1()
    {
        $option = array(
            'callback' => '1jquery1234567890',
        );
        $converter = new Jsonp($option);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    function invalid_callback2()
    {
        $option = array(
            'callback' => 'jquery123456789012345678901234567890123456789012345678901234567890',
        );
        $converter = new Jsonp($option);
    }
}
