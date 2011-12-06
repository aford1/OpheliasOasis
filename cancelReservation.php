<?php

/*
 * This page cancels a reservation
 * @author Adam Ford
 */
include('configLocalDB.php');
include('functions.php');

$reservationID = $_GET['reservationID'];
$currentDate = $_GET['currentDate'];
$arrivalDate = $_GET['arrivalDate'];
$reservationType = $_GET['reservationType'];

$currentDate = convertDate($currentDate);

//find the time until the reservation starts
$diff = abs(strtotime($arrivalDate) - strtotime($currentDate));
$days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
echo $days;
$penalty = false;
if ((int)$days < 3 && $reservationType == "conventional") 
    $penalty = true;
else
    $penalty = false;

$result = cancelReservation($reservationID, $penalty);
echo $result;
?>
