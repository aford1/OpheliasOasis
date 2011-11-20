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

function getBaseRate ($roomID){
    $sql = "SELECT baseRate
                FROM Room
                WHERE roomID = '$roomID';";
    $result = mysql_query($sql);
    mysql_close($con);
    $row = mysql_fetch_array($result);
    return $row['baseRate'];
}

//Convert a date from mm/dd/yyyy  to yyyy-mm-dd
function convertDate($date) {
    $month = substr($date, 0, 2);
    $day = substr($date, 3, 2);
    $year = substr($date, 6, 4);
    $date = $year . "-" . $month . "-" . $day;
    return $date;
}

function printReservationInfo($reservationID){
    
    //Get the reservation information for the requested id
    $reservationInfo = "SELECT * from Reservation WHERE reservationID = '$reservationID';";
    
    $result = mysql_query($reservationInfo);
    mysql_close($con);
    $row = mysql_fetch_array($result);
    return "
    <table>
        <tr>
            <td>Reservation ID:</td><td>".$row['reservationID']."</td>
        </tr>
        <tr>
            <td>First Name:</td><td>".$row['firstName']."</td>
        </tr>
        <tr>
            <td>Last Name:</td><td>".$row['lastName']."</td>
        </tr>
        <tr>
            <td>Arrival Date:</td><td>".$row['arrivingDate']."</td>
        </tr>
        <tr>
            <td>Departure Date: </td><td>".$row['departureDate']."</td>
        </tr>
        <tr> <td></td></tr>
    </table>

";
}

?>
