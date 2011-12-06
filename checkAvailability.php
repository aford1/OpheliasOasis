<?php

/*
 * This file checks to see if there are any rooms available for a specified date range
 * @author Adam Ford
 */
session_start();

$DEBUG = false;
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
$currentDate = $_GET['currentDate'];

$arrivalDate = convertDate($arrivalDate);
$departureDate = convertDate($departureDate);
$currentDate = convertDate($currentDate);

$_SESSION['arrivalDate'] = $arrivalDate;
$_SESSION['departureDate'] = $departureDate;
$_SESSION['rooms'] = $rooms;
$_SESSION['incentive'] = false;


if ($DEBUG) {
    echo("<p>Arrival Date: " . $arrivalDate . "\n</p>");
    echo("<p>Departure Date: " . $departureDate . "\n</p>");
    echo("<p>Requested Rooms: " . $rooms . "\n</p>");
}


//Find the number of days for the stay
$diff = abs(strtotime($departureDate) - strtotime($arrivalDate));
$days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

//find the number of days from the current date until the reservation would start
$diff = abs(strtotime($arrivalDate) - strtotime($currentDate));
$daysFromArrival = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));


$freeRooms = findAvailableRoom($arrivalDate, $departureDate);
if (sizeof($freeRooms) == 0) {
    echo "<div id='notAvailable'><h3>Sorry, there are no available rooms for the dates you selected.</h3></div>";
} else {

    if (sizeof($freeRooms) < $rooms) {
        echo("<div id='notAvailable'><h3>Sorry, there are only " . sizeof($freeRooms) . " available rooms during that time period.</h3></div>");
    } else {

        //if we are within 30 days of the start date, check to see if we can give incentive reservation
        $discount = 1;
        if($daysFromArrival <= 30){
            //get the expected occupancy rate
            $occRate = getExpectedOccupancy($arrivalDate, $departureDate);
            if($occRate < 60){
                $discount = .8;
                $_SESSION['incentive'] = true;
            }
        }
        
        //Calculate the total costs of the room based on nights and number of rooms.
        $baseRate = (float) getBaseRate(reset($freeRooms));
        $roomRate = $baseRate * $discount;
        $subTotal = $roomRate * $days * $rooms;
        $taxRate = .0755;
        $taxAmount = $subTotal * $taxRate;
        $total = $subTotal + $taxAmount;
        $roomRate = number_format($roomRate, 2, '.', '');
        $subTotal = number_format($subTotal, 2, '.', '');
        $taxAmount = number_format($taxAmount, 2, '.', '');
        $total = number_format($total, 2, '.', '');

        //display the table with the room details
        echo "
    <table class='room_info'>
                        <thead>
                            <tr>
                                <th class='room_desc' width='200'>
                        <h4>2 Queen Bed Room</h4>
                        </th>
                        <th class='availability'>

                        </th>
                        <th class='availability'>Availability</th>
                        <th class='price'>Price</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class='room_desc' colspan='2'>
                                    <div class='media'>
                                        <img src='images/desert_room.jpg' />                                      
                                    </div>
                                    <div class='desc'>
                                        <p> 2 Queen Beds Non Smoking Room With Wi-Fi.</p>
                                        </a>
                                    </div>
                                </td>
                                <td class='availability'>
                                    <span class='available'>Available</span>
                                </td>
                                <td class='price'>
                                    <div class='pricing_wrapper'>
                                        <table class='pricing_table'>
                                            <tbody>
                                                <tr>
                                                    <th>Nightly Rate</th>	
                                                    <td>                                                                    
                                                        <span>$" . $roomRate . "	
                                                        </span>                                                                                                                                                                 
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th> </th>	
                                                    <td> </td>
                                                </tr>
                                                <tr>
                                                    <th> " . $days . " Night(s)</br> " . $rooms . " Room(s)</th> 
                                                    <td>                                                                              
                                                        <span>$" . $subTotal . "
                                                        </span>                                                                                                                                                                
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Tax</th>
                                                    <td>                                                                            
                                                        <span>$" . $taxAmount . "	
                                                        </span>                                                                                                                                                                         
                                                    </td>
                                                </tr>
                                                <tr class='total'>
                                                    <th>Total Cost*</th>
                                                    <td>                                                                           
                                                        <span class='total'>$" . $total . "	
                                                        </span>                                                                                                                                                                 
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td>
                                                        <input type='button' value='Reserve' id='reserveButton' class='reserveButton'></input>
                                                    </td>
                                                </tr>                                                                        
                                            </tbody>
                                        </table> <!-- end of pricing table -->                                                                     
                                    </div> <!-- .pricing_wrapper -->
                                </td>
                            </tr>
                        </tbody>
                    </table> <!-- end of room info table -->        
    ";
        
        //if we are 60 days or more out from the reservation, show the 60 day prepaid prices.
        if ($daysFromArrival >= 60) {
            
            echo "<div class='center'><h3>Specials Available!</h3></div>";
            //calculate the discount prices
            $roomRate = $baseRate * 0.85;
            $subTotal = $roomRate * $days * $rooms;
            $taxRate = .0755;
            $taxAmount = $subTotal * $taxRate;
            $total = $subTotal + $taxAmount;
            $roomRate = number_format($roomRate, 2, '.', '');
            $subTotal = number_format($subTotal, 2, '.', '');
            $taxAmount = number_format($taxAmount, 2, '.', '');
            $total = number_format($total, 2, '.', '');
            echo"
        <div id='specials60' class='specials'>
        <form action='reserve60Special.php' method='POST'>
            <h4><u>Pay within 45 days of your reservation and save 15% on your reservation!</u></h4>
            <table>
                <tbody>
                    <td class='price'>
                                    <div class='pricing_wrapper'>
                                        <table class='pricing_table'>
                                            <tbody>
                                                <tr>
                                                    <th>Nightly Rate</th>	
                                                    <td>                                                                    
                                                        <span>$" . $roomRate . "	
                                                        </span>                                                                                                                                                                 
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th> </th>	
                                                    <td> </td>
                                                </tr>
                                                <tr>
                                                    <th> " . $days . " Night(s)</br> " . $rooms . " Room(s)</th> 
                                                    <td>                                                                              
                                                        <span>$" . $subTotal . "
                                                        </span>                                                                                                                                                                
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Tax</th>
                                                    <td>                                                                            
                                                        <span>$" . $taxAmount . "	
                                                        </span>                                                                                                                                                                         
                                                    </td>
                                                </tr>
                                                <tr class='total'>
                                                    <th>Total Cost*</th>
                                                    <td>                                                                           
                                                        <span class='total'>$" . $total . "	
                                                        </span>                                                                                                                                                                 
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td>
                                                        <input type='submit' value='Reserve' id='special60Button' class='reserveButton'></input>
                                                    </td>
                                                </tr>                                                                        
                                            </tbody>
                                        </table> <!-- end of pricing table -->                                                                     
                                    </div> <!-- .pricing_wrapper -->
                                </td>
                </tbody>
            </table>
            </form>
        </div>    
        ";
        }

        //if we are 90 days or more out from the reservation, show the 90 day prepaid prices.
        if ($daysFromArrival >= 90) {

            //calculate the discount prices
            $roomRate = $baseRate * 0.75;
            $subTotal = $roomRate * $days * $rooms;
            $taxRate = .0755;
            $taxAmount = $subTotal * $taxRate;
            $total = $subTotal + $taxAmount;
            $roomRate = number_format($roomRate, 2, '.', '');
            $subTotal = number_format($subTotal, 2, '.', '');
            $taxAmount = number_format($taxAmount, 2, '.', '');
            $total = number_format($total, 2, '.', '');
            echo"
        <div id='specials90' class='specials'>
        <form action='reserve90Special.php' method='POST'>
            <h4><u>Prepay now and save 25% on this reservation!</u></h4>
            <div class='center'>
            <table>
                <tbody>
                    <td class='price'>
                                    <div class='pricing_wrapper'>
                                        <table class='pricing_table'>
                                            <tbody>
                                                <tr>
                                                    <th>Nightly Rate</th>	
                                                    <td>                                                                    
                                                        <span>$" . $roomRate . "	
                                                        </span>                                                                                                                                                                 
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th> </th>	
                                                    <td> </td>
                                                </tr>
                                                <tr>
                                                    <th> " . $days . " Night(s)</br> " . $rooms . " Room(s)</th> 
                                                    <td>                                                                              
                                                        <span>$" . $subTotal . "
                                                        </span>                                                                                                                                                                
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Tax</th>
                                                    <td>                                                                            
                                                        <span>$" . $taxAmount . "	
                                                        </span>                                                                                                                                                                         
                                                    </td>
                                                </tr>
                                                <tr class='total'>
                                                    <th>Total Cost*</th>
                                                    <td>                                                                           
                                                        <span class='total'>$" . $total . "	
                                                        </span>                                                                                                                                                                 
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td>
                                                        <input type='submit' value='Reserve' id='special90Button' class='reserveButton'></input>
                                                    </td>
                                                </tr>                                                                        
                                            </tbody>
                                        </table> <!-- end of pricing table -->                                                                     
                                    </div> <!-- .pricing_wrapper -->
                                </td>
                </tbody>
            </table>
            </div>
            </form>
        </div>    
        ";
        }

        
    }
}
//print_r($freeRooms);
?>
