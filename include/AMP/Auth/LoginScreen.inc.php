<?php header("P3P: CP='NON ADMi OUR STP INT'"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title><?= $_GLOBAL['SiteName'] ?> Administration</title>
    <style type="text/css">

        * {
            font-family: "Lucida Grande", helvetica, arial, verdana, sans-serif;
        }

        h1 {
            font-size: 140%;
            text-align: center;
            color: #006699;
        }

        div#wrapper {
            width: 500px;
            margin-top: 15%;
            margin-left: auto;
            margin-right: auto;
        }

        div#content {
            width: 400px;
            margin: 0 auto;
            padding: 2ex 0 0 0;
            border: 1px solid #999999;
            text-align: center;
        }

        p.login {
            padding: 0.5ex;
            width: 90%;
            margin: 0 auto 1em;
            font-size: 80%;
        }

        p.Error {
            color: white;
            background-color: #cc0000;
            border: 1px outset #660000;
            font-weight: bold;
        }

        p.OK {
            font-size: 72%;
            color: #226622;
            background-color: #ccffcc;
            border: 1px outset #88aa88;
        }

        form#login div#formWrap {
            width: 300px;
            margin: 0 auto;
            text-align: left;
        }

        form#login input {
            display: block;
            width: 200px;
            margin-left: 50px;
            font-size: 90%;
        }

        form#login input#login {
            margin: 2ex auto 0 auto;
            width: 8em;
        }

        form#login label {
            font-size: 80%;
            margin-left: 50px;
            color: #333333;
        }

        form#login label[FOR="password"] {
            display: block;
            margin-top: 1.5ex;
        }

        p.loginNote {
            font-size: 65%;
            color: #999999; 
            text-align: center;
        }

    </style>
</head>

<body onload="document.forms[0].elements[0].focus();">

<div id="wrapper">

    <div id="header">
        <h1><?php global $SiteName; print $SiteName; ?> Administrative Login</h1>
    </div>

    <div id="content">

        <?= (isset($this->message)) ? "<p class=\"login {$this->message_type}\">{$this->message}</p>" : '' ?>

        <form id="login" method="post" action="<?= $_SYSTEM['PHP_SELF'] ?>" />

            <div id="formWrap">
                <label for="username">Username:</label> <input type="text" name="username" />
                <label for="password">Password:</label> <input type="password" name="password" />

                <input type="submit" value="Login" id="login" />
            </div>

        </form>

    </div>

    <p class="loginNote">If you are having trouble logging in, please contact the <a href="mailto:<?= AMP_SITE_ADMIN ?>">site administrator</a>.</p>

</div>

</body>

</html>
