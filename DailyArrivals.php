<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Daily Arrivals</title>
</head>


<?PHP
include ('configWebhostDB.php');

$result = mysql_query("SELECT `Reservation`.`customerID`, `Customer`.`firstName`, `Customer`.`lastName`, `Reservation`.`roomID`, `Reservation`.`arrivingDate`, `Reservation`.`departureDate`, `Reservation`.`reservationType` FROM Reservation INNER JOIN Customer on Reservation.customerID = Customer.customerID WHERE `Reservation`.`arrivingDate` = CURDATE() ORDER BY `Customer`.`lastName`") or die(mysql_error());

?>

<?php
echo ("Showing all customers arriving on ");
print(Date("l F d, Y"));
?>

<table width="500" border="1">
<tr>
    <td><strong>Customer ID</strong></td>
    <td><strong>First Name</strong></td>
    <td><strong>Last Name</strong></td>
    <td><strong>Room Number</strong></td>
    <td><strong>Arriving Date</strong></td>
    <td><strong>Departure Date</strong></td>
    <td><strong>Reservation Type</strong></td>
  </tr>
  
<?php
while ($reservationdata = mysql_fetch_array($result)){
	echo "<tr><td>" .$reservationdata['customerID'];
	echo "</td><td>" .$reservationdata['firstName'];
	echo "</td><td>" .$reservationdata['lastName'];
	echo "</td><td>" .$reservationdata['roomID'];
	echo "</td><td>" .$reservationdata['arrivingDate'];
	echo "</td><td>" .$reservationdata['departureDate'];
	echo "</td><td>" .$reservationdata['reservationType'];
	echo "</tr></td>";
}	

?>

</table>

</body>
</html>
