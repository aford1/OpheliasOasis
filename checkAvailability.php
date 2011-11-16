<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include('configLocalDB.php');

// Define $myusername and $mypassword
$arrivalDate = $_GET['arrivalDate'];
$departureDate = $_GET['departureDate'];
$rooms = $_GET['rooms'];

print("<p>".$arrivalDate."\n</p>");
print("<p>".$departureDate."\n</p>");
print("<p>".$rooms."\n</p>");

// To protect MySQL injection (more detail about MySQL injection)
$arrivalDate= stripslashes($arrivalDate);
$departureDate = stripslashes($departureDate);
$arrivalDate = mysql_real_escape_string($arrivalDate );
$departureDate = mysql_real_escape_string($departureDate);


$sql = "SELECT * FROM Room;";
$result = mysql_query($sql);
mysql_close($con);


// Mysql_num_row is counting table row
$count = mysql_num_rows($result);
// If result matched $myusername and $mypassword, table row must be 1 row

print($count);
?>
