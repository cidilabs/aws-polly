<?php

use CidiLabs\Polly\AwsPollyFileConversionProvider;

class AwsPollyTest extends AwsPollyTestCase {

    public function testPollyToTaskID() {

        $taskId = '01ec455e-7cb0-4d6f-80b4-b071de98c408';

        $options = [
            'format' => 'mp3',
            'S3Bucket' => 'cidilabs-polly',
            'voice' => 'Joanna',
            'TextType' => 'ssml',
            'text' =>  $this->getValidHtml(),
        ];

        $awsPollyMock = $this->getMockBuilder(AwsPollyFileConversionProvider::class)
            ->onlyMethods(array('startFileConversion'))
            ->getMock();

        $awsPollyMock->expects($this->once())
            ->method('startFileConversion')
            ->will($this->returnValue($taskId));

        $this->assertEquals($awsPollyMock->startFileConversion($options), $taskId);

    }

}