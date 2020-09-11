<!DOCTYPE html>
<html>
    <head>
        <!-- <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1, minimum-scale=1, width=device-width">
        <title>Error 403 (Insufficient Permissions)</title> -->
        <style>
            html {height:90%;width:100%;} main{align-items:center;justify-content:center;display:flex;height:100%;width:100%;background-color:rgb(230, 230, 230);} span#errorTitle{font-size:2em;padding-bottom:20px} #error-div{align-items:center;justify-content:center;display:flex;} #error-img{float:left;max-width:90%;} #error-div p{color:black;padding-left:40px;font-size:1.7em;} inc{color:gray;}
        </style>
    </head>
    <body>
        <main>
            <div id="error-div">
                <a href="index"><img id="error-img" src="/sssprinklerAPI/img/logo1.png"></a><br>
                <p>
                    <span id="errorTitle">Error 403: Insufficient Permissions</span><br>
                    <?php
                        $errormsg = array("Where to you think you're going?", "Halt!", "Let me see your papers!", "You shall not pass!", "Back up! Where are you of to now?", "What were you thinking?", "Boss said no, sorry can't let you pass.");
                        echo "<b>".$errormsg[rand(0, sizeof($errormsg)-1)]."</b>";
                    ?>
                    <br>Access to this section of the site is restricted. Please enter adequate credentials or just go back.<br>
                    <inc>No really you are not welcome here, move on.</inc>
                </p>
            </div>
        </main>
    </body>
</html>