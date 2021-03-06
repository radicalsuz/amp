<?php header("P3P: CP='NON ADMi OUR STP INT'"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title><?php echo ( AMP_SITE_NAME . $login_manager->_loginScreenText ); ?> </title>
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

        form#login label[FOR="<?php print $login_manager->getFieldnamePassword( );?>"] {
            display: block;
            margin-top: 1.5ex;
        }

        p.authOptions{
            font-size: 95%;
            color: #999999; 
            text-align: center;
            clear: both;
        }
        p.loginNote {
            font-size: 65%;
            color: #999999; 
            text-align: center;
            clear: both;
        }

    </style>

</head>

<body onload="document.forms[0].elements[0].focus(); setOpacity( document.getElementById('shadow'), 100 ); setOpacity( document.getElementById('content'), 100 );">
<div id="wrapper">

    <div id="header">
        <h1><?php print AMP_SITE_NAME . $login_manager->_loginScreenText; ?> </h1>
    </div>

    <div id="content">

        <?php print (isset($login_manager->_handler->message)) ? "<p class=\"login {$login_manager->_handler->message_type}\">{$login_manager->_handler->message}</p>" : '' ?>

        <form id="login" method="post" action="<?php print $login_manager->getLoginUrl(); ?>" />

            <div id="formWrap">
                <?php print $login_manager->getFormFields();?>

                <input type="submit" value="Login" id="login" />
                <p class="authOptions"><?php print $login_manager->getAuthOptions(); ?></p>
            </div>

        </form>

    </div>

    <p class="loginNote"><?php print $login_manager->getHelpLinks(); ?></p>

</div>

</body>

</html>
