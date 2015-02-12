<?php
namespace ryunosuke\Test\TypeConverter;

use ryunosuke\TypeConverter\Xml;

class XmlTest extends \PHPUnit_Framework_TestCase
{

    function provideArray()
    {
        return array(
            array(
                array(
                    'holder' => array(
                        array(
                            'empty' => ""
                        ),
                        array(
                            'values' => array(
                                'key1' => 'value',
                                'key2' => 'value'
                            )
                        ),
                        array(
                            'values' => array(
                                '@attributes' => array(
                                    'attr1' => 'attrvalue1',
                                    'attr2' => 'attrvalue2'
                                ),
                                'key1' => 'value1',
                                'key2' => 'value2'
                            )
                        ),
                        array(
                            'values' => array(
                                'key1' => 'value3',
                                'key2' => array(
                                    '@attributes' => array(
                                        'attr3' => 'attrvalue3',
                                        'attr4' => 'attrvalue4'
                                    ),
                                    'value4'
                                )
                            )
                        )
                    )
                ),
                '<?xml version="1.0" encoding="utf-8"?>
<root>
  <holder>
    <empty></empty>
  </holder>
  <holder>
    <values>
      <key1>value</key1>
      <key2>value</key2>
    </values>
  </holder>
  <holder>
    <values attr1="attrvalue1" attr2="attrvalue2">
      <key1>value1</key1>
      <key2>value2</key2>
    </values>
  </holder>
  <holder>
    <values>
      <key1>value3</key1>
      <key2 attr3="attrvalue3" attr4="attrvalue4">value4</key2>
    </values>
  </holder>
</root>
'
            )
        );
    }

    /**
     * @test
     * @dataProvider provideArray
     */
    function test($input, $output)
    {
        $converter = new Xml();
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
            'rootNodeName' => 'hogehoge',
            'formatOutput' => false,
            'preserveWhiteSpace' => true
        );
        $converter = new Xml($option);
        $input = array(
            'node_name' => 'node_value'
        );
        $output = '<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<hogehoge><node_name>node_value</node_name></hogehoge>' . "\n";
        $this->assertEquals($output, $converter->convert($input));
    }

    /**
     * @test
     */
    function cdata()
    {
        $converter = new Xml();
        $input = '<?xml version="1.0" encoding="utf-8"?><root><element>te<![CDATA[<?xml version="1.0" ?><node></node>]]>xt</element></root>';
        $output = array(
            'element' => 'te<?xml version="1.0" ?><node></node>xt'
        );
        $this->assertEquals($output, $converter->deconvert($input));
    }

    /**
     * @test
     */
    function emptynode()
    {
        $converter = new Xml(array(
            'rootNodeName' => 'root',
            'formatOutput' => false,
            'preserveWhiteSpace' => true
        ));
        $input1 = array(
            'element' => array()
        );
        $input2 = array(
            'element' => ''
        );
        $output = '<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<root><element></element></root>' . "\n";
        $this->assertEquals($output, $converter->convert($input1));
        $this->assertEquals($output, $converter->convert($input2));
    }

    /**
     * @test
     */
    function ignorecomment()
    {
        $converter = new Xml();
        $input = '<?xml version="1.0" encoding="utf-8"?><root><!-- コメント --><element>ho<!-- コメント -->ge</element></root>';
        $output = array(
            'element' => 'hoge'
        );
        $this->assertEquals($output, $converter->deconvert($input));
    }

    /**
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessage $array is not array
     */
    function notarray()
    {
        $converter = new Xml();
        $converter->convert('12345');
    }

    /**
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessage is not alphanum
     */
    function flatarray()
    {
        $converter = new Xml();
        $converter->convert(range(1, 5));
    }

    /**
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessage @hoge is not alphanum
     */
    function invalid_nodename()
    {
        $converter = new Xml();
        $converter->convert(array(
            '@hoge' => 'value'
        ));
    }

    /**
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessage text node and element node are mixed
     */
    function mixed_textelement()
    {
        $converter = new Xml();
        $input = '<?xml version="1.0" encoding="utf-8"?><root>text<element></element></root>';
        $converter->deconvert($input);
    }

    /**
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessage @attributes is not array
     */
    function notarray_attribute()
    {
        $converter = new Xml();
        $input = array(
            'node' => array(
                '@attributes' => 'attr',
                'value'
            )
        );
        $converter->convert($input);
    }

    /**
     * @test
     * @expectedException RuntimeException
     * @expectedExceptionMessage invalid xml format
     */
    function invalid_xml()
    {
        $converter = new Xml();
        @$converter->deconvert('invalid');
    }
}
