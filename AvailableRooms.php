<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>All Rooms</title>
</head>


<?PHP
include ('configWebhostDB.php');
?>

<?php
$result = mysql_query("SELECT * FROM Rooms WHERE status = 'vacant'") or die(mysql_error());
?>

<?php echo ("Showing all available rooms."); ?>

<table width="500" border="1">
<tr>
    <td><strong>Room Number</strong></td>
    <td><strong>Base Rate</strong></td>
    <td><strong>Status</strong></td>
    <td><strong>Type</strong></td>
  </tr>
  
<?php
while ($Roomdata = mysql_fetch_array($result)){
	echo "<tr><td>" .$Roomdata['roomID'];
	echo "</td><td>" .$Roomdata['baseRate'];
	echo "</td><td>" .$Roomdata['status'];
	echo "</td><td>" .$Roomdata['type'];
	echo "</tr></td>";
}	

?>

</table>

</body>
</html>
