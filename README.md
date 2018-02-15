# php-fpm
FCM App server protocol v1 php implementation


## Trubleshooting
If you get a 403 error _"Firebase Cloud Messaging API has not been used in project <project_name> before..."_ that's because the new v1 API is not enabled automatically (even if you genereated the credentials from Firebase Console and the legacy Api works).

You need to enable it from this page: https://console.developers.google.com/apis/api/fcm.googleapis.com/overview