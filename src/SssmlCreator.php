<?php

namespace CidiLabs\Polly;

use DOMDocument;

class SsmlCreator
{
    private $html_elements = array( 'html','body','p','pre','span','div','b','non-emphasis', 'i', 'font', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote','q');

    private $html_element_map_to_ssml = array(
        'html' => array('ssml_tag' => 'speak' ),
        'body' => array('ssml_tag' => 'amazon:auto-breaths' ),
        'p' => array('ssml_tag' => 'p' ),
        'pre' => array('ssml_tag' => 'p' ),
        'span' => array('ssml_tag' => 'p' ),
        'div' => array('ssml_tag' => 'p' ),
        'b' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate')),
        'i' => array('ssml_tag' => 'amazon:effect' , 'options' => array('name' => 'whispered' )),
        'font' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate')),
        'h1' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'strong')),
        'h2' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'strong')),
        'h3' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate')),
        'h4' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate')),
        'h5' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'reduced')),
        'h6' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'reduced')),
        'blockquote' => array('ssml_tag' => 'p' ),
        'q' => array('ssml_tag' => 'p' )
    );

    public function __construct(SsmlCreator $ssml = null)
    {

    }

    public function buildSsmlText($text)
    {
        $strippedHtml = $this->stripHTML($text);
        $html = new DOMDocument();
        $html->loadHTML($strippedHtml);

        // remove doctype
        $html->removeChild($html->doctype);


        foreach($this->html_elements as $element){
            $elements = $html->getElementsByTagName($element);
            if(!empty($elements)) {


                foreach($elements as $ele){
                    $nodeDiv = $this->changeTagName($ele,$this->html_element_map_to_ssml[$element]['ssml_tag']);
                    if (!empty($this->html_element_map_to_ssml[$element]['options'])) {
                        foreach ($this->html_element_map_to_ssml[$element]['options'] as $key => $attribute) {
                            $domAttribute = $nodeDiv->setAttribute($key, $attribute);
                        }
                    }
                }
            }
        }

        return  trim($html->saveHTML(), "\n\r\t\v\0");

    }

    public function stripHTML($html)
    {
        $allowedTags = "";
        foreach ($this->html_elements as $element) {
            $allowedTags .= "<" . $element . ">";
        }
        return strip_tags($html,$allowedTags);

    }



    function changeTagName( $node, $name ) {
        $childnodes = array();
        foreach ( $node->childNodes as $child ) {
            $childnodes[] = $child;
        }
        $newnode = $node->ownerDocument->createElement( $name );
        foreach ( $childnodes as $child ){
            $child2 = $node->ownerDocument->importNode( $child, true );
            $newnode->appendChild($child2);
        }
        if ( $node->hasAttributes() ) {
            foreach ( $node->attributes as $attr ) {
                $attrName = $attr->nodeName;
                $attrValue = $attr->nodeValue;
                $newnode->setAttribute($attrName, $attrValue);
            }
        }
        $node->parentNode->replaceChild( $newnode, $node );
        return $newnode;
    }



}