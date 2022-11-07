<?php

namespace CidiLabs\Polly;

use DOMDocument;

class SsmlCreator
{


    private $html_elements = array( 'html','body','p','pre','span','div','b','non-emphasis', 'i', 'font', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote','q','table','th','tr','td','ol','ul','li', 'br' , 'strong','b');

    private $html_element_map_to_ssml = array(
        'html' => array('ssml_tag' => 'speak','alternateTag' => ''),
        'body' => array('ssml_tag' => 'amazon:auto-breaths','alternateTag' => ''),
        'p' => array('ssml_tag' => 'p' ,'alternateTag' => 'break','alternateOptions' => array( 'strength' => 'x-strong', 'time' => '250ms')),
        'pre' => array('ssml_tag' => 'p' ,'alternateTag' => 'break','alternateOptions' => array( 'strength' => 'x-strong', 'time' => '250ms')),
        'span' => array('ssml_tag' => 'p' ,'alternateTag' => 'break','alternateOptions' => array( 'strength' => 'x-strong', 'time' => '250ms')),
        'div' => array('ssml_tag' => 'p','alternateTag' => 'break','alternateOptions' => array( 'strength' => 'x-strong', 'time' => '250ms')),
        'b' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate'),'alternateTag' => ''),
        'i' => array('ssml_tag' => 'amazon:effect' , 'options' => array('name' => 'whispered'),'alternateTag' => ''),
        'font' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate'),'alternateTag' => ''),
        'h1' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'strong'),'alternateTag' => ''),
        'h2' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'strong'),'alternateTag' => ''),
        'h3' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate'),'alternateTag' => ''),
        'h4' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate'),'alternateTag' => ''),
        'h5' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'reduced'),'alternateTag' => ''),
        'h6' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'reduced'),'alternateTag' => ''),
        'blockquote' => array('ssml_tag' => 'p','alternateTag' => 'break','alternateOptions' => array( 'strength' => 'medium', 'time' => '500ms')),
        'q' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate'),'alternateTag' => ''),
        'table' => array('ssml_tag' => 'p','alternateTag' => 'break','alternateOptions' => array( 'strength' => 'medium', 'time' => '1s')),
        'th' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'strong'),'alternateTag' => ''),
        'tr' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate'),'alternateTag' => 'break','alternateOptions' => array( 'strength' => 'medium', 'time' => '250ms')),
        'td' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate'),'alternateTag' => 'break','alternateOptions' => array( 'strength' => 'medium', 'time' => '250ms')),
        'ol' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate'),'alternateTag' => ''),
        'ul' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate'),'alternateTag' => ''),
        'li' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate'),'alternateTag' => ''),
        'br' => array('ssml_tag' => 'break', 'options' => array( 'strength' => 'medium', 'time' => '3s'),'alternateTag' => ''),
        'strong' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'strong'),'alternateTag' => ''),
        'emphasis' => array('ssml_tag' => 'emphasis' , 'options' => array( 'level' => 'moderate'),'alternateTag' => ''),
        'amazon:effect' => array('ssml_tag' => 'amazon:effect' , 'options' => array('name' => 'whispered'),'alternateTag' => ''),

    );

    private $allowedAttributes = ['level','name','strength','time','xml:lang','alphabet','ph','volume','rate','pitch','amazon:max-duration','interpret-as','alias','role','duration','frequency','phonation','vocal-tract-length'];

    private $parentNode = ['element' => ''];

    private $data = [
        'ssml' => '',
        'errors' => ''
    ];

    public function buildSsmlText($text)
    {
        $preCleanedHtml = $this->cleanUpBeforeDomDoc($text);
        $strippedHtml = $this->stripHTML($preCleanedHtml);
        $html = new DOMDocument();
        libxml_use_internal_errors(true);
        $html->loadHTML($strippedHtml);
        $data['errors'] = libxml_get_errors();

        // remove doctype
        $html->removeChild($html->doctype);

        $html = $this->setupBaseSSML($html);

        $html = $this->convertToSSML($html);

        if(is_null($html)){
            return null;
        }

        $output = $html->saveHTML();

        $filteredOutput = preg_replace('/&.*?;/im', '', $output);

        $data['ssml'] = is_null($html) ? null : trim($filteredOutput, "\n\r\t\v\0");
        
        return $data;
    }

    private function setupBaseSSML($html){
        foreach (['html','body'] as $element) {
            $elements = $html->getElementsByTagName($element);
            while ($elements->length > 0) {

                foreach ($elements as $ele) {
                    $this->removeTagAttributes($ele);
                    $nodeDiv = $this->changeTagName($ele, $this->html_element_map_to_ssml[$element]['ssml_tag']);
                }
                $elements = $html->getElementsByTagName($element);
            }
        }

        return $html;

    }

    private function convertToSSML($html){
        $rootElementList = $html->getElementsByTagName('amazon:auto-breaths');

        $rootElement = $rootElementList->item(0);

        if(is_null($rootElement) && !$rootElement->hasChildNodes()){
            return null;
        }

        //create childNode because childNodes will be changes and become un-reliable.
        $childNodes = array();
        foreach ( $rootElement->childNodes as $child ) {
            $childNodes[] = $child;
        }

        foreach($childNodes as $childNode) {
            $newNode = null;
            if (!$childNode instanceof DOMText && !empty($childNode->tagName) && !is_null($childNode)) {
                $childNode = $this->removeTagAttributes($childNode);
                $newNode = $this->changeTagName($childNode, $this->html_element_map_to_ssml[$childNode->tagName]['ssml_tag']);
                $this->parentNode['element'] = $this->html_element_map_to_ssml[$childNode->tagName]['ssml_tag'];

                if (!empty($this->html_element_map_to_ssml[$childNode->tagName]['options'])) {
                    foreach ($this->html_element_map_to_ssml[$childNode->tagName]['options'] as $key => $attribute) {
                        $domAttribute = $newNode->setAttribute($key, $attribute);
                    }
                }
            }
            if(!is_null($newNode) && $newNode->hasChildNodes() && $newNode->childElementCount > 0){
                $this->cleanChildNodes($newNode->childNodes);
            }

            $this->parentNode['element'] = '';

        }

        return $html;

    }

    private function cleanChildNodes($childNodes){

        //create childNode because childNodes will be changes and become un-reliable.
        $childNodesArray = array();
        foreach ( $childNodes as $child ) {
            $childNodesArray[] = $child;
        }

        foreach($childNodesArray as $childNode) {
            $newNode = null;
            if(!is_null($childNode) && !$childNode instanceof DOMText && !empty($childNode->tagName) && isset($this->parentNode['element']) && !empty($this->html_element_map_to_ssml[$childNode->tagName]['alternateTag'])  ){
                $childNode = $this->removeTagAttributes($childNode);
                if($this->html_element_map_to_ssml[$childNode->tagName]['alternateTag'] == 'break'){

                    $firstBreak = $childNode->parentNode->ownerDocument->createElement($this->html_element_map_to_ssml[$childNode->tagName]['alternateTag']);
                    $secondBreak = $childNode->parentNode->ownerDocument->createElement($this->html_element_map_to_ssml[$childNode->tagName]['alternateTag']);
                    if (!empty($this->html_element_map_to_ssml[$childNode->tagName]['alternateOptions'])) {
                        foreach ($this->html_element_map_to_ssml[$childNode->tagName]['alternateOptions'] as $key => $attribute) {
                            $firstBreak->setAttribute($key, $attribute);
                            $secondBreak->setAttribute($key, $attribute);

                        }
                    }

                    $newNode = $this->changeTagName($childNode, 'emphasis');
                    foreach (['level' => 'reduced'] as $key => $attribute) {
                        $newNode->setAttribute($key, $attribute);
                    }

                    $newNode->parentNode->insertBefore($firstBreak, $newNode);
                    $newNode->parentNode->insertBefore($secondBreak, $newNode->nextSibling);
                    //$childNode->parentNode->removeChild($childNode);

                    if(!is_null($newNode) && !$newNode instanceof DOMText && !empty($newNode->tagName) && $newNode->hasChildNodes() ){
                        $this->cleanChildNodes($newNode->childNodes);
                    }

                }else{
                    $childNode = $this->removeTagAttributes($childNode);
                    $newNode = $this->changeTagName($childNode, $this->html_element_map_to_ssml[$childNode->tagName]['alternateTag']);
                    if (!empty($this->html_element_map_to_ssml[$childNode->tagName]['options'])) {
                        foreach ($this->html_element_map_to_ssml[$childNode->tagName]['options'] as $key => $attribute) {
                            $domAttribute = $newNode->setAttribute($key, $attribute);
                        }
                    }

                    if(!is_null($newNode) && !$newNode instanceof DOMText && !empty($newNode->tagName) && $newNode->hasChildNodes()){
                        $this->cleanChildNodes($newNode->childNodes);
                    }
                }


            }elseif(!$childNode instanceof DOMText && !empty($childNode->tagName) && !is_null($childNode)){
                $childNode = $this->removeTagAttributes($childNode);
                $newNode = $this->changeTagName($childNode, $this->html_element_map_to_ssml[$childNode->tagName]['ssml_tag']);

                if (!empty($this->html_element_map_to_ssml[$childNode->tagName]['options'])) {
                    foreach ($this->html_element_map_to_ssml[$childNode->tagName]['options'] as $key => $attribute) {
                        $domAttribute = $newNode->setAttribute($key, $attribute);
                    }
                }

                if(!is_null($newNode) && !$newNode instanceof DOMText && !empty($newNode->tagName) && $newNode->hasChildNodes()){
                    $this->cleanChildNodes($newNode->childNodes);
                }

            }

        }

    }



    private function removeTagAttributes($node){
        $childNodesAttArray = array();
        foreach ( $node->attributes as $attr ) {
            $childNodesAttArray[] = $attr;
        }
        
        foreach ($childNodesAttArray as $attr) {
            if (!in_array($attr->nodeName, $this->allowedAttributes)) {
                // $node->removeAttributeNode($attr);
                $node->removeAttribute($attr->nodeName);
            }
        }

        return $node;
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

    private function cleanUpBeforeDomDoc($html) {

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);

        // remove doctype
        $dom->removeChild($dom->doctype);

        $head = $dom->getElementsByTagName('head');

        if($head = $dom->getElementsByTagName('head')->item(0)) {

            $head->parentNode->removeChild($head);
        }

        return $dom->saveHTML();
    }

}