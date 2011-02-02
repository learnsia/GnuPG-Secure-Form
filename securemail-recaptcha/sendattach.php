<?php

include('recaptcha/recaptchalib.php');
include('config.php');

/* Script put together with code from:

July 22, 2008 by Emanuele Feronato http://www.emanueleferonato.com/2008/07/22/sending-email-with-multiple-attachments-with-php/

and Julie Meloni "Getting Started with GnuPG"

*/

	/************* CAPTCHA *********************/
        # the response from reCAPTCHA
        $resp = null;
        # the error code from reCAPTCHA, if any
        $error = null;

        # are we submitting the page?
          $resp = recaptcha_check_answer ($privatekey,
                                          $_SERVER["REMOTE_ADDR"],
                                          $_POST["recaptcha_challenge_field"],
                                          $_POST["recaptcha_response_field"]);

          if ($resp->is_valid) {

            # in a real application, you should send an email, create an account, etc

          } else {

            # set the error code so that we can display it. You could also use
            # die ("reCAPTCHA failed"), but using the error message is
            # more user friendly

                die("Incorrect Captcha Code entered. Please <a href=\"index.php\">go back</a> and try again." . $t_error);

          }

	/************ END CAPTCHA **********************/

// Declare Variables
$sender_name = trim(addslashes(htmlentities($_POST['sender_name'])));
$sender_email = trim(addslashes($_POST['sender_email']));
$secret_msg = trim(addslashes(htmlspecialchars($_POST['secret_msg'])));

// Some data validation
if ($sender_name == "" || $sender_email == "" || $secret_msg == "" )
  die("All fields must be filled out.");

// Check to be sure we have a valid email address
if (eregi("^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+))*$",$sender_email, $regs)) {

} else { 

	echo "Error: '$sender_email' isn't a valid mail address!\n";
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
  die("<b>$file1</b> is too large.  Files must be less than 5MB");

if ($file2_size > $FILE_SIZE_LIMIT) 
  die("<b>$file2</b> is too large.  Files must be less than 5MB");

// Clean up file names
$file1 = preg_replace("/[^[[:alnum:]]]/", "_", $file1);
$file2 = preg_replace("/[^[[:alnum:]]]/", "_", $file2);

// Put the files into an array to be processed for encryption
$the_files = array($file1, $file2);
$the_files_tmp = array($file1_tmp, $file2_tmp);
 
//set the environment variable for PGPPATH
putenv("GNUPGHOME=".$GNUPG_HOME); 
 
// email fields: to, from, subject, and so on
$to = $SEND_TO;
$from = "$sender_email";
$subject ="Secure email from $sender_email";
$message = "$secret_msg";
$headers = "From: $from";
 
// boundary
$semi_rand = md5(time());
$mime_boundary = "------------{$semi_rand}x";
 
// headers for attachment
$headers .= "\nMIME-Version: 1.0\n" . "X-Enigmail-Version: 1.1.1\n" . "Content-Type: multipart/mixed;" . " boundary={$mime_boundary}";

/*********************************************
BEGIN MESSAGE ENCRYPTION 
*********************************************/

// escape command arguments
$GNUPG = escapeshellcmd($GNUPG);

 	//create vars to hold paths and filenames
        $plainTxt = $TEMP_DIR. "$random_hash" . "data";
        $crypted = $TEMP_DIR. "$random_hash" . "pgpdata";

        //open file and dump in plaintext contents
        $fp = fopen($plainTxt, "w+");
        fputs($fp, $message);
        fclose($fp);

        //invoke PGP to encrypt file contents
        system("$GNUPG --encrypt -ao $crypted -r $YOUR_KEY $plainTxt");

        //open file and read encrypted contents into var
        $fd = fopen($crypted, "r");
        $message = fread($fd, filesize($crypted));
        fclose($fd);

        //delete files!
        unlink($plainTxt);
        unlink($crypted);

/*********************************************
END MESSAGE ENCRYPTION 
*********************************************/

// multipart boundary
$message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
  
$message .= "--{$mime_boundary}\n";

// preparing attachments
for($x=0;$x<count($the_files);$x++){

	$the_file = $the_files[$x];

	if ($the_file == "") {

	} else {

		// Read file contents into variable
	        $file = file_get_contents($the_files_tmp[$x]);
	
	/*********************************************
	BEGIN FILE ENCRYPTION 
	*********************************************/
	        //create vars to hold paths and filenames
	        $plainTxt = $TEMP_DIR. "$random_hash" . "data";
	        $crypted = $TEMP_DIR. "$random_hash" . "pgpdata";
	
	        //open file and dump in plaintext contents
	        $fp = fopen($plainTxt, "w+");
	        fputs($fp, $file);
	        fclose($fp);
	
	        //invoke PGP to encrypt file contents
	        system("$GNUPG --encrypt -ao $crypted -r $YOUR_KEY $plainTxt");
	
	        //open file and read encrypted contents into var
	        $fd = fopen($crypted, "r");
	        $data = fread($fd, filesize($crypted));
	        fclose($fd);
	
	        //delete files!
	        unlink($plainTxt);
	        unlink($crypted);
	  
	    $the_files[$x] = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $the_files[$x]);
	    $data = chunk_split(base64_encode($data));
	    $message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$the_files[$x].gpg\"\n" .
	    "Content-Disposition: attachment;\n" . " filename=\"$the_files[$x].gpg\"\n" .
	    "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
	    $message .= "--{$mime_boundary}\n";
	
	/*********************************************
	END FILE ENCRYPTION 
	*********************************************/
  
	}

} //end for loop
 
// send
 
$ok = @mail($to, $subject, $message, $headers);
 
if ($ok) {
 
    echo "<p>mail sent to $to!</p>";
 
} else {
 
    echo "<p>mail could not be sent!</p>";
 
}
 
?>
