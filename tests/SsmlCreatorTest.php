<?php

use CidiLabs\Polly\SsmlCreator;

class SsmlCreatorTest extends AwsPollyTestCase
{
    public function testValidHtmlToSsml()
    {
        $html = $this->getValidHtml();
 
        $ssmlCreator = new SsmlCreator();
        $ssml = $ssmlCreator->buildSsmlText($html);
        $this->assertNotEmpty($ssml, "SSML not empty.");
        $this->assertStringContainsString('<speak>', $ssml, 'SSML contains <speak> tag.');
    }

}
