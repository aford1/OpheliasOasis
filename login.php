<?PHP
session_start();

$failedLogin = false;

if (isset($_SESSION['failedAttempt'])) {
    if ($_SESSION['failedAttempt'] === TRUE)
        $failedLogin = true;
}
$_SESSION['failedAttempt'] = FALSE;

?>



<!DOCTYPE html>
<html>
    <head>
        <title>login</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script type="text/javascript" src="jquery/jquery-1.6.2.min.js"></script>
        <script type="text/javascript">
            $(function(){
                
                
                
            })
        </script>
    </head>
    <body>
        <div id="container">
            <div id="logo">
                <h1>Ophelia's Oasis</h1>
            </div>       
            <div id="spacer"></div>
            <?php
                if ($failedLogin) {
                    echo "<div class=error id=failedLogin >Incorrect username or password</div><br>";
                }
            ?>
            <div id ="formContainer">
                <form id="loginForm" method="post" action="checkLogin.php">
                    <fieldset>
                        <h2>Management System Login</h2>
                        <table>
                            <tr>
                                <td><label >Username</label></td>
                                <td><input name="username" type="text" id="username" value=""  autocomplete ="OFF" size="10"/></td>                          
                            </tr>                           
                            <tr>
                                <td><label >Password</label></td>
                                <td><input name="password" type="password"  id="password" value="" autocomplete ="OFF" size="10" /></td>
                            </tr>                         
                        </table>

                        <button id="loginButton">Login</button>
                    </fieldset>
                </form>
            </div>
        </div>
    </body>
</html>
