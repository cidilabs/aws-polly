<?php

namespace CidiLabs\Polly;

use Aws\Exception\AwsException;
use Aws\Polly\PollyClient;
use Aws\S3\S3Client;
use CidiLabs\Polly\SsmlCreator;

class AwsPollyFileConversionProvider
{
    protected $pollyClient;

    protected $s3Client;

    public function __construct($pollyClient = null, $s3Client = null)
    {
        $this->pollyClient = new PollyClient([
            'profile' => 'default',
            'version' => '2016-06-10',
            'region' => 'us-east-1'
        ]);

        $this->s3Client = new S3Client([
            'profile' => 'default',
            'region' => 'us-east-1',
            'version' => '2006-03-01'
        ]);


    }

    public function convertFile($options) {

        if($options['TextType'] == 'ssml'){
            $ssmlService = new SsmlCreator();
            $ssmlText = $ssmlService->buildSsmlText($options['text']);
        }

        try {
            $result = $this->pollyClient->synthesizeSpeech([
                'Text' => $ssmlText ? $ssmlText : $options['text'],
                'TextType' => $options['TextType'],
                'OutputFormat' => $options['format'],
                'VoiceId' => $options['voice'],
            ]);
            return $result;
        } catch (AwsException $e) {
            echo $e->getMessage();
            echo "\n";
        }

    }

    public function startFileConversion($options) {

        if($options['TextType'] == 'ssml'){
            $ssmlService = new SsmlCreator();
            $ssmlText = $ssmlService->buildSsmlText($options['text']);
        }

        try {
            $result = $this->pollyClient->startSpeechSynthesisTask([
                'Text' => $ssmlText ? $ssmlText : $options['text'],
                'TextType' => $options['TextType'],
                'OutputFormat' => $options['format'],
                'OutputS3BucketName' => $options['S3Bucket'],
                'VoiceId' => $options['voice'],
            ]);
            $taskId = $result['SynthesisTask']['TaskId'];

            return $taskId;
        } catch (AwsException $e) {
            echo $e->getMessage();
            echo "\n";
        }

    }

    public function isReady($taskId) {

        try {
            $result = $this->pollyClient->getSpeechSynthesisTask([
                'TaskId' => $taskId,
            ]);
            if($result["SynthesisTask"]["TaskStatus"] == "completed"){
                return $result["SynthesisTask"]["OutputUri"];
            }else {
                return false;
            }
        } catch (AwsException $e) {
            echo $e->getMessage();
            echo "\n";
        }
    }

    public function getFileUrl($taskId) {

        try {
            $result = $this->pollyClient->getSpeechSynthesisTask([
                'TaskId' => $taskId,
            ]);
          var_dump($result);
        } catch (AwsException $e) {
            echo $e->getMessage();
            echo "\n";
        }
    }

    public function getVoices()
    {

        try {
            $result = $this->pollyClient->describeVoices([
                'LanguageCode' => 'en-US',
            ]);
            return $result;
        } catch (AwsException $e) {
            echo $e->getMessage();
            echo "\n";
        }
    }

    public function downloadFile($bucket, $key)
    {
        try {
            $result = $this->s3Client->getObject([
                'Bucket' => $bucket,
                'Key'    => $key
            ]);

            return $result;
        } catch (AwsException $e) {
            echo $e->getMessage();
            echo "\n";
        }
    }

    public function deleteFile($bucket, $key) {
        try {
            $result = $this->s3Client->deleteObject([
                'Bucket' => $bucket,
                'Key'    => $key
            ]);
            return $result;
        } catch (AwsException $e) {
            echo $e->getMessage();
            echo "\n";
        }
    }


}