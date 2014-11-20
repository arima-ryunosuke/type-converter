<?php

namespace Test\Unit\TypeConverter;
use ryunosuke\TypeConverter\AbstractConverter;

class TestType extends \ryunosuke\TypeConverter\Json
{
}

class AbstractConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException LogicException
     */
    function getMimeType()
    {
        $converter = new \Concrete();
        $converter->getMimeType();
    }

    /**
     * @test
     */
    function addNamespace()
    {
        AbstractConverter::addNamespace(__NAMESPACE__);
        $test = AbstractConverter::factory('TestType', array());
        $this->assertInstanceOf(get_class(new TestType()), $test);
    }

    /**
     * @test
     */
    function factory()
    {
        $json = AbstractConverter::factory('json', array());
        $this->assertInstanceOf(get_class(new \ryunosuke\TypeConverter\Json()), $json);

        $this->setExpectedException(get_class(new \InvalidArgumentException()));
        AbstractConverter::factory('undefined', array());
    }

    /**
     * @test
     */
    function isHasArray_true()
    {
        $array = array(
            '0'   => 'hoge',
            '1.0' => 'fuga',
            '2'   => 'piyo',
        );
        $this->assertTrue(AbstractConverter::isHashArray($array));

        $array = array(
            1 => 'fuga',
            2 => 'piyo',
        );
        $this->assertTrue(AbstractConverter::isHashArray($array));
    }

    /**
     * @test
     */
    function isHasArray_false()
    {
        $array = array();
        $this->assertFalse(AbstractConverter::isHashArray($array));

        $array = array(
            '0' => 'hoge',
            '1' => 'fuga',
            '2' => 'piyo',
        );
        $this->assertFalse(AbstractConverter::isHashArray($array));

        $array[] = 'hage';
        $this->assertFalse(AbstractConverter::isHashArray($array));

        $array = array(
            0 => 'hoge',
            1 => 'fuga',
            2 => 'piyo',
        );
        $this->assertFalse(AbstractConverter::isHashArray($array));

        $array[] = 'hage';
        $this->assertFalse(AbstractConverter::isHashArray($array));

        $array = array(
            'hoge',
            'fuga',
            'piyo',
        );
        $this->assertFalse(AbstractConverter::isHashArray($array));

        $array = array(
            'hoge',
            1 => 'fuga',
            'piyo',
        );
        $this->assertFalse(AbstractConverter::isHashArray($array));
    }

    /**
     * @test
     */
    function toPascalCase_string()
    {
        $this->assertEquals('ABC', AbstractConverter::toPascalCase('a_b_c'));
        $this->assertEquals('AbcDef', AbstractConverter::toPascalCase('abc_def'));
        $this->assertEquals('Abc', AbstractConverter::toPascalCase('_abc'));
    }

    /**
     * @test
     */
    function toPascalCase_array()
    {
        $expected = array(
            'KeyKey1' => array(
                'KeyKey2' => array(
                    'value1',
                    'value2',
                )
            )
        );
        $base = array(
            'key_key1' => array(
                'key_key2' => array(
                    'value1',
                    'value2',
                )
            )
        );
        $this->assertEquals($expected, AbstractConverter::toPascalCase($base));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must be string or array
     */
    function toPascalCase_invalid()
    {
        AbstractConverter::toPascalCase(1);
    }

    /**
     * @test
     */
    function toSnakeCase_string()
    {
        $this->assertEquals('a_b_c', AbstractConverter::toSnakeCase('ABC'));
        $this->assertEquals('abc_def', AbstractConverter::toSnakeCase('AbcDef'));
        $this->assertEquals('abc', AbstractConverter::toSnakeCase('Abc'));
    }

    /**
     * @test
     */
    function toSnakeCase_array()
    {
        $expected = array(
            'key_key1' => array(
                'key_key2' => array(
                    'value1',
                    'value2',
                )
            )
        );
        $base = array(
            'KeyKey1' => array(
                'KeyKey2' => array(
                    'value1',
                    'value2',
                )
            )
        );
        $this->assertEquals($expected, AbstractConverter::toSnakeCase($base));
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must be string or array
     */
    function toSnakeCase_invalid()
    {
        AbstractConverter::toSnakeCase(1);
    }
}
