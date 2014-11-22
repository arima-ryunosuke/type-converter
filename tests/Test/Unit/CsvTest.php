<?php

namespace Test\Unit\TypeConverter;
use ryunosuke\TypeConverter\Csv;

class CsvTest extends \PHPUnit_Framework_TestCase
{
    function provideArray1()
    {
        return array(
            array(
                array(
                    array(
                        'row1col1',
                        'row1col2',
                    ),
                    array(
                        'row2col1',
                        'row2col2',
                    ),
                ),
                'row1col1,row1col2
row2col1,row2col2
',
            ),
        );
    }

    function provideArray2()
    {
        return array(
            array(
                array(
                    array(
                        'header1' => 'row1col1',
                        'header2' => 'row1col2',
                    ),
                    array(
                        'header1' => 'row2col1',
                        'header2' => 'row2col2',
                    ),
                ),
                'header1,header2
row1col1,row1col2
row2col1,row2col2
',
            ),
            array(
                array(
                    array(
                        'header1' => "row1\ncol1",
                        'header2' => ' row1col2 ',
                        'header3' => 'row1",col2',
                    ),
                ),
                'header1,header2,header3
"row1
col1"," row1col2 ","row1"",col2"
',
            ),
        );
    }

    /**
     * @test
     * @dataProvider provideArray1
     */
    function test1($input, $output)
    {
        $converter = new Csv(array(
            Csv::USE_FIRST_LINE => false
        ));
        $this->assertEquals($output, $converter->convert($input));
        $this->assertEquals($input, $converter->deconvert($output));
        $this->assertEquals($input, $converter->deconvert($converter->convert($input)));
        $this->assertEquals($output, $converter->convert($converter->deconvert($output)));
    }

    /**
     * @test
     * @dataProvider provideArray2
     */
    function test2($input, $output)
    {
        $converter = new Csv();
        $this->assertEquals($output, $converter->convert($input));
        $this->assertEquals($input, $converter->deconvert($output));
        $this->assertEquals($input, $converter->deconvert($converter->convert($input)));
        $this->assertEquals($output, $converter->convert($converter->deconvert($output)));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    function invalid1()
    {
        $converter = new Csv(array(
            Csv::USE_FIRST_LINE => true
        ));
        $converter->convert(array(array('a', 'b')));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    function invalid2()
    {
        $converter = new Csv(array(
            Csv::USE_FIRST_LINE => true
        ));
        $converter->deconvert("header1,header2
row1col1,row1col2,row1col3");
    }
}
