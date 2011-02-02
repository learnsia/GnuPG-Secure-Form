<?php

session_start();

include('config.php');

$username = $_SESSION['s_user'];
$phone = $_SESSION['s_phone'];
$rand_code = $_SESSION['s_rand'];

/*
 * ---------------
 * 
 * Copyright (c) 2008 Positive Networks, Inc.
 * 
 * Permission is hereby granted, free of charge, to any person
 * obtaining  a copy of this software and associated documentation
 * files (the "Software"),  to deal in the Software without
 * restriction, including without limitation the  rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 * 
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT  SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,  ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER  DEALINGS IN THE SOFTWARE.
 * 
 * ---------------
*/

function the_send_form() {

?>

        <FORM method="POST" enctype="multipart/form-data" name="form1" action="sendattach.php">

        <p>Your Name:<br>
        <INPUT type="text" name="sender_name" size=25></p>

        <p>Your E-Mail Address:<br>
        <INPUT type="text" name="sender_email" size=25></p>

        <p>The Secret Message:<br>
        <TEXTAREA name="secret_msg" cols=75 rows=25></TEXTAREA></p>

        <p>Send an e-mail with an attachment:</p>
        <p>File: <input type="file" name="file[]"></p>
        <p>File: <input type="file" name="file[]"></p>

        <p><INPUT type="submit" value="Send Secure Message"></p>

        </FORM>

<?php

} // end the_send_form function

// Call pfsdk
require($PFSDK_AUTH);

                // note that the phone number contains no dashes, spaces, or any other
                // special characters.
                $res = pf_authenticate(
                        $username,        // username
                        $phone,        // phone
                        '1',                 // country code (optional)
                        false,               // allow international calls (optional)
                        '',      // hostname (optional)
                        '',   // ip (optional)
                        '',    // ca path (optional) 
                        '', // ca file (optional)
                        false,               // user can change phone (optional) 
                        'en',                // language (optional)
                        'standard',          // authentication type (optional)
                        'one-way',                  // authentication mode (optional)
                        $rand_code);                 // user's pin (optional)

                // the return value from the above function is an array with three elements,
                // the result of the authentication (boolean), the result of the phonecall
                // itself, and the result of the connection with the PhoneFactor backed,
                // respectively.  see call_results.txt for a list of call results and
                // descriptions that correspond to the second value in the array.
                if ($res[0]) {

			session_start();
			session_destroy();
                        print the_send_form();
                        exit();

                } else {

                        print "There was an error authenticating you.\n";
		echo "$username $phone $pin";
			print "<br />".$res[0];

                }
