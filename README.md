/*******************/

Duane Dunston
thedunston@gmail.com


/*******************/

GnuPG Secure Form

These scripts were created to allow someone without GNUPG to send a message or documents securely to someone that does use GNUPG.


Requirements:
- Be sure you use an HTTPS connection.  Let's Encrypt provides free SSL certificates.
- PHP 7.X
- PHP GnuPG module (apt install php-gnupg on Ubuntu)
- You may want to add a Captcha if this is not behind an existing authentication system.
- Add your GNUPG public key and the trustdb.gpg file to a directory that the web user can access.
- Edit the config.php file based on your settings.
- Be sure you can send email from your web server host.
