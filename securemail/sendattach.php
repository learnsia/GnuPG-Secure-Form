<?php

include('config.php');

/* Script put together with code from:

July 22, 2008 by Emanuele Feronato http://www.emanueleferonato.com/2008/07/22/sending-email-with-multiple-attachments-with-php/

and Julie Meloni "Getting Started with GnuPG"

*/

// Declare Variables
$sender_name = trim(addslashes(htmlentities($_POST['sender_name'])));
$sender_email = trim(addslashes($_POST['sender_email']));
$secret_msg = htmlentities($_POST['secret_msg'], ENT_QUOTES);

// Some data validation
if ($sender_name == '' || $sender_email == '' || $secret_msg == '' )
  exit('All fields must be filled out.');

// Check to be sure we have a valid email address
if (filter_var($sender_email, FILTER_VALIDATE_EMAIL)) {

} else { 

echo "Error: '{$sender_email}' isn't a valid mail address!\n";
exit(); 

} 

// array with filenames to be sent as attachment
// Temp file name to read in to be encrypted
$file1_tmp = $_FILES['file']['tmp_name'][0];
$file2_tmp = $_FILES['file']['tmp_name'][1];

// Real file names
$file1 = $_FILES['file']['name'][0];
$file2 = $_FILES['file']['name'][1];

// Size
$file1_size = $_FILES['file']['size'][0];
$file2_size = $_FILES['file']['size'][1];

// If the files are over 5MB then error
if ($file1_size > $FILE_SIZE_LIMIT) 
  exit("<b>{$file1}</b> is too large.  Files must be less than $FILE_SIZE_LIMIT"."MB");

if ($file2_size > $FILE_SIZE_LIMIT) 
  exit("<b>{$file2}</b> is too large.  Files must be less than $FILE_SIZE_LIMIT"."MB");

// Clean up file names
$file1 = preg_replace("/[^[[:alnum:]]]/", '_', $file1);
$file2 = preg_replace("/[^[[:alnum:]]]/", '_', $file2);

// Put the files into an array to be processed for encryption
$the_files = array($file1, $file2);
$the_files_tmp = array($file1_tmp, $file2_tmp);

//set the environment variable for PGPPATH
//error_reporting(E_ALL);

if (!is_dir($GNUPG_HOME))
	die("GNUPG Home dir doesn't exist.  Please check the config file.");

$res = gnupg_init();
putenv("GNUPGHOME=".$GNUPG_HOME); 
gnupg_seterrormode($res,GNUPG_ERROR_WARNING);
gnupg_addencryptkey($res,$YOUR_FINGERPRINT);
$enc_secret_msg = gnupg_encrypt($res, $secret_msg);

if ($enc_secret_msg == "")
	die("Error preparing message.");

// email fields: to, from, subject, and so on
$to = $sender_email;
$from = "$sender_email";
$subject ="Secure email from $sender_email";
$message = "$enc_secret_msg";
$headers = "From: $from";
 
// boundary
$semi_rand = md5(time());
$mime_boundary = "--==_mimepart_{$semi_rand}";
 
// Email headers
$headers .= "\nMIME-Version: 1.0\n" . "X-Enigmail-Version: 1.1.1\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"$mime_boundary\";\n charset=UTF-8\nContent-Transfer-Encoding: 7bit";

// multipart boundary
$message .= "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"UTF-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
  
$message .= "--{$mime_boundary}\n";

// preparing attachments
for($x=0;$x<count($the_files);$x++){

	$the_file = $the_files[$x];

	// If there is no file attachment then skip
	if ($the_file == '') {

	} else {
	
	/*********************************************
	BEGIN FILE ENCRYPTION 
	*********************************************/

		// Read file contents into variable
	        $file_plain = file_get_contents($the_files_tmp[$x]);

		// Set Environment variable
		//putenv('GNUPGHOME=/var/www/.gnupg');
		gnupg_addencryptkey($res,"AC185BB10B8B7605491FE6F5F3796D1FE505BCA4");
		$enc_file_contents = gnupg_encrypt($res, $file_plain);

		// Convert to base64
		$data = chunk_split(base64_encode($enc_file_contents));

	       	// fputs($fp, $enc_file_contents);
		// New Temp Directory
		// Gets around the issue with systemd putting temp files in a random directory
		// under /tmp on Ubuntu at least.
		// https://www.the-art-of-web.com/php/where-is-tmp/
		$file = new \SplFileObject(sys_get_temp_dir() . "/$the_files[$x].gpg", "w");
		$file->fwrite($data);
	  
		// Attachment Format	
		$the_files[$x] = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $the_files[$x]);
		$message .= "Content-Type: application/octet-stream;\n" . " name=$the_files[$x].gpg\n" .
		"Content-Disposition: attachment; " . " filename=$the_files[$x].gpg\n" .
		"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
		$message .= "--{$mime_boundary}\n";
	
		// wipe the new file.
		$file = NULL;
	/*********************************************
	END FILE ENCRYPTION 
	*********************************************/
  
	} //end for loop

}
 
// send
 
$ok = @mail($to, $subject, $message, $headers);
 
if ($ok) {

	echo "<p>mail sent to {$to}!</p>";
 
} else {
 
	echo "<p>mail could not be sent!</p>";
 
}
