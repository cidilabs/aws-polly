<?php

use CidiLabs\Polly\SsmlCreator;

class SsmlCreatorTest extends AwsPollyTestCase
{
    public function testHtmlToSsml()
    {

        $html = "<h1>This is a sample header</h1><div>This is a sample text to see exactly how the <h4>text converts over</h4></div>";

        $ssml = "<speak><amazon:auto-breaths><emphasis level=\"strong\">This is a sample header</emphasis><p>This is a sample text to see exactly how the <emphasis level=\"moderate\">text converts over</emphasis></p></amazon:auto-breaths></speak>";

        $ssmlMock = $this->getMockBuilder(SsmlCreator::class)
            ->onlyMethods(array('buildSsmlText'))
            ->getMock();

        $ssmlMock->expects($this->once())
            ->method('buildSsmlText')
            ->will($this->returnValue($ssml));

        $this->assertEquals($ssmlMock->buildSsmlText($html), $ssml);
    }

}