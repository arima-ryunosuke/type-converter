<?php
namespace ryunosuke\TypeConverter;

/**
 * phpのネイティブ配列とxml形式を相互に変換するクラス
 *
 * @author ryunosuke
 */
class Xml extends AbstractConverter
{

    protected $mimetype = 'text/xml';

    public function __construct($option = array())
    {
        $option = $option + array(
            'rootNodeName' => 'root',
            'formatOutput' => true,
            'preserveWhiteSpace' => false
        );
        
        parent::__construct($option);
    }

    public function convert($data)
    {
        $document = new \DOMDocument('1.0', 'utf-8');
        
        $option = $this->option;
        $rootname = $option['rootNodeName'];
        unset($option['rootNodeName']);
        
        foreach ($option as $name => $value) {
            $document->$name = $value;
        }
        
        $root = $document->createElement($rootname);
        $document->appendChild($root);
        
        $this->_compact("", $root, $data);
        
        return $document->saveXML();
    }

    private function _compact($name, \DOMElement $node, $array)
    {
        if (! is_array($array)) {
            throw new \RuntimeException('$array is not array');
        }
        
        /* @var $document \DOMDocument */
        $document = $node->ownerDocument;
        
        if (isset($array['@attributes'])) {
            $attributes = $array['@attributes'];
            unset($array['@attributes']);
            
            if (! is_array($attributes)) {
                throw new \RuntimeException("@attributes is not array");
            }
            foreach ($attributes as $k => $v) {
                $node->setAttribute($k, $v);
            }
        }
        
        $ishash = self::isHashArray($array);
        foreach ($array as $key => $value) {
            $name = $ishash ? $key : $name;
            if ($name === null) {
                $node->nodeValue = $value;
                continue;
            }
            
            if (! preg_match('/^[a-z][_0-9a-z]*$/i', $name)) {
                throw new \RuntimeException("$name is not alphanum");
            }
            
            if (is_array($value)) {
                if (count($value) === 0) {
                    $child = $document->createElement($name);
                    $child->appendChild($document->createTextNode(''));
                    $node->appendChild($child);
                } else if (self::isHashArray($value)) {
                    $child = $document->createElement($name);
                    $node->appendChild($child);
                    $this->_compact(null, $child, $value);
                } else {
                    $this->_compact($name, $node, $value);
                }
            } else {
                $child = $document->createElement($name);
                $child->appendChild($document->createTextNode($value));
                $node->appendChild($child);
            }
        }
    }

    public function deconvert($data)
    {
        //テキストノード(の親)の属性が死ぬので使えない
        //return json_decode(json_encode(simplexml_load_string($data)), true);
        $document = new \DOMDocument();
        $document->preserveWhiteSpace = false;
        $document->formatOutput = false;
        $result = $document->loadXML($data);
        if ($result === false) {
            throw new \RuntimeException('invalid xml format');
        }
        
        return $this->_extract($document->documentElement);
    }

    private function _extract(\DOMElement $node)
    {
        $attr2array = function ($attrnode)
        {
            $attributes = array();
            foreach ($attrnode as $attr) {
                $attributes[$attr->nodeName] = $attr->nodeValue;
            }
            return $attributes;
        };
        
        $result = array();
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_COMMENT_NODE) {
                continue;
            } else if ($child->nodeType === XML_TEXT_NODE || $child->nodeType === XML_CDATA_SECTION_NODE) {
                // コメントノードやCDATAが挟まっている場合を考慮
                $value = $child->nodeValue;
                if (is_string($result)) {
                    $value = $result . $value;
                }
                $result = $value;
                
                // テキストノードの場合は属性も見る
                if ($child->nodeType === XML_TEXT_NODE) {
                    if ($node->attributes->length) {
                        $result = array(
                            '@attributes' => $attr2array($node->attributes),
                            $result
                        );
                    }
                }
            } else {
                $value = $this->_extract($child);
                
                if ($child->attributes->length) {
                    $value = array(
                        '@attributes' => $attr2array($child->attributes)
                    ) + $value;
                }
                
                if (isset($result[$child->nodeName])) {
                    if (self::isHashArray($result[$child->nodeName])) {
                        $result[$child->nodeName] = array(
                            $result[$child->nodeName]
                        );
                    }
                    
                    $result[$child->nodeName][] = $value;
                } else {
                    if (! is_array($result)) {
                        throw new \RuntimeException('text node and element node are mixed');
                    }
                    if (is_array($value) && count($value) === 0) {
                        $value = "";
                    }
                    $result[$child->nodeName] = $value;
                }
            }
        }
        return $result;
    }
}
