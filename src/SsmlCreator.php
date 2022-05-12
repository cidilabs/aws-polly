<?php

namespace CidiLabs\Polly;

use DOMDocument;

class SsmlCreator
{

    private $ssml;

    private $html_elements = array( 'html','body','p','pre','span','div','b','non-emphasis', 'i', 'font', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote','q','table','th','tr','td','ol','ul','li', 'br' , 'strong','b');

    private $html_element_map_to_ssml = array(
        'html' => array('ssml_tag' => 'speak'),
        'body' => array('ssml_tag' => 'amazon:auto-breaths'),
        // 'p' => array('ssml_tag' => 'p' ),
        'pre' => array('ssml_tag' => 'p' ),
        'span' => array('ssml_tag' => 'p' ),
        'div' => array('ssml_tag' => 'p'),
        'b' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate')),
        'i' => array('ssml_tag' => 'amazon:effect' , 'options' => array('name' => 'whispered')),
        'font' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate')),
        'h1' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'strong')),
        'h2' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'strong')),
        'h3' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate')),
        'h4' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate')),
        'h5' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'reduced')),
        'h6' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'reduced')),
        'blockquote' => array('ssml_tag' => 'p'),
        'q' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate')),
        'table' => array('ssml_tag' => 'p'),
        'th' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'strong')),
        'tr' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate')),
        'td' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate')),
        'ol' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate')),
        'ul' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate')),
        'li' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate')),
        'br' => array('ssml_tag' => 'break', 'options' => array( 'strength' => 'medium', 'time' => '3s')),
        'strong' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'strong')),

    );

    private $tagsToDelete = array(
        'html' => ['deleteParent' => [''], 'deleteSelf' => ['']],
        'body' => ['deleteParent' => [''], 'deleteSelf' => ['']],
        'pre' => ['deleteParent' => ['emphasis'], 'deleteSelf' => ['p','s']],
        'span' => ['deleteParent' => ['emphasis'], 'deleteSelf' => ['p','s']],
        'div' => ['deleteParent' => ['emphasis'], 'deleteSelf' => ['p','s']],
        'b' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'i' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'font' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'h1' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'h2' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'h3' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'h4' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'h5' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'h6' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'blockquote' => ['deleteParent' => ['emphasis'], 'deleteSelf' => ['p','s']],
        'q' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'table' => ['deleteParent' => ['emphasis'], 'deleteSelf' => ['p','s']],
        'th' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'tr' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'td' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'ol' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'ul' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'li' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'br' => ['deleteParent' => [''], 'deleteSelf' => ['']],
        'break' => ['deleteParent' => [''], 'deleteSelf' => ['']],
        'strong' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'p' =>  ['deleteParent' => ['emphasis'], 'deleteSelf' => ['p','s']],
        'emphasis' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],
        'amazon:effect' => ['deleteParent' => [''], 'deleteSelf' => ['emphasis','amazon:effect']],

    );

    private $allowedAttributes = ['level','name','strength','time','xml:lang','alphabet','ph','volume','rate','pitch','amazon:max-duration','interpret-as','alias','role','duration','frequency','phonation','vocal-tract-length'];

    public function __construct(SsmlCreator $ssml = null)
    {

    }

    public function buildSsmlText($text)
    {
        $strippedHtml = $this->stripHTML($text);
        $html = new DOMDocument();
        libxml_use_internal_errors(true);
        $html->loadHTML($strippedHtml);

        // remove doctype
        $html->removeChild($html->doctype);


        $html = $this->convertTagsToSSML($html);

        $html = $this->cleanUpSSML($html);

        $output = $html->saveHTML();

        $filteredOutput = preg_replace('/&.*?;/im', '', $output);

        return trim($filteredOutput, "\n\r\t\v\0");
    }

    private function convertTagsToSSML($html){
        foreach ($this->html_elements as $element) {
            if ($element == 'p') {
                $elements = $html->getElementsByTagName($element);
                foreach ($elements as $ele) {
                    $this->removeTagAttributes($ele);
                }
            } else {
                $elements = $html->getElementsByTagName($element);
                while ($elements->length > 0) {

                    foreach ($elements as $ele) {
                        $this->removeTagAttributes($ele);
                        $nodeDiv = $this->changeTagName($ele, $this->html_element_map_to_ssml[$element]['ssml_tag']);

                        if (!empty($this->html_element_map_to_ssml[$element]['options'])) {
                            foreach ($this->html_element_map_to_ssml[$element]['options'] as $key => $attribute) {
                                $domAttribute = $nodeDiv->setAttribute($key, $attribute);
                            }
                        }
                    }
                    $elements = $html->getElementsByTagName($element);

                }
            }

        }

        return $html;
    }

    private function cleanUpSSML($html) {
        foreach ($this->html_elements as $element) {

            $elements = $html->getElementsByTagName($element);

            foreach ($elements as $ele) {
                if(!empty($ele->tagName) && $ele->childElementCount > 0 && $ele->hasChildNodes()) {
                    $this->cleanChildren($ele);
                }

                if(!empty($ele->parentNode->tagName) && in_array($ele->parentNode->tagName, $this->tagsToDelete[$ele->tagName]['deleteSelf']) ){
                    $ele->parentNode->insertBefore($ele->lastChild, $ele->nextSibling);
                    $ele->parentNode->removeChild($ele);
                }

                if(!empty($ele->parentNode->tagName) && in_array($ele->parentNode->tagName, $this->tagsToDelete[$ele->tagName]['deleteParent'])){
                    $ele->parentNode->parentNode->insertBefore($ele->parentNode->lastChild, $ele->parentNode->nextSibling);
                    $ele->parentNode->parentNode->removeChild($ele->parentNode);
                }

            }
        }

        return $html;
    }

    private function cleanChildren($node){
        if($node->hasChildNodes()){
            foreach($node->childNodes as $childNode) {
                if(!empty($childNode->tagName) && $childNode->hasChildNodes() ){
                    foreach($childNode->childNodes as $child) {
                        $this->cleanChildren($child);
                    }
                }

                if(!empty($childNode->tagName) && !empty($childNode->parentNode->tagName) &&  in_array($childNode->parentNode->tagName, $this->tagsToDelete[$childNode->tagName]['deleteSelf']) && !is_null($childNode->parentNode) && !is_null($childNode->lastChild) && !is_null($childNode->nextSibling)){
                        $childNode->parentNode->insertBefore($childNode->lastChild, $childNode->nextSibling);
                        $childNode->parentNode->removeChild($childNode);
                }

                if(!empty($childNode->tagName) && !empty($childNode->parentNode->tagName) && in_array($childNode->parentNode->tagName, $this->tagsToDelete[$childNode->tagName]['deleteParent']) && !is_null($childNode->parentNode->parentNode) && !is_null($childNode->parentNode->lastChild) && !is_null($childNode->parentNode->nextSibling)){
                        $childNode->parentNode->parentNode->insertBefore($childNode->parentNode->lastChild, $childNode->parentNode->nextSibling);
                        $childNode->parentNode->parentNode->removeChild($childNode->parentNode);
                }

            }
        }
    }


    private function removeTagAttributes($element){
        foreach ($element->attributes as $attr) {
            if (!in_array($attr->nodeName, $this->allowedAttributes)) {
                $element->removeAttribute($attr->nodeName);
            }
        }
    }

    public function stripHTML($html)
    {
        $allowedTags = "";
        foreach ($this->html_elements as $element) {
            $allowedTags .= "<" . $element . ">";
        }
        return strip_tags($html,$allowedTags);

    }


    private function changeTagName( $node, $name ) {
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