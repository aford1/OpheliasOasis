<?php
/**
 * This file sets the configuration for the mysql database
 * @author Adam Ford
 */
//Database Login Information.
$host = 'localhost:8889';
$user = 'root';
$password = 'root';

$con = mysql_connect($host, $user, $password);
if (!$con) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("OpheliasOasis", $con);

?>
