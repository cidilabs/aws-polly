
# aws-polly

aws-polly is an integration library for the file conversion functionality of files to audio using aws polly.
HTML to SSML is also included with SsmlCreator.php

## Setup

aws-polly can be installed to your project via Composer by adding the following line to your composer.json file: 
```
composer require "cidilabs/aws-polly": "dev-master"
```

```"cidilabs/aws-polly": "dev-master"```

Once aws-polly library is installed, you'll need to let UDOIT know which file conversion library you'll be using.

This can be done:

- In the .env: 
```
AVAILABLE_FILE_FORMATS="pdf"
MP3_FILE_FORMAT_CLASS="\\CidiLabs\\Polly\\AwsPollyFileConversionProvider"
```

You will need to define your [AWS Credentials](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html) in the AWS.
For local the default is to put it in the env for docker-compose for your IAM Access keys.
You should be using a EC2 IAM Role for AWS.
In your docker-compose file 
```
environment:
      - AWS_ACCESS_KEY_ID=
      - AWS_SECRET_ACCESS_KEY=
```
# AwsPollyFileConversionProvider.php
## Basic Usage

- **fileName**: The name of the file
- **fileUrl**: The download URL for the file
- **fileType**: The file type of the original file
- **format**: The file type that we want to convert to
```
$polly =  new  AwsPollyFileConversionProvider();

$fileUrl =  "https://cidilabs.instructure.com/files/295964/download?download_frd=1&verifier=RZwKCP3iVlNQIULZnTAXO0usUROMC9AuplKkDf2g";

$options =  array('fileUrl'  => $fileUrl,  'fileType'  =>  'pdf',  'format'  =>  'html',  'fileName'  =>  'Test1.pdf');

$polly->convertFile($options);
```

## Class Methods

### convertFile
### Public Methods
#### Parameters
- ***options***: (array) Takes in options
-- **fileName**: (string) The name of the file
-- **fileUrl**: (string) The download URL for the file
-- **fileType**: (string) The file type of the original file
-- **format**: (string) The file type that we want to convert to
-- **Text**: (String) in the end this is created by the function to transform html to ssml
-- **TextType**: (String) The Type of Text, This MUST be ssml or text
-- **voice**: The voice used in polly for the conversion
-- **S3Bucket**: Where you want to put the polly converted files
#### Returns
- ***taskId***: (string) The UUID representing the file conversion task
- ***null***

### isReady
#### Parameters
- ***taskId***: (string) The UUID representing the file conversion task
#### Returns
- ***True/False*** (boolean) True if the file has been converted and is ready, false otherwise
### getFileUrl
#### Parameters
- ***taskId***: (string) The UUID representing the file conversion task
#### Returns
- ***fileUrl***: (string) The url of the converted file
- ***null***
### downloadFile
#### Parameters
- ***bucket***: (string) The bucket you want to download from
- ***key***: (string) key of the object you are trying to access
#### Returns

### deleteFile
#### Parameters
- ***fileUrl***: (string) The url of the converted file
#### Returns
- ***True/False*** (boolean) True if successfully deleted, false otherwise


### Private Methods

### deleteFileOnS3
#### Parameters
- ***bucket***: (string) The bucket you want to delete from
- ***key***: (string) key of the object you are trying to delete
#### Returns

# SsmlCreator.php

## Class Methods

### buildSsmlText
#### Parameters
- ***text***: (string) HTML to take in to convert to SSML
#### Returns
- ***data*** (string) SSML

### Private Methods

### setupBaseSSML
#### Parameters
- ***html***: (string) brings in HTML to setup base before converting
#### Returns
- ***html***: (string) returns base html

### convertToSSML
#### Parameters
- ***html***: (string) HTML to be converted to SSML
#### Returns
- ***ssml***: (string) returns ssml


### cleanChildNodes
#### Parameters
- ***childNodes***: (Array) Array of ChildNodes of a DomElement
#### Returns

### removeTagAttributes
#### Parameters
- ***node***: (node) Brings in a node of a DomDocument
#### Returns
- ***node***: (node) node of a DomDocument

### removeTagAttributes
#### Parameters
- ***node***: (node) Brings in a node of a DomDocument
#### Returns
- ***node***: (node) node of a DomDocument

### stripHTML
#### Parameters
- ***html***: (string) html to strip out tags
#### Returns
- ***html***: (string) html without tags

### changeTagName
#### Parameters
- ***node***: (node) node to change element name
- ***name***: (string) name to change node to 
#### Returns
- ***newnode***: (node) returns a new node with the element or tag changed

### cleanUpBeforeDomDoc
#### Parameters
- ***html***: (string) html to make sure is clean before setting up base
#### Returns
- ***html***: (string) returns html with doctype and head removed