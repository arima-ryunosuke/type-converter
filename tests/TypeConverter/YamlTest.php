<?php

namespace ryunosuke\Test\TypeConverter;
use ryunosuke\TypeConverter\Yaml;

class YamlTest extends \PHPUnit_Framework_TestCase
{
    function provideArray()
    {
        return array(
            array(
                array(
                    'key1' => 'value1',
                    'key2' => 'value2',
                ),
                '{"key1":"value1","key2":"value2"}',
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
                '{"holder":[{"key1":"value1","key2":"value2"},{"key1":"value3","key2":"value4"}]}',
            ),
        );
    }

    /**
     * @test
     */
    function mimetype()
    {
        $converter = new Yaml();
        $this->assertContains('yaml', $converter->getMimeType());
    }

    /**
     * @test
     * @dataProvider provideArray
     */
    function test($input, $output)
    {
        $converter = new Yaml();
        $this->assertEquals($output, $converter->convert($input));
        $this->assertEquals($input, $converter->deconvert($output));
        $this->assertEquals($input, $converter->deconvert($converter->convert($input)));
        $this->assertEquals($output, $converter->convert($converter->deconvert($output)));
    }
}
