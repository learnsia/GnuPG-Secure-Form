/*******************/

Duane Dunston
thedunston@gmail.com


/*******************/

GnuPG Secure Form

These scripts were created to allow someone without GNUPG to send a message or documents securely to someone that does use GNUPG.

SETUP:
- Be sure you use an HTTPS connection.  Let's Encrypt provides free SSL certificates.
- Edit the config.php file based on your environment.
- Add your GNUPG public key and the trustdb.gpg file to a directory that the web user can access.
- Be sure you can send email from your web server host.
- For the key you will use, run the command:

gpg --list-keys

and use the long public key fingerprint in the config file (config.php) for "KEY_ID." It will need to the ID used to encrypt and decrypt the messages.

Requirements:
- PHP 7.X
- PHP GnuPG module (apt install php-gnupg on Ubuntu)
- You may want to add a Captcha if this is not behind an existing authentication system.
