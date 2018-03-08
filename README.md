# php-fpm-v1
FCM App server protocol v1 php implementation

__NOTE: this project is now part of  [plokko/firebase-php](https://github.com/plokko/firebase-php) and may be deprecated__
## Installation:
Install with composer via

`$composer require plokko\php-fcm-v1`

## Usage:


The FCM message is build using the `Message` class
```php
$message = new Message();
```
This class contains all the API V1 objects like `Notification`
```
//Set the message notification
$message->notification
    ->setTitle('My notification title')
    ->setBody('My notification body....');
```
The `Data` payload
```php
$message->data->fill([
    'a'=>1,
    'b'=>'2',
]);
$message->data->set('x','value');
$messsage->data->y='Same as above';
```
And system-specific configuration like `AndroidConfig`, `WebpushConfig` and `ApnsConfig`.

The message require that the `Target` parameter is specified with either a single device (`Token`) `Topic` or `Condition`
```php
$message->setTarget(new Token('your-fcm-device-token'));
//Or
$message->setTarget(new Topic('your-fcm-topic'));
//Or
$message->setTarget(new Condition('your-fcm-conditions'));
```
If this value is not set the message will throw an error before submitting.


Before submitting we need to downloaded a JSON file with your service account credentials (see https://firebase.google.com/docs/admin/setup#initialize_the_sdk ).

This file is needed to initialize the class `ServiceAccount` that's used to generate an OAuth2 token for the FCM request.
```php
// Prepare service account
$sa = new ServiceAccount('path/to/your/firebase-credentials.json');
```

The `ServiceAccount` will not be used directly to submit the message but to build the `Request`:
```php
$request = new Request($sa);
```
For testing purpuses you can also set the `validate_only` parameter to test the message in Firebase without submitting it to the device;
the Request's http client can also be overriddent with a custom GuzzleHttp client.
```php
//Custom http client
$myClient = new Client(['debug'=>true]);

$request->setHttpClient($myClient);
$request->validate_only = true; 
//or
$request->validateOnly(true);
```

To send the `Message` use the `send` method:
```php
$message->send($request);
```
If the function will not throw a `FcmError`, `BadRequest` or a generic `GuzzleException` the message has been successfully sent and, if it's not a validate_only request, the message name will be populated.

```php
//after submitting:
echo 'my message name:'.$message->name; 
``

If you want to use the validate_only without modifying the request the `validate` method will force the validate_only flag on the request.
```php
$request->validateOnly(false);
$message->validate($request);//will be a validate_only request anyway
```


### Full example:
```php
<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use GuzzleHttp\Client;
use Plokko\PhpFcmV1\Message;
use Plokko\PhpFcmV1\Request;
use Plokko\PhpFcmV1\ServiceAccount;
use Plokko\PhpFcmV1\Targets\Token;

// Your Firebase credential file path
$firebase_secrets_file = dirname(__DIR__).'/.firebase-credentials.json';

// Prepare service account
$sa = new ServiceAccount($firebase_secrets_file);


$message = new Message();

// Add a data payload,NB: everything will be converted as a string:string
$message->data->fill([
    'a'=>1,//Will be converted to '1'
    'b'=>'2',
    'x'=>'xxxxxxxxxxxxxxx',
    'n'=>null,//Will be converted to ''
]);

//Add a notification
$message->notification
    ->setTitle('My notification title')
    ->setBody('My notification body....');

//Set the message destination
$message->setTarget(new Token('your-device-token'));


//Custom http client (optional)
$myClient = new Client(['debug'=>true]);

//Prepare the request
$request = new Request($sa,true,$myClient);

$message->send($request);
```

## Trubleshooting:
If you get a 403 error _"Firebase Cloud Messaging API has not been used in project <project_name> before..."_ that's because the new v1 API is not enabled automatically (even if you genereated the credentials from Firebase Console and the legacy Api works).

You need to enable it from this page: https://console.developers.google.com/apis/api/fcm.googleapis.com/overview
