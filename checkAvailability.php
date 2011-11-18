<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include('configLocalDB.php');
include('functions.php');

// Define $myusername and $mypassword
$arrivalDate = $_GET['arrivalDate'];
$departureDate = $_GET['departureDate'];
$rooms = $_GET['rooms'];

$arrivalDate = convertDate($arrivalDate);
$departureDate = convertDate($departureDate);

echo("<p>Arrival Date: " . $arrivalDate . "\n</p>");
echo("<p>Departure Date: " . $departureDate . "\n</p>");
echo("<p>Requested Rooms: " . $rooms . "\n</p>");


//Find the number of days for the stay
$diff = abs(strtotime($departureDate) - strtotime($arrivalDate));
$days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
echo("<p>Requested number of days: " . $days . "\n</p>");


$freeRooms = findAvailableRoom($arrivalDate, $departureDate);
if (sizeof($freeRooms) == 0) {
    echo "<h3>Sorry, there are no available rooms for the dates you selected.</h3>";
} else {

    print_r("<h3>There are " . sizeof($freeRooms)." rooms available for this period</h3>");
    echo"<p></p>";
    echo ("First available room: " . reset($freeRooms));
    echo"<p></p>";
    echo "Free Rooms: ";
    print_r($freeRooms);
}
?>
