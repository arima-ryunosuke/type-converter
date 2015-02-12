<?php

namespace ryunosuke\Test\TypeConverter;
use ryunosuke\TypeConverter\Php;

class PhpTest extends \PHPUnit_Framework_TestCase
{
    function provideArray()
    {
        return array(
            array(
                array(
                    'key1' => 'value1',
                    'key2' => 'value2',
                ),
                'a:2:{s:4:"key1";s:6:"value1";s:4:"key2";s:6:"value2";}',
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
                'a:1:{s:6:"holder";a:2:{i:0;a:2:{s:4:"key1";s:6:"value1";s:4:"key2";s:6:"value2";}i:1;a:2:{s:4:"key1";s:6:"value3";s:4:"key2";s:6:"value4";}}}',
            ),
        );
    }

    /**
     * @test
     * @dataProvider provideArray
     */
    function test($input, $output)
    {
        $converter = new Php();
        $this->assertEquals($output, $converter->convert($input));
        $this->assertEquals($input, $converter->deconvert($output));
        $this->assertEquals($input, $converter->deconvert($converter->convert($input)));
        $this->assertEquals($output, $converter->convert($converter->deconvert($output)));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    function invalid()
    {
        $converter = new Php();
        @$converter->deconvert("{{{");
    }

    /**
     * @test
     */
    function successFalse()
    {
        $converter = new Php();
        $this->assertEquals(false, $converter->deconvert("b:0;"));
    }
}
