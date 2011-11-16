<?php

include('configLocalDB.php');
//include('configMelanieDB.php');

session_start();


// Define $myusername and $mypassword
$myusername = $_POST['username'];
$mypassword = $_POST['password'];


// To protect MySQL injection (more detail about MySQL injection)
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$myusername = mysql_real_escape_string($myusername);
$mypassword = mysql_real_escape_string($mypassword);


$sql = "SELECT * FROM Employee  where username = '$myusername' AND password = '$mypassword';";
$result = mysql_query($sql);
mysql_close($con);


// Mysql_num_row is counting table row
$count = mysql_num_rows($result);
// If result matched $myusername and $mypassword, table row must be 1 row

if ($count == 1) {
    
    //Get the users ID
    $row = mysql_fetch_array($result);
    // Register $myusername, $mypassword
    $_SESSION['username'] = $myusername;
    $_SESSION['loggedin'] = TRUE;
    $_SESSION['failedAttempt'] = FALSE;
    header("location:management.php");
} else {

    $_SESSION['loggedin'] = FALSE;
    $_SESSION['failedAttempt'] = TRUE;
    header("location:login.php");
}
?>