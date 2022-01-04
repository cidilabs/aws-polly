<?php

require 'vendor/autoload.php';

use CidiLabs\Polly\AwsPollyAlternateFileProvider;
use CidiLabs\Polly\SsmlCreator;

class AwsPollyTest extends AwsPollyTestCase {

    public function testPollyToTaskID() {


        $taskId = '01ec455e-7cb0-4d6f-80b4-b071de98c408';

        $text = "<h1>This is a sample header</h1><div>This is a sample text to see exactly how the <h4>text converts over</h4></div>";

        $options = [
            'format' => 'mp3',
            'S3Bucket' => 'cidilabs-polly',
            'voice' => 'Joanna',
            'TextType' => 'ssml',
            'ssml' => $this->getValidSsml(),
        ];

        $awsPollyMock = $this->getMockBuilder(AwsPollyAlternateFileProvider::class)
            ->onlyMethods(array('createAlternateFileTask'))
            ->getMock();

        $awsPollyMock->expects($this->once())
            ->method('createAlternateFileTask')
            ->will($this->returnValue($taskId));

        $this->assertEquals($awsPollyMock->createAlternateFileTask($options['ssml'],$options), $taskId);

    }

}