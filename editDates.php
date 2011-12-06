<?php

/*
 * This page checks to see if a date range is valid for a reservation and makes the changes if they are
 * @author Adam Ford
 */
include('configLocalDB.php');
include('functions.php');

$arrivalDate= $_GET['arrivalDate'];
$departureDate= $_GET['departureDate'];
$currentDate = $_GET['currentDate'];

//echo $newArrival.$newDeparture;

$arrivalDate = convertDate($arrivalDate);
$departureDate = convertDate($departureDate);
$currentDate = convertDate($currentDate);

$freeRooms = findAvailableRoom($arrivalDate, $departureDate);
if (sizeof($freeRooms) == 0) {
    echo "<div id='notAvailable'>Sorry, there are no available rooms for the dates you selected.</div>";
}
else{
    echo "
        <div class='greenLetters'>Rooms Available for these dates</div>
        <input type='button' value='Confirm Changes' id='confirmChanges' class='redButton'></input>

";
}

?>
