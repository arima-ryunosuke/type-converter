<?php
namespace ryunosuke\Test\TypeConverter;

use ryunosuke\TypeConverter\Mediator;
use ryunosuke\TypeConverter\Json;
use ryunosuke\TypeConverter\Xml;

class MediatorTest extends \PHPUnit_Framework_TestCase
{

    function provideArray()
    {
        return array(
            array(
                'holder' => array(
                    'key1' => 'value1',
                    'key2' => 'value2'
                )
            )
        );
    }

    /**
     * @test
     */
    function test0()
    {
        $this->setExpectedException(get_class(new \InvalidArgumentException()));
        
        $converter = new Mediator(false, false);
    }

    /**
     * @test
     * @dataProvider provideArray
     */
    function test1($array)
    {
        $jop = array(
            JSON_PRETTY_PRINT => false
        );
        $xop = array(
            'rootNodeName' => 'hoge'
        );
        
        $json = new Json($jop);
        $xml = new Xml($xop);
        
        $jsondata = $json->convert($array);
        $xmldata = $xml->convert($array);
        
        $converter = new Mediator(array(
            'json' => $jop,
            'xml' => $xop
        ));
        $this->assertEquals($xmldata, $converter->convert($jsondata));
        $this->assertEquals($jsondata, $converter->deconvert($xmldata));
        $this->assertEquals($jsondata, $converter->deconvert($converter->convert($jsondata)));
        $this->assertEquals($xmldata, $converter->convert($converter->deconvert($xmldata)));
    }

    /**
     * @test
     * @dataProvider provideArray
     */
    function test2($array)
    {
        $jop = array(
            JSON_PRETTY_PRINT => false
        );
        $xop = array(
            'rootNodeName' => 'hoge'
        );
        
        $json = new Json($jop);
        $xml = new Xml($xop);
        
        $jsondata = $json->convert($array);
        $xmldata = $xml->convert($array);
        
        $converter = new Mediator($json, $xml);
        $this->assertEquals($xmldata, $converter->convert($jsondata));
        $this->assertEquals($jsondata, $converter->deconvert($xmldata));
        $this->assertEquals($jsondata, $converter->deconvert($converter->convert($jsondata)));
        $this->assertEquals($xmldata, $converter->convert($converter->deconvert($xmldata)));
    }

    /**
     * @test
     * @dataProvider provideArray
     */
    function test3($array)
    {
        $jop = array(
            JSON_PRETTY_PRINT => false
        );
        $xop = array(
            'rootNodeName' => 'hoge'
        );
        
        $json = new Json($jop);
        $xml = new Xml($xop);
        
        $jsondata = $json->convert($array);
        $xmldata = $xml->convert($array);
        
        $converter = new Mediator('json', 'xml', $jop, $xop);
        $this->assertEquals($xmldata, $converter->convert($jsondata));
        $this->assertEquals($jsondata, $converter->deconvert($xmldata));
        $this->assertEquals($jsondata, $converter->deconvert($converter->convert($jsondata)));
        $this->assertEquals($xmldata, $converter->convert($converter->deconvert($xmldata)));
    }

    /**
     * @test
     */
    function mimetype()
    {
        $xml = new Xml(array());
        $converter = new Mediator('json', 'xml');
        $this->assertEquals($xml->getMimeType(), $converter->getMimeType());
    }
}
