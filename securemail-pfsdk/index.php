<?php

session_start();

include('config.php');

if($_SERVER['REMOTE_ADDR'] != "127.0.0.1" && $_SERVER['HTTP_PORT'] != "80") {

   header ('Location: https://'. $SERVER_NAME.'/'.$_SERVER['REQUEST_URI']);

} 


if ($_POST['Login']) {


	$user = trim($_POST['user']);
	$pass = trim($_POST['pass']);

	// Data validation
	if (!preg_match("/^[a-zA-Z0-9]{1,8}$/",$user))
 	  die("Incorrect username.");

	$xml = simplexml_load_file($XML_FILE)
          or die("Unable to open document.");

	foreach ($xml->user as $users) {

		if ($users->username != $user) {

		} else {

			if ($user == $users->username && $users->password == hash('ripemd160', $pass)) {
	
				$_SESSION['s_user'] = "$users->username";
				$_SESSION['s_phone'] = "$users->phone";
				$_SESSION['s_rand'] = "$users->pin";
	
				header('Location: pf.php');
	
			} else {
	
				echo "Login failed. <a href=\"index.php\">Go Back</a>";
				exit();
	
			}

		} // end user check

        } // end foreach loop

} else {

?>

	<FORM method="POST" action="<?php print $_SERVER['PHP_SELF']; ?>">
	
	<p>Username:<br>
	<INPUT type="text" name="user" size=25></p>
	<br /><br />
	
	Password:<br>
	<INPUT type="password" name="pass" size=25></p>
	
	<p><INPUT type="submit" name="Login" value="Login"></p>
	
	</FORM>

	<p><b>If your login is successful, the browser will hang temporarily until the call comes through and is successful.</b>

<?php

}

?>
