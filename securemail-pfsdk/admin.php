<?php

include 'config.php';

if ($_POST['submit'] == "Add") {

	$new_user = strip_tags($_POST['new_user']);
	$user_phone = strip_tags($_POST['user_phone']);
	$user_pass = hash('ripemd160', $_POST['user_pass']);
	$user_pass2 = hash('ripemd160', $_POST['user_pass2']);

	$xml = simplexml_load_file($XML_FILE)
	  or die("Unable to open document.");

	$go_back = "<a href=\"admin.php\">Go Back</a>";

	if ($new_user == "" || $user_phone == "" || $user_pass == "")
	  die("All fields must be filled in. $go_back");
	
	/* Data validation */

	if (!preg_match("/[a-zA-Z0-9]{1,8}/", $new_user))
 	  die("Username can't be longer than eight characters.");

	// Phone number
        //Phone Number (North America)
        //Matches 3334445555, 333.444.5555, 333-444-5555, 333 444 5555, (333) 444 5555 and all combinations thereof.
        //Replaces all those with (333) 444-5555
        $user_phone = preg_replace('/\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})/', '(\1) \2-\3', $user_phone);
        $user_phone = preg_replace("/[^0-9]/","", $user_phone);


	if (!preg_match("/$user_pass/", $user_pass))
	  die("Password is not valid!");
	

	if ($user_pass != $user_pass2)
	  die("User passwords don't match.");

	/*********   End data validation ***********/
	foreach ($xml->username as $users) {

		if ($users == $new_user)
		  die("This user <b>$new_user</b> already exists.");

	} // end foreach loop

		$user_record = $xml->addChild('user');

		/*** add a username element ***/
		$user_record->addChild("username", utf8_encode($new_user));
		
		/*** add a phone element ***/
		$user_record->addChild("phone", utf8_encode($user_phone));
			
		/*** add password element ***/
		$user_record->addChild("password", utf8_encode($user_pass));

		/*** add random pin element ***/

		$pin = rand(1000,9999);
		$user_record->addChild("pin", utf8_encode($pin));
			
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($xml->asXML());
		$dom->save($XML_FILE);

	header('Location: admin.php');

} elseif ($_POST['submit'] == "Delete") {
	
	$xml = simplexml_load_file($XML_FILE)
	  or die("Unable to open document.");

	$new_user = $_POST['user'];

	/** Data Validation */

	if (!preg_match("/[a-zA-Z0-9]{1,8}/", $new_user))
 	  die("Username can't be longer than eight characters.");

	/** End Data Validation */

	foreach ($xml->user as $users) {

		if ($users->username == $new_user) {

			//Use XPath to find target node for removal
			//If target does not exist (already deleted by someone/thing else), halt
			if($users->username == false)
			  die("User doesn't exist. <b>$new_user</b>.");
			
			//Import simpleXml reference into Dom & do removal (removal occurs in simpleXML object)
			$domRef = dom_import_simplexml($users[0]); //Select position 0 in XPath array
			$domRef->parentNode->removeChild($domRef);
			
			//Format XML to save indented tree rather than one line and save
			$dom = new DOMDocument('1.0');
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($xml->asXML());
			$dom->save($XML_FILE);

		}

	} // end foreach loop

	header('Location: admin.php');

} else {

					
	echo "<table border=\"0\"";
	echo "<form action=\"";
	print $_SERVER['PHP_SELF'];
        echo "\" method=\"POST\">";
	echo "<tr>";
	echo "<td align=\"left\">Username:<br /> <input type=\"text\" name=\"new_user\" value=\"\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"left\">Phone: <br /><input type=\"text\" name=\"user_phone\" value=\"\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"left\">Password: <br /><input type=\"password\" name=\"user_pass\" value=\"\"></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=\"left\">Retype Password: <br /><input type=\"password\" name=\"user_pass2\" value=\"\"></td>";
	echo "</tr>";
	echo "<td align=\"center\"><input type=\"submit\" name=\"submit\" value=\"Add\">";

	echo "</form>";

	echo "</tr>";
	echo "</table>";

	echo "<p>Existing Users<p>";

	$xml = simplexml_load_file($XML_FILE)
	  or die("Unable to open document.");

	foreach ($xml->user as $users) {

		echo "<form action=\"";
		print $_SERVER['PHP_SELF'];
        	echo "\" method=\"POST\">";
		echo "<input type=\"hidden\" name=\"user\" value=\"$users->username\"><br />";
		echo "$users->username";
		echo " - Pin: $users->pin &nbsp;&nbsp;";
		echo "<input type=\"submit\" name=\"submit\" value=\"Delete\">";

		echo "</form>";

	} // end foreach loop

	?>

	<table>
			
	</table>		
				
	</p>			

			</body></html>

<?php

} // end main if statement
