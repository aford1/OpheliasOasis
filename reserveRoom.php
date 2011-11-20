
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
            
            $(function() {
                $("#submitReservation").click(function(){
                    alert("submit registration information");
                    
                    var phone = $("#phone1").attr("value") + $("#phone2").attr("value") + $("#phone3").attr("value");
                    console.log(phone);
                    $.ajax({
                        url: "submitReservation.php",
                        type: "POST",
                        data: "firstName="+$("#firstName").attr("value")+
                            "&lastName="+$("#lastName").attr("value")+
                            "&address="+$("#address").attr("value")+
                            "&city="+$("#city").attr("value")+
                            "&state="+$("#state").attr("value")+
                            "&zipCode="+$("#zipCode").attr("value")+
                            "&country="+$("#country").attr("value")+
                            "&email="+$("#email").attr("value")+
                            "&phone="+$("#phone").attr("value")+
                            "&cardType="+$("#cardType").attr("value")+
                            "&cardNumber="+$("#cardNumber").attr("value")+
                            "&exp-month="+$("#exp-month").attr("value")+
                            "&exp-year="+$("#exp-year").attr("value")   ,               
                        success: function(result){
                            if(result){
                                $("#reserveForm").children().remove();
                                $("#reserveForm").html(result);
                                
                            }
                            else
                                alert("Error submitting form");                              
                        }
                    }); 
                })    
            });
            
          
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
            <div id="spacer"></div>

            <div id ="reserveForm">
                <h3>Contact Information</h3>
                <form>
                    <table >
                        <td>*First Name</td><td><input type="text" id="firstName" value="Adam" /></td></tr>
                        <tr><td>*Last Name</td><td><input type="text" id="lastName" value="Ford" /></td></tr>  
                        <tr><td>*Address</td><td><input id="address" value="1234 fake street" ></td></tr>
                        <tr><td>*City</td><td><input type="text" id="city" value="Honolulu" /></td></tr><tr>  
                        <tr><td>*State</td><td><input type="text" id="state" value="Hawaii"  /></td></tr><tr> 
                        <tr><td>*Zip/Postal code</td><td><input type="text" id="zipCode"  value="96816" /></td></tr><tr> 
                        <tr><td>*Country</td><td><input type="text" id="country" value="USA" /></td></tr><tr> 
                        <tr><td>*Email </td><td><input type="text" id="email"  value="1234@gmail.com" /></td></tr>
                        
                    </table>

                    <h3>Credit Card Information</h3>

                    <table >
                        <tr><td>*Card Type</td></tr>
                        <tr>
                            <td>
                                <select id="cardType" id="cardType" default="Visa">
                                    <option>Visa</option>
                                    <option>Master Card</option>
                                    <option>American Express</option>
                                    <option>Discover</option>
                                </select>
                            </td>
                        </tr>
                        <tr> <td></br></td></tr>
                        <tr>
                            <td>*Credit Card Number</td>
                        </tr>
                        <tr>
                            <td><input type="text" id="cardNumber"  value="23432432432432"/></td>
                        </tr>
                        <tr> <td></br></td></tr>
                        <tr>
                            <td>*Expiration date</td>
                        </tr>
                        <tr>
                            <td>
                                <select name="expMonth" id="exp-month" value="Jan"><option></option> 		
                                    <option value="01">Jan</option>
                                    <option value="02">Feb</option>
                                    <option value="03">Mar</option>
                                    <option value="04">Apr</option>
                                    <option value="05">May</option>
                                    <option value="06">Jun</option>
                                    <option value="07">Jul</option>
                                    <option value="08">Aug</option>
                                    <option value="09">Sep</option>
                                    <option value="10">Oct</option>
                                    <option value="11">Nov</option>
                                    <option value="12">Dec</option></select>

                                <select name="expYear" id="exp-year" value="2011"><option></option> 
                                    <option value="2011">2011</option>
                                    <option value="2012">2012</option>
                                    <option value="2013">2013</option>
                                    <option value="2014">2014</option>
                                    <option value="2015">2015</option>
                                    <option value="2016">2016</option>
                                    <option value="2017">2017</option>
                                    <option value="2018">2018</option>
                                    <option value="2019">2019</option>
                                    <option value="2020">2020</option>
                                    <option value="2021">2021</option></select>
                            </td>
                        </tr>
                        <tr> <td></br></td></tr>

                        <tr>
                            <td><input type="button" value="Continue" class="reserveButton" id="submitReservation"></td>
                            <td><input type="reset" value="Cancel"  onClick="location.href='index.html'"></td>
                        </tr>
                    </table>
                </form>
            </div>

        </div>



    </body>
</html>

