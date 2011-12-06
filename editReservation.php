<?php
/*
 * This file is used to display a current reservation and provide the functionality to edit it.
 * @author Adam Ford
 */
include('configLocalDB.php');
include('functions.php');

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$reservationID = $_POST['reservationID'];

$reservation = getReservation($firstName, $lastName, $reservationID);

$row = mysql_fetch_array($reservation);
if (!$row) {
    header("location:index.html");
}

//Calculate the total costs of the room based on nights and number of rooms.
$reservationType = $row['reservationType'];
$arrivalDate = $row['arrivingDate'];



$nights = $row['numNights'];
$roomRate = $row['rate'];
$balance = $row['balance'];
$subTotal = $roomRate * (float) $nights;
$taxRate = $row['taxRate'];
$taxAmount = $subTotal * $taxRate;
$total = $subTotal + $taxAmount;
//format the floating numbers so they are rounded by two decimal places.
$roomRate = number_format($roomRate, 2, '.', '');
$subTotal = number_format($subTotal, 2, '.', '');
$taxAmount = number_format($taxAmount, 2, '.', '');
$total = number_format($total, 2, '.', '');
?>


<!DOCTYPE html>
<html>
    <head>
        <title>Ophelia's Oasis</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="style.css" />
        <link type="text/css" href="css2/redmond/jquery-ui-1.8.16.custom.css" rel="Stylesheet" />
        <script type="text/javascript" src="jquery/jquery-1.6.2.min.js"></script>
        <script type="text/javascript" src="jquery/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript">
            
            var d = new Date;           
            //Variables for the date
            var month = d.getMonth()+1;
            var day = d.getDate();
            var nextDay = day+1;
            var year = d.getFullYear();
            var currentDate = month+"/"+day+"/"+year;
            
            
            $(function(){
                $("#editDates").click(function(){               
                    $("#checkIn").html("<input id= 'newCheckIn' type='text' value='<?PHP echo convertDateReverse($row['arrivingDate']); ?>'></input>");
                    $("#checkOut").html("<input id='newCheckOut' type='text' value='<?PHP echo convertDateReverse($row['departureDate']); ?>'></input>");
                    $("#editDatesDiv").html("<input type='button' value='Check Dates' id='checkDates' class='redButton'></input>");
                    insertCheckDates();
                })
                
                $("#cancelReservation").click(function(){
                    var answer = confirm ("Are you sure you want to cancel this reservation?\n\
 If you cancel less than three days from your check in date, you will be charged for the first night.");
                    if (answer){
                        //cancel the reservation
                        $.ajax({
                            url: "cancelReservation.php",
                            type: "GET",
                            data: "reservationID="+'<?PHP echo $reservationID; ?>'+
                            "&currentDate="+currentDate+
                            "&arrivalDate"+'<?PHP echo $arrivalDate; ?>'+
                            "&reservationType="+'<?PHP echo $reservationType; ?>',
                            success: function(result){
                                if(result == "1"){
                                    $("#paymentInfo").html("");
                                    $("#checkResponse").html("<u>YOUR RESERVATION HAS BEEN CANCELLED</u>");   
                                    $("#cancelButton").html("");
                                    $("#editDatesDiv").html("");
                                }
                                else
                                    alert("Error submitting form");                              
                            }
                        }); 
                    }
                    else{
                        //do nothing
                    }  
                })              
            })
        
            function insertCheckDates() {
                $("#checkDates").click(function() {
                    //alert("checkDates clicked for "+$("#newCheckIn").attr("value"));
                    $.ajax({
                        url: "editDates.php",
                        type: "GET",
                        data: "arrivalDate="+$("#newCheckIn").attr("value")+
                            "&departureDate="+$("#newCheckOut").attr("value")+
                            "&currentDate="+currentDate,               
                        success: function(result){
                            if(result){
                                //alert(result);
                                $("#paymentInfo").html("");
                                $("#checkResponse").html(result);
                                $("#confirmChanges").click(function(){                           
                                    confirmChanges();
                                })
                            }
                            else
                                alert("Error submitting form");                              
                        }
                    });   
                })
            }
            function confirmChanges() {
                <?PHP if($row['reservationType'] == "conventional"){ ?>
                var answer = confirm("Are you sure you want to change your reservation?");
                <?PHP } else{ ?>
                var answer = confirm("Are you sure you want to change your reservation? You will be charged and extra 10% of the regular rate");
                <?PHP } ?>
                if(answer){
                    $.ajax({
                        url: "confirmNewDates.php",
                        type: "GET",
                        data: "arrivalDate="+$("#newCheckIn").attr("value")+
                            "&departureDate="+$("#newCheckOut").attr("value")+
                            "&currentDate="+currentDate+
                            "&reservationId="+<?PHP echo $reservationID; ?>+
                            "&reservationType="+'<?PHP echo $reservationType; ?>',
                        success: function(result){
                            if(result){
                                //alert(result);
                                $("#paymentInfo").html("");
                                $("#checkResponse").html(result);
                            }
                            else
                                alert("Error submitting form");                              
                        }
                    }); 
                }
            }   
        </script>
    </head>
    <body>

        <div id="container">
            <div id="logo">
                <h1>Ophelia's Oasis</h1>
            </div>
            <ul id="nav">
                <li><a href="index.html">Home</a></li>
                <li><a href="index.html"">News</a></li>
                <li><a href="index.html"">Contact</a></li>
                <li><a href="index.html"">About</a></li>
            </ul>
            <?PHP
            if ($row['status'] == "canceled") {
                echo "<div class='center'><u>THIS RESERVATION HAS BEEN CANCELLED</u></div>";
            }
            ?>
            <div id="reservationInfo">


                <h3>Reservation Information</h3>
                <table>
                    <tr>
                        <td>Reservation Number:</td><td><?PHP echo $row['reservationID']; ?></td>
                    </tr>
                    <tr>
                        <td>Reservation Type:</td><td><?PHP echo $row['reservationType']; ?></td>
                    </tr>
                    <tr>
                        <td>Date of Reservation:</td><td><?PHP echo convertDateReverse($row['dateofReservation']); ?></td>
                    </tr>
                    <tr>
                        <td>First Name:</td><td><?PHP echo $row['firstName']; ?></td>
                    </tr>
                    <tr>
                        <td>Last Name:</td><td><?PHP echo $row['lastName']; ?></td>
                    </tr>      
                    <tr>
                        <td>Check In:</td><td><div id="checkIn"><?PHP echo convertDateReverse($row['arrivingDate']); ?></div></td>
                    </tr>
                    <tr>
                        <td>Check Out: </td><td><div id="checkOut"><?PHP echo convertDateReverse($row['departureDate']); ?></div></td>
                    </tr>
                    <?PHP
                    if ($row['status'] != "canceled") {
                        ?>
                        <tr>
                            <td>
                                <div id="cancelButton"><input id="cancelReservation" class="redButton" type="button" value="Cancel Reservation"></input></div>
                            </td>
                            <td>
                                <div id="editDatesDiv"><input id="editDates" class="redButton" type="button" value="Edit Dates"></input></div>
                            </td>
                        </tr>

                    </table>
                    <div id="checkResponse"></div>

                    <div id="paymentInfo">
                        <h3>Payment Information</h3>
                        <table>
                            <tr>
                                <td>Nightly Rate: </td><td>$<?PHP echo $roomRate; ?></td>
                            </tr>
                            <tr>
                                <td>Night(s): </td><td>$<?PHP echo $subTotal; ?></td>
                            </tr>
                            <tr>
                                <td>Tax: </td><td>$<?PHP echo $taxAmount; ?></td>
                            </tr>
                            <tr>
                                <td><b>Total: </b></td><td><u><b>$<?PHP echo $total; ?></b></u></td>
                            </tr>
                            <tr> <td></td></tr>

                        </table>
                    </div>
                    <?PHP
                }
                ?>
            </div>

        </div>



    </body>
</html>
