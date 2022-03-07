<?php
require __DIR__ . '/vendor/autoload.php';

/* This is importing the class `Helpers` and `Instagram` from the folder `InstagramPicture`. */

use sandroputraa\InstagramPicture\Instagram;

/* This is creating a new object of Instagram class. */
$instagram = new Instagram();

/* This is logging into your Instagram account. */
$username = '';
$password = '';

$Login = $instagram->Login($username, $password);
if ($Login['Status'] === true) {

    /* This is uploading a single photo to Instagram. */
    print_r(

        $instagram->UploadSingleFeeds(__DIR__ . '/mbaks.jpg', 'Caption !!')

    );

    echo "\n";

    /* This is changing your profile picture. */
    print_r(

        $instagram->ChangeProfile(__DIR__ . '/mbaks.jpg')

    );

} else {

    print_r($Login);

}
