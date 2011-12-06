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
$reservationType = $_SESSION['reservationType'];

//Get a list of available rooms so that we can  get a free room to reserve 
$freeRooms = findAvailableRoom($arrivalDate, $departureDate);

//if there are no free rooms then something went wrong and we can't make the reservation
if (sizeof($freeRooms) < 1) {
    echo "<h3>Sorry, there is not enough rooms available to make the reservation.</h3>";
} else {
    
    //insert the customer data into the customer table
    $insertCustomer = "INSERT INTO `Customer` 
        (`firstName`, `lastName`, `address`, `phone`, `email`, `cardNumber`,
       `city`, `state`, `country`, `zipCode`, `cardType`, `expirationMonth`, `expirationYear`)
        VALUES ('$firstName', '$lastName', '$address', '2345678909', '$email', '$cardNumber', 
        '$city', '$state', '$country', '$zipCode', '$cardType', '$expMonth', '$expYear');";
    $reply = mysql_query($insertCustomer);
    $customerID = mysql_insert_id();
    mysql_query("commit;");
    
    //get the number of nights the reservation will be for
    $diff = abs(strtotime($departureDate) - strtotime($arrivalDate));
    $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
    

    //find the first available room out of the list of available rooms and reserve it.
    $firstAvailRoom = reset($freeRooms);
    //get the base rate for the room to reserve
    $baseRate = getBaseRate($firstAvailRoom);
    if($reservationType == "60special"){
        $baseRate = $baseRate * .85;
    }
    if($reservationType == "90special"){
        $baseRate = $baseRate * .75;
    }
    if($_SESSION['incentive']){
        $baseRate = $baseRate*.8;
        $reservationType = "incentive";
    }
    //compute balance
    $subTotal = $baseRate * $days;
    $taxRate = .0755;
    $taxAmount = $subTotal * $taxRate;
    $total = $subTotal + $taxAmount;
    //statement that will reserve this room for the customer we inserted into the Customer table.
    $addReservation = "INSERT INTO `Reservation` 
        (`dateofReservation`, `arrivingDate`, `departureDate`, `reservationType`, 
        `roomID`, `customerID`, `rate`, `status`, `taxRate`, `numNights`, `balance`) 
        
        VALUES (CURRENT_DATE, '$arrivalDate', '$departureDate', '$reservationType', '$firstAvailRoom', 
                        '$customerID', '$baseRate', 'active', '.0755', '$days', '$total')";

    $reply = mysql_query($addReservation);
    
    //if the statement worked, print out the reservation information for the customer
    if ($reply) {
        $reservationID = mysql_insert_id();

        //Print reservation information
        $reservationInfo = printReservationConformation($reservationID);
        echo $reservationInfo;
    } else {
        echo "Error submitting reservation";
    }

    mysql_query("commit;");

    mysql_close($con);
}
?>
