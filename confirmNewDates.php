<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include('configLocalDB.php');
include('functions.php');

$arrivalDate = $_GET['arrivalDate'];
$departureDate = $_GET['departureDate'];
$currentDate = $_GET['currentDate'];
$reservationID = $_GET['reservationId'];
$reservationType = $_GET['reservationType'];

//echo $newArrival.$newDeparture;

$arrivalDate = convertDate($arrivalDate);
$departureDate = convertDate($departureDate);
$currentDate = convertDate($currentDate);



$freeRooms = findAvailableRoom($arrivalDate, $departureDate);
if (sizeof($freeRooms) == 0) {
    echo "<div id='notAvailable'>Sorry, there are no available rooms for the dates you selected.</div>";
} else {
    $result = changeDates($reservationID, $arrivalDate, $departureDate, $reservationType);
    if ($result) {
        echo "Your Reservation Has been updated";
    }
    else{
        echo "We're Sorry, changes could not be made to your reservation";
    }
}
?>
