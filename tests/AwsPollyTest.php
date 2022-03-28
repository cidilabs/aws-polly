<?php

use CidiLabs\Polly\AwsPollyFileConversionProvider;

class AwsPollyTest extends AwsPollyTestCase {

    public function testPollyInit() {
        $polly = new AwsPollyFileConversionProvider();

        $this->assertEquals(true, is_object($polly));
    }

    public function testPollyToTaskID() {

        $taskId = '01ec455e-7cb0-4d6f-80b4-b071de98c408';

        $options = [
            'format' => 'mp3',
            'S3Bucket' => 'cidilabs-polly',
            'voice' => 'Joanna',
            'TextType' => 'ssml',
            'ssml' => $this->getValidSsml(),
        ];

        $awsPollyMock = $this->getMockBuilder(AwsPollyFileConversionProvider::class)
            ->onlyMethods(array('convertFile'))
            ->getMock();

        $awsPollyMock->expects($this->once())
            ->method('convertFile')
            ->will($this->returnValue($taskId));

        $this->assertEquals($awsPollyMock->convertFile($options), $taskId);

    }

}