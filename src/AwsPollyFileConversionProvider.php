<?php

namespace CidiLabs\Polly;

use Aws\AwsClient;
use Aws\Exception\AwsException;
use Aws\Polly\PollyClient;
use Aws\S3\S3Client;
use CidiLabs\Polly\SsmlCreator;

class AwsPollyFileConversionProvider
{
    protected $pollyClient;

    protected $s3Client;

    // Response object
    private $responseObject = [
        'data' => [
            'taskId' => '',
            'filePath' => '',
            'relatedFiles' => [],
            'status' => ''
        ],
        'errors' => []
    ];

    private $outputDir;
    private $s3bucket;
    private $pollyFormat;

    public function __construct($pollyClient = null, $s3Client = null, $outputDir = 'alternates', $pollyFormat = 'mp3')
    {
        $this->pollyClient = new PollyClient([
            'version' => '2016-06-10',
            'region' => 'us-east-1'
        ]);

        $this->s3Client = new S3Client([
            'region' => 'us-east-1',
            'version' => '2006-03-01'
        ]);

        $this->outputDir = $outputDir;
        $this->s3bucket = $_ENV['AWS_S3_BUCKET_NAME'];
        $this->pollyFormat = $pollyFormat;

    }

    public function supports()
    {
        return [
            'input' => ['pdf', 'doc'],
            'output' => ['mp3']
        ];
    }

    /*
     * Convert a file onDemand. Not to recieve a TaskId and check back the status.
     * Currently not used.
     */
    public function startFileConversion($options) {

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


    public function convertFile($options) {

        if($options['TextType'] == 'ssml'){
            $ssmlService = new SsmlCreator();
            $ssmlText = $ssmlService->buildSsmlText($options['text']);
        }

        try {
            $result = $this->pollyClient->startSpeechSynthesisTask([
                'Text' => $ssmlText ? $ssmlText : $options['text'],
                'TextType' => $options['TextType'],
                'OutputFormat' => $this->pollyFormat,
                'OutputS3BucketName' => $options['S3Bucket'],
                'VoiceId' => $options['voice'],
            ]);
            $this->responseObject['data']['taskId'] = $result['SynthesisTask']['TaskId'];

        } catch (AwsException $e) {
            $this->responseObject['errors'][] = $e->getMessage();
        }

        return $this->responseObject;

    }

    public function isReady($taskId) {

        try {
            $result = $this->pollyClient->getSpeechSynthesisTask([
                'TaskId' => $taskId,
            ]);

            if($result["SynthesisTask"]["TaskStatus"] == "completed"){
                $this->responseObject['data']['status'] = true;
            }else {
                $this->responseObject['data']['status'] = false;
            }
        } catch (AwsException $e) {
            $this->responseObject['errors'][] = $e->getMessage();

        }

        return  $this->responseObject;
    }

    public function getFileUrl($taskId, $options = []) {

        try {
            $result = $this->pollyClient->getSpeechSynthesisTask([
                'TaskId' => $taskId,
            ]);

            $this->responseObject['data']['filePath'] = $this->downloadFile($this->s3bucket,"{$taskId}.{$this->pollyFormat}");
            $this->responseObject['data']['status'] = true;
        } catch (AwsException $e) {
            $this->responseObject['errors'][] = $e->getMessage();
        }

        return  $this->responseObject;

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
            if(!is_dir($this->outputDir)){
                mkdir($this->outputDir,0755);
            }
            $filename = "{$this->outputDir}/{$key}";
            $result = $this->s3Client->getObject([
                'Bucket' => $bucket,
                'Key'    => $key,
                'SaveAs' => $filename
            ]);

            return $filename;
        } catch (AwsException $e) {
            $this->responseObject['errors'][] = $e->getMessage();
        }
    }

    public function deleteFile($fileUrl) {
        if (file_exists($fileUrl)) {
            unlink($fileUrl);
            $this->deleteFileOnS3($this->s3bucket,$this->responseObject['data']['taskId']);
        } else {
            $this->responseObject['errors'][] = "File not found";
        }

        return $this->responseObject;
    }

    private function deleteFileOnS3($bucket, $key) {
        try {
            $filename = "{$this->outputDir}/{$key}.{$this->pollyFormat}";

            $result = $this->s3Client->deleteObject([
                'Bucket' => $bucket,
                'Key'    => $filename
            ]);
        } catch (AwsException $e) {
            $this->responseObject['errors'][] = $e->getMessage();
        }

        return $this->responseObject;
    }


}