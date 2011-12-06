<?php

/*
 * Some basic functions for Ophelias Oasis data management system
 * @author Adam Ford
 */

include('configLocalDB.php');

//This function returns a list of rooms that are available for a certain day
function findAvailableRoom($arrivalDate, $departureDate) {
    //number of rooms in the hotel
    //get the total number of rooms
    $sql = "SELECT count(*) as numRooms from Room;";
    $row = mysql_fetch_array(mysql_query($sql));
    $numRooms = $row['numRooms'];
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
                            (departureDate < '$departureDate'))
                            AND
                            Reservation.status != 'canceled';";
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

function getBaseRate($roomID) {
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
    if (strlen($date) == 9) {
        $day = "0" . substr($date, 3, 1);
        $year = substr($date, 5, 4);
        $date = $year . "-" . $month . "-" . $day;
    } else {
        $day = substr($date, 3, 2);
        $year = substr($date, 6, 4);
        $date = $year . "-" . $month . "-" . $day;
    }
    return $date;
}

//Convert time from yyyy-mm-dd to mm/dd/yyyy
function convertDateReverse($date) {
    $year = substr($date, 0, 4);
    $month = substr($date, 5, 2);
    $day = substr($date, 8, 2);
    $date = $month . "/" . $day . "/" . $year;
    return $date;
}

function printReservationConformation($reservationID) {

    //Get the reservation information for the requested id
    $reservationInfo =
            "SELECT * from Reservation 
    INNER JOIN Customer ON Reservation.customerID = Customer.customerID
    WHERE reservationID = '$reservationID';";

    $result = mysql_query($reservationInfo);
    mysql_close($con);
    $row = mysql_fetch_array($result);

    //Calculate the total costs of the room based on nights and number of rooms.
    $nights = $row['numNights'];
    $roomRate = $row['rate'];
    $subTotal = $roomRate * (float) $nights;
    $taxRate = $row['taxRate'];
    $taxAmount = $subTotal * $taxRate;
    $total = $subTotal + $taxAmount;
    //format the floating numbers so they are rounded by two decimal places.
    $roomRate = number_format($roomRate, 2, '.', '');
    $subTotal = number_format($subTotal, 2, '.', '');
    $taxAmount = number_format($taxAmount, 2, '.', '');
    $total = number_format($total, 2, '.', '');


    return "
        <p><u>Your Reservation has been confirmed. Please keep this information for your records.</u></p>
        <h3>Reservation Information</h3>
    <table>
        <tr>
            <td><b>Confirmation Number:</b></td><td><b>" . $row['reservationID'] . "</b></td>
        </tr>
        <tr>
            <td>Date of Reservation:</td><td>" . convertDateReverse($row['dateofReservation']) . "</td>
        </tr>
        <tr>
            <td>First Name:</td><td>" . $row['firstName'] . "</td>
        </tr>
        <tr>
            <td>Last Name:</td><td>" . $row['lastName'] . "</td>
        </tr>      
        <tr>
            <td>Check In:</td><td>" . convertDateReverse($row['arrivingDate']) . "</td>
        </tr>
        <tr>
            <td>Check Out: </td><td>" . convertDateReverse($row['departureDate']) . "</td>
        </tr>
        <tr>
            <td>Reservation Type: </td><td>" . $row['reservationType'] . "</td>
        </tr>
        <tr>
            <td><h3>Payment Information</h3></td>
        </tr>
        <tr>
            <td>Nightly Rate: </td><td>$" . $roomRate . "</td>
        </tr>
        <tr>
            <td>" . $nights . " Night(s): </td><td>$" . $subTotal . "</td>
        </tr>
        <tr>
            <td>Tax: </td><td>$" . $taxAmount . "</td>
        </tr>
        <tr>
            <td><b>Total: </b></td><td><b>$" . $total . "</b></td>
        </tr>
        <tr> <td></td></tr>
    </table>

";
}

function getReservation($firstName, $lastName, $reservationID) {
    $query = "SELECT * from Reservation
                    INNER JOIN Customer on Reservation.customerID = Customer.customerID
                    WHERE firstName = '$firstName' AND lastName = '$lastName' AND reservationID = '$reservationID';";
    $result = mysql_query($query);
    mysql_close($con);
    return $result;
}

function changeDates($id, $arriving, $departing, $reservationType) {
    //find the amount of days for the new dates
    $diff = abs(strtotime($departing) - strtotime($arriving));
    $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

    //If the reservation is conventional, then there is no penalty for changing. 
    if ($reservationType == "conventional") {
        $query = "SELECT rate, balance, numNights, taxRate from Reservation where reservationID = '$id';";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);
        //compute new totals
        $rate = $row['rate'];
        $taxRate = $row['taxRate'];
        $subTotal = $rate * $days;
        $tax = $subTotal * $taxRate;
        $total = $subTotal + $tax;
        $balance = $total;


        $query = "UPDATE `Reservation` 
        SET `arrivingDate`='$arriving', `departureDate`='$departing' , `balance` = '$balance', `rate` = '$rate', `numNights` = '$days'
        WHERE `reservationID`='$id';";

        $result = mysql_query($query);
        mysql_query("commit;");
        mysql_close($con);
        return $result;
    }
    // this reservation is not conventional, so add a penalty to the balance
    else {
        $query = "SELECT rate, balance, numNights, taxRate from Reservation where reservationID = '$id';";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);
        //compute new totals
        $rate = $row['rate'];
        $taxRate = $row['taxRate'];
        $newRate = $rate + $rate * .10;
        $subTotal = $newRate * $days;
        $tax = $subTotal * $taxRate;
        $total = $subTotal + $tax;
        $balance = $total;


        $query = "UPDATE `Reservation` 
        SET `arrivingDate`='$arriving', `departureDate`='$departing' , `balance` = '$balance', `rate` = '$newRate', `numNights` = '$days'
        WHERE `reservationID`='$id';";

        $result = mysql_query($query);
        mysql_query("commit;");
        mysql_close($con);
        return $result;
    }
}

function cancelReservation($id, $penalty) {

    if ($penalty) {
        $query = "UPDATE `Reservation` 
        SET `status`='canceledWithPenalty' 
        WHERE `reservationID`='$id';";
    } else {
        $query = "UPDATE `Reservation` 
        SET `status`='canceled' 
        WHERE `reservationID`='$id';";
    }
    $result = mysql_query($query);
    mysql_query("commit;");
    mysql_close($con);
    return $result;
}

function getExpectedOccupancy($arrivalDate, $departureDate) {
//get the total number of rooms
    $sql = "SELECT count(*) as numRooms from Room;";
    $row = mysql_fetch_array(mysql_query($sql));
    $numRooms = $row['numRooms'];

//Find all rooms that are reserved for this period.
    $sql = "Select count(Room.roomID) as resCount
                From Reservation
                INNER JOIN Room ON Reservation.roomID = Room.roomID
                WHERE ((arrivingDate >= '$arrivalDate') 
                            OR 
                            (departureDate >'$arrivalDate')) 
                            AND
                            ((arrivingDate < '$departureDate') 
                            OR
                            (departureDate < '$departureDate'))
                            AND
                            Reservation.status != 'canceled';";
    $row = mysql_fetch_array(mysql_query($sql));
    $numRes = $row['resCount'];
    //echo $numRes;
    mysql_close($con);
    $occRate = $numRes / $numRooms;
    
    return $occRate * 100;
}

?>
