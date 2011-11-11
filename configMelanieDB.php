<?php
/**
 * This file sets the configuration for the mysql database
 * @author Adam Ford
 */
//Database Login Information.
$host = 'db390470981.db.1and1.com';
$user = 'dbo390470981';
$password = 'SoftEng';

$con = mysql_connect($host, $user, $password);
if (!$con) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("db390470981", $con);

?>
