LOGINSYSTEM

/* This is my first ever personal project. This welcomed me to the world of programming. It is a login system with enough security sign up criteria. Email verification is also available in this system. I used procedural programming to build this from scratch. Styling isn't that great but functionality-wise, I am proud of what I built especially since it was my first time. */

1. import the 'loginsystem.sql' to get you started. Database name used here is 'loginsystem' but you are free to change it in 'core/credentials/secure.php'. You could put in your personal database credentials here as well.

2. Go to 'core/init.php' and change the 'SITE_ROOT' relative to your server root. If the 'loginsystem' folder is inside your server root directly, '/loginsystem' would do just fine.

3. Also in the 'core/credentials/secure.php', it is essential to put in your correct email credentials. This is compulsory as complete registration by activating users account, emailing users and recovery uses email services.

4. To make a user an admin, you have to manually change the 'is_admin' in the database to '1'. Admin users have the ability to email users that have the 'receive_mail' in the database set to '1'.

5. Profile images are dependent on the 'php.ini' file. The image size must be less than the 'upload_max_filesize' in the 'php.ini' file.
