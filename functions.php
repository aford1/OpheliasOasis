<?php

/*
 * Some basic functions for Ophelias Oasis data management system
 * @author Adam Ford
 */

include('configLocalDB.php');

//This function returns a list of rooms that are available for a certain day
function findAvailableRoom($arrivalDate, $departureDate) {
    //number of rooms in the hotel
    $numRooms = 5;
    //Find all rooms that are not available for this day
    $sql = "Select Room.roomID
                From Reservation
                INNER JOIN Room ON Reservation.roomID = Room.roomID
                WHERE ((arrivingDate >= '$arrivalDate') 
                            OR 
                            (departureDate >'$arrivalDate')) 
                            AND
                            ((arrivingDate < '$departureDate') 
                            OR
                            (departureDate < '$departureDate'));";
    $result = mysql_query($sql);
    mysql_close($con);


    //We know what rooms are taken for this day, so find the rooms that are still available
    $i = 1;
    while ($row = mysql_fetch_array($result)) {
        $takenRooms[$i] = $row['roomID'];
        $i++;
    }
    $freeRooms = array();
    for ($i = 1; $i <= $numRooms; $i++) {
        $freeRooms[$i] = $i;
    }
    if ($takenRooms) {
        $freeRooms = array_diff($freeRooms, $takenRooms);
    }
    //Return an array of all the free rooms
    return $freeRooms;
}

//Convert a date from mm/dd/yyyy  to yyyy-mm-dd
function convertDate($date) {
    $month = substr($date, 0, 2);
    $day = substr($date, 3, 2);
    $year = substr($date, 6, 4);
    $date = $year . "-" . $month . "-" . $day;
    return $date;
}

?>
