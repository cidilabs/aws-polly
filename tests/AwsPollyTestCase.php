<?php

use PHPUnit\Framework\TestCase;

class AwsPollyTestCase extends TestCase
{

    protected function getValidHtml()
    {
        return '<h1>This is a sample header</h1><div>This is a sample text to see exactly how the <h4>text converts over</h4></div>';
    }

    protected function getInvalidHtml()
    {
        return '<h1>This is a sample header<div>This is a sample text to see exactly how the <h4>text converts over</h4>';
    }

    protected function getValidSsml()
    {
        return '<speak><amazon:auto-breaths><emphasis level="strong">This is a sample header</emphasis><p>This is a sample text to see exactly how the <emphasis level=\"moderate\">text converts over</emphasis></p></amazon:auto-breaths></speak>';
    }

}