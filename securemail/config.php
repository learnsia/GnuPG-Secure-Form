<?php

/**********  USER EDITS ***************/

// SERVER NAME
// THIS IS used to redirect the user to the secure
// form at https://, if they browse to the http:// location
// NO trailing slash.
$SERVER_NAME = '10.106.3.197';

// Path to GNUPG
$GNUPG = '/usr/bin/gpg';

// YOUR GNUPG email
// This is the key selected to send the encrypted email to
// It must match your GNUPG key
$YOUR_KEY = 'thedunston@gmail.com';

// Location of the gnupg keys,
// WARNING!! This should be outside the web root.
$GNUPG_HOME = '/var/www/.gnupg';

// Email address where the email will be sent.
$SEND_TO = 'thedunston@gmail.com';

// This is the temp directory to encrypt the message and files.
// The web user must have write permissions to this directory
// WARNING!! This should be outside the web root.

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
