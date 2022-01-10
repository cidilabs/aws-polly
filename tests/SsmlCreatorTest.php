<?php

use CidiLabs\Polly\SsmlCreator;

class SsmlCreatorTest extends AwsPollyTestCase
{

    public function testValidHtmlToSsml()
    {
        $html = $this->getValidHtml();

        $expectedSsml = $this->getValidSsml();
 
        $ssmlCreator = new SsmlCreator();
        $ssml = $ssmlCreator->buildSsmlText($html);
        $this->assertNotEmpty($ssml, "SSML not empty.");
        $this->assertEquals($ssml, $expectedSsml);
    }

    public function testInvalidHtmlToSsml()
    {
        $html = $this->getInvalidHtml();

        $expectedSsml = $this->getInvalidSsml();
 
        $ssmlCreator = new SsmlCreator();
        $ssml = $ssmlCreator->buildSsmlText($html);
        $this->assertNotEmpty($ssml, "SSML not empty.");
        $this->assertEquals($ssml, $expectedSsml);
    }

    public function testHtmlUlToSsml()
    {
        $html = $this->getValidUlHtml();

        $expectedSsml = $this->getValidUlSsml();
 
        $ssmlCreator = new SsmlCreator();
        $ssml = $ssmlCreator->buildSsmlText($html);
        $this->assertNotEmpty($ssml, "SSML not empty.");
        $this->assertEquals($ssml, $expectedSsml);
    }

    public function testHtmlOlToSsml()
    {
        $html = $this->getValidOlHtml();

        $expectedSsml = $this->getValidOlSsml();
 
        $ssmlCreator = new SsmlCreator();
        $ssml = $ssmlCreator->buildSsmlText($html);
        $this->assertNotEmpty($ssml, "SSML not empty.");
        $this->assertEquals($ssml, $expectedSsml);
    }

    public function testHtmlTableToSsml()
    {
        $html = $this->getValidTableHtml();

        $expectedSsml = $this->getValidTableSsml();
 
        $ssmlCreator = new SsmlCreator();
        $ssml = $ssmlCreator->buildSsmlText($html);
        $this->assertNotEmpty($ssml, "SSML not empty.");
        $this->assertEquals($ssml, $expectedSsml);
    }

    public function testHtmlBodyToSsml()
    {
        $html = $this->getValidBodyHtml();

        $expectedSsml = $this->getValidBodySsml();

        $ssmlCreator = new SsmlCreator();
        $ssml = $ssmlCreator->buildSsmlText($html);
        $this->assertNotEmpty($ssml, "SSML not empty.");
        $this->assertEquals($ssml, $expectedSsml);
    }

    public function testHtmlHeadersToSsml()
    {
        $html = $this->getValidHeadersHtml();

        $expectedSsml = $this->getValidHeadersSsml();
 
        $ssmlCreator = new SsmlCreator();
        $ssml = $ssmlCreator->buildSsmlText($html);
        $this->assertNotEmpty($ssml, "SSML not empty.");
        $this->assertEquals($ssml, $expectedSsml);
    }

   public function testHtmlParagraphToSsml()
   {
       $html = $this->getValidParagraphHtml();

       $expectedSsml = $this->getValidParagraphSsml();

       $ssmlCreator = new SsmlCreator();
       $ssml = $ssmlCreator->buildSsmlText($html);
       $this->assertNotEmpty($ssml, "SSML not empty.");
       $this->assertEquals($ssml, $expectedSsml);
   }

   public function testHtmlBrToSsml()
   {
       $html = $this->getValidBrHtml();

       $expectedSsml = $this->getValidBrSsml();

       $ssmlCreator = new SsmlCreator();
       $ssml = $ssmlCreator->buildSsmlText($html);
       $this->assertNotEmpty($ssml, "SSML not empty.");
       $this->assertEquals($ssml, $expectedSsml);
   }

   public function testHtmlStrongToSsml()
   {
       $html = $this->getValidStrongHtml();

       $expectedSsml = $this->getValidStrongSsml();

       $ssmlCreator = new SsmlCreator();
       $ssml = $ssmlCreator->buildSsmlText($html);
       $this->assertNotEmpty($ssml, "SSML not empty.");
       $this->assertEquals($ssml, $expectedSsml);
   }

   public function testHtmlBoldToSsml()
   {
       $html = $this->getValidBoldHtml();

       $expectedSsml = $this->getValidBoldSsml();

       $ssmlCreator = new SsmlCreator();
       $ssml = $ssmlCreator->buildSsmlText($html);
       $this->assertNotEmpty($ssml, "SSML not empty.");
       $this->assertEquals($ssml, $expectedSsml);
   }



}
