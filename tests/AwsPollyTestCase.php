<?php

use PHPUnit\Framework\TestCase;

class AwsPollyTestCase extends TestCase
{

    protected function getValidHtml()
    {
        return '<h1>This is a sample header</h1><div>This is a sample text to see exactly how the <h4>text converts over</h4></div>';
    }

    protected function getValidSsml()
    {
        return '<speak><amazon:auto-breaths><emphasis level="strong">This is a sample header</emphasis><p>This is a sample text to see exactly how the <emphasis level="moderate">text converts over</emphasis></p></amazon:auto-breaths></speak>';
    }

    protected function getInvalidHtml()
    {
        return '<h1>This is a sample header<div>This is a sample text to see exactly how the <h4>text converts over</h4>';
    }

    protected function getInvalidSsml()
    {
        return '<speak><amazon:auto-breaths><emphasis level="strong">This is a sample header<p>This is a sample text to see exactly how the <emphasis level="moderate">text converts over</emphasis></p></emphasis></amazon:auto-breaths></speak>';
    }
    protected function getValidUlHtml()
    {
        return '<ul><li>Coffee</li><li>Tea</li><li>Milk</li></ul>';
    }

    protected function getValidUlSsml()
    {
        return '<speak><amazon:auto-breaths><p><break strength="medium">Coffee</break><break strength="medium">Tea</break><break strength="medium">Milk</break></p></amazon:auto-breaths></speak>';
    }

    protected function getValidOlHtml()
    {
        return '<ol><li>Coffee</li><li>Tea</li><li>Milk</li></ol>';
    }

    protected function getValidOlSsml()
    {
        return '<speak><amazon:auto-breaths><p><break strength="medium">Coffee</break><break strength="medium">Tea</break><break strength="medium">Milk</break></p></amazon:auto-breaths></speak>';
    }

    protected function getValidTableHtml()
    {
        return '<table><tr><th>Person 1</th><th>Person 2</th><th>Person 3</th></tr><tr><td>Emil</td><td>Tobias</td><td>Linus</td></tr><tr><td>16</td><td>14</td><td>10</td></tr></table>';
    }

    protected function getValidTableSsml()
    {
        return '<speak><amazon:auto-breaths><p><p><break strength="medium">Person 1</break><break strength="medium">Person 2</break><break strength="medium">Person 3</break></p><p><break strength="medium">Emil</break><break strength="medium">Tobias</break><break strength="medium">Linus</break></p><p><break strength="medium">16</break><break strength="medium">14</break><break strength="medium">10</break></p></p></amazon:auto-breaths></speak>';
    }

    protected function getValidBodyHtml()
    {
        return '<body><h1>My First Heading</h1><p>My first paragraph.</p></body>';
    }

    protected function getValidBodySsml()
    {
        return '<speak><amazon:auto-breaths><emphasis level="strong">My First Heading</emphasis><p>My first paragraph.</p></amazon:auto-breaths></speak>';
    }

    protected function getValidHtmlHtml()
    {
        return '<html><body><h1>My First Heading</h1><p>My first paragraph.</p></body></html>';
    }

    protected function getValidHtmlSsml()
    {
        return '<speak><amazon:auto-breaths><emphasis level="strong">This is a sample header</emphasis><p>This is a sample text to see exactly how the <emphasis level=\"moderate\">text converts over</emphasis></p></amazon:auto-breaths></speak>';
    }

    protected function getValidHeadersHtml()
    {
        return '<html><body><h1>This is heading 1</h1><h2>This is heading 2</h2><h3>This is heading 3</h3><h4>This is heading 4</h4><h5>This is heading 5</h5><h6>This is heading 6</h6></body></html>';
    }

    protected function getValidHeadersSsml()
    {
        return '<speak><amazon:auto-breaths><emphasis level="strong">This is heading 1</emphasis><emphasis level="strong">This is heading 2</emphasis><emphasis level="moderate">This is heading 3</emphasis><emphasis level="moderate">This is heading 4</emphasis><emphasis level="reduced">This is heading 5</emphasis><emphasis level="reduced">This is heading 6</emphasis></amazon:auto-breaths></speak>';
    }

    protected function getValidParagraphHtml()
    {
        return '<html><body><p>This is a paragraph.</p><p>This is another paragraph.</p></body></html>';
    }

    protected function getValidParagraphSsml()
    {
        return '<speak><amazon:auto-breaths><p>This is a paragraph.</p><p>This is another paragraph.</p></amazon:auto-breaths></speak>';
    }

    protected function getValidBrHtml()
    {
        return '<p>This is<br>a paragraph<br>with line breaks.</p>';
    }

    protected function getValidBrSsml()
    {
        return '<speak><amazon:auto-breaths><p>This is<break strength="medium"></break>a paragraph<break strength="medium"></break>with line breaks.</p></amazon:auto-breaths></speak>';
    }

    protected function getValidStrongHtml()
    {
        return '<p>This text is normal.</p><p><strong>This text is important!</strong></p>';
    }

    protected function getValidStrongSsml()
    {
        return '<speak><amazon:auto-breaths><p>This text is normal.</p><p><emphasis level="moderate">This text is important!</emphasis></p></amazon:auto-breaths></speak>';
    }

    protected function getValidBoldHtml()
    {
        return '<p>This text is normal.</p><p><b>This text is bold.</b></p>';
    }

    protected function getValidBoldSsml()
    {
        return '<speak><amazon:auto-breaths><p>This text is normal.</p><p><emphasis level="moderate">This text is bold.</emphasis></p></amazon:auto-breaths></speak>';
    }



}