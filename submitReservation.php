<?php

/*
 * This file submits a reservation into the database
 * @author Adam Ford
 */
session_start();
include('configLocalDB.php');
include('functions.php');


//Get all of our post variables so we can put them in the database
$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$address = $_POST['address'];
$city = $_POST['city'];
$state = $_POST['state'];
$zipCode = $_POST['zipCode'];
$country = $_POST['country'];
$email = $_POST['email'];
$cardType = $_POST['cardType'];
$cardNumber = $_POST['cardNumber'];
$expMonth = $_POST['exp-month'];
$expYear = $_POST['exp-year'];

//Get the arrival date, departure date, and number or rooms that we stored in session variables
$arrivalDate = $_SESSION['arrivalDate'];
$departureDate = $_SESSION['departureDate'];
$rooms = $_SESSION['rooms'];

//Get a list of available rooms so that we can  get a free room to reserve 
$freeRooms = findAvailableRoom($arrivalDate, $departureDate);

//if there are no free rooms then something went wrong and we can't make the reservation
if (sizeof($freeRooms) < $rooms) {
    echo "<h3>Sorry, there is not enough rooms available to make the reservation.</h3>";
} else {
    
    //enter a reservation in the database for each requested room
    for ($i = 0; $i < $rooms; $i++) {
        //find the first available room out of the list of available rooms and reserve it.
        $firstAvailRoom = reset($freeRooms);
        $addReservation = "INSERT INTO `OpheliasOasis`.`Reservation` (`dateofReservation`, `arrivingDate`, 
                            `departureDate`, `reservationType`, `roomID`, `firstName`, `lastName`, `address`, 
                            `city`, `state`, `country`, `email`, `cardType`, `cardNumber`, `expirationMonth`, 
                            `expirationYear`) 
                        VALUES (CURRENT_DATE, '$arrivalDate', '$departureDate', 'conventional', '$firstAvailRoom', '$firstName', '$lastName', 
                        '$address', '$city', '$state', '$country', '$email', 
                        '$cardType', '$cardNumber', '$expMonth', '$expYear')";

        $reply = mysql_query($addReservation);
        
        //
        if ($reply) {
            $reservationID = mysql_insert_id();
            
            //Print reservation information
            $reservationInfo = printReservationInfo($reservationID);
            echo $reservationInfo;
        } else {
            echo "Error submitting reservation";
        }

        mysql_query("commit;");
    }
    mysql_close($con);
}
?>
