<html>
    <head>
        <!-- <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1, minimum-scale=1, width=device-width">
        <title>Error 403 (Insufficient Permissions)</title> -->
        <style>
            html {height:100%;width:100%;} main{align-items:center;justify-content:center;display:flex;height:100%;width:100%;background-color:rgb(230, 230, 230);} span#errorTitle{font-size:2em;padding-bottom:20px} #error-div{align-items:center;justify-content:center;display:flex;} #error-img{float:left;max-width:90%;} #error-div p{color:black;padding-left:40px;font-size:1.7em;} inc{color:gray;}
        </style>
    </head>
    <body>
        <main>
            <div id="error-div">
                <a href="index"><img id="error-img" src="img/logo1.png"></a><br>
                <p>
                    <span id="errorTitle">Error 404: Page not Found</span><br>
                    <?php
                        $errormsg = array("The emptiness...", "It's just not here.", "Nothing to see here", "Where are you trying to go?", "INVALID URL", "What were you thinking?", "There's nothing here yet, but if you want it we'll make it!");
                        echo "<b>".$errormsg[rand(0, sizeof($errormsg)-1)]."</b>";
                    ?>
                    <br>The page you are looking for does not exist. Please enter a vaild URL.<br>
                    <inc>mmmmmm... nothing here...</inc>
                </p>
            </div>
        </main>
    </body>
</html>