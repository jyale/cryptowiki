<?php

$user = $_REQUEST['user'];
$password = $_REQUEST['password'];


exec("curl -c cookies.txt -d 'lgname=Bryan&lgpassword=test&action=login&format=xml' http://smorz.cs.yale.edu/cryptowiki/wiki/api.php -o output.xml");

echo("weak");
?>

