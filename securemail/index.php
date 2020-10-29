<?php

?>

<HTML>
<HEAD>
<TITLE>Emails Secure Form</TITLE>
</HEAD>
<BODY>

<h1>Send secure email</h1>

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
</BODY>
</HTML>
