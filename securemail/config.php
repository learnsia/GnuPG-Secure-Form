<?php

/**********  USER EDITS ***************/

// SERVER NAME
// THIS IS used to redirect the user to the secure
// form at https://, if they browse to the http:// location
// NO trailing slash.
// Just the domain name
$SERVER_NAME = '';

// Path to gpg executable
$GNUPG = '';

// YOUR KEY FINGERPRINT
// run:  gpg --list-keys and use the fingerprint
// which is the long public key string.
// It should be the fingeprint of the key that will be used
// to decrypt the messages and attachments received.
// 1ACEA81D842D9E1DD9F54D70BE2BE859EEE2B692
$YOUR_FINGERPRING = '';


// YOUR GNUPG email
// This is the key selected to send the encrypted email to
// It must match your GNUPG key
$YOUR_KEY = '';

// Location of the gnupg keys,
// WARNING!! This should be outside the web root.
$GNUPG_HOME = '';

// Email address where the email will be sent.
$SEND_TO = '';

// File size limit.
// This is where you set how large of an attachment you'll accept.
// You may have to change your php.ini file to accept file uploads
// for this path or use runtime variable to set the limit.
// Also, you'll you have have to adjust the execution time 
// settings to accomodate slow uploads or the script will
// timeout.
// File size in MB.
// Example: $FILE_SIZE_LIMIT = 10000000
// for 10 MB file uploads
$FILE_SIZE_LIMIT = '10000000';

/**********   END USER EDITS ***************/
