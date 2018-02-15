<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Plokko\PhpFcmV1\Message;
use Plokko\PhpFcmV1\Request;
use Plokko\PhpFcmV1\ServiceAccount;
use Plokko\PhpFcmV1\Targets\Token;

// Your Firebase credential file path
$firebase_secrets_file = dirname(__DIR__).'/.firebase-credentials.json';


$message = new Message();

$message->data->fill([
    'a'=>1,
    'b'=>'2',
    'x'=>'xxxxxxxxxxxxxxx',
    'n'=>null,
]);
$message->notification
    ->setTitle('My notification title')
    ->setBody('My notification body....');

$message->setTarget(new Token('your-device-token'));



// Prepare service account
$sa = new ServiceAccount($firebase_secrets_file);

//Custom http client (optional)
$myClient = new Client(['debug'=>true]);

//Prepare the request
$request = new Request($sa,true,$myClient);

$message->send($request);