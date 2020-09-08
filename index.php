<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <!-- 
  *****************************************************
  *                                                   *
  *    OTH Amberg-Weiden                              *
  *    Medientechnik und Medienproduktion (Master)    *
  *    Web-Engineering                                *
  *    Studienarbeit: Rapid Feedback mit dem TED      *
  *                                                   *
  *    Design und Realisierung:                       *    
  *                                                   *
  *    Marco Hanelt und Philipp Jetschina             *
  *    marco@lunaarte.de                              *
  *    kontakt@jetschina.de                           *
  *                                                   *
  *****************************************************
-->

<!--
medinf Server Zugang:

ftp: medinf.oth-aw.de/
User: web
Passwort: stAweb2016 (Apache und MySQL)

Datenbankserver: localhost
PHPMyAdmin: http://medinf.oth-aw.de/~florianhaupt/phpMyAdmin-4/
Datenbank-Tabellen individuell mit Prefix benennen: ted2016_name1_name2_

Web Zugang: https://medinf.oth-aw.de/~web/

-->

<!--
Database Setting File 1: /php/ajax.php Zeile 27-31
Database Setting File 2: /php/functions.php Zeile 29-33

/*
    $user = "web";
    $pass = "stAweb2016";
    $db = "web";
*/

-->

<!--
    Tabellennamen:
    Dozenten: ted2016_hj_dozent
    Umfragen: ted2016_hj_umfrage
    Antworten: ted2016_hj_antwort
-->


<title>TED</title>

<!-- font -->
<link href='https://fonts.googleapis.com/css?family=Hind+Vadodara:300,400,500,600,700' rel='stylesheet' type='text/css'>

<!-- css -->
<link rel="stylesheet" href="css/style.css">

<!-- font-awesome -->
<!-- <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css"> -->


<!-- php -->
<?php include("php/dozent.php"); ?>
<?php include("php/umfrage.php"); ?>
<?php include("php/antwort.php"); ?>
<?php session_start(); ?>
<?php include("php/functions.php"); ?>

<!-- js -->
<script language="javascript" type="text/javascript" src="js/jquery-1.12.3.min.js"></script>
<script language="javascript" type="text/javascript" src="js/popup/jquery.popupoverlay.js"></script>
<script src="http://canvasjs.com/assets/script/canvasjs.min.js"></script>
<!--    <script>window.jQuery || document.write('<script src="js/canvasjs.min.js"><\/script>')</script> -->
<script type='text/javascript'> var $_POST = <?php echo !empty($_POST)?json_encode($_POST):'null';?>; </script>
<script type='text/javascript'> var $_GET = <?php echo !empty($_GET)?json_encode($_GET):'null';?>; </script>
<script language="javascript" type="text/javascript" src="js/script_babel.js"></script>

<link rel="icon" href="img/Logo_favicon.ico" type="image/x-icon">


</head>


<body>
    <div class="header">
        <ul class="header_container">
            <li class="header_item">
                <a href="index.php">
                    <img class="logo" src="img/TED_Logo.png">
                </a>
            </li>

        </ul>
    </div>
    <div class="content">
    <div class="container session">
    <h1><?php if(isset($_SESSION['user'])){echo $_SESSION['user']->name;} ?></h1>
    <?php
    if(isset($_SESSION['user'])){
        echo "<div class='session_id_container'><input type='text' value='".$_SESSION['user']->link."' /></div>";

    }
    ?>
    </div>

    <div class="container registration">
    <?php if(!isset($_SESSION['user']) && !isset($_GET['link'])){ ?>
        <h1>Neuer Dozent</h1>

        <form name="create_dozent" method="post">
            <div class="input_container">
            <label>Benutzername:</label>
                <input name="name" type="text" <?php if(isset($_POST['name_exists']) || isset($_POST['no_name_reg']) ){?>style="background-color:#ed2553"<?php } ?> />
            </div>
            <div class="input_container">
            <label>Passwort:</label>
                <input name="pass_1" type="password" <?php if(isset($_POST['no_pass_1_reg']) || isset($_POST['short_pass']) || isset($_POST['reg_pass_no_match'])){?>style="background-color:#ed2553"<?php } ?> />

            </div>
            <div class="input_container">
            <label>Passwort wiederholen:</label>
                <input name="pass_2" type="password" <?php if(isset($_POST['no_pass_2_reg']) || isset($_POST['short_pass']) || isset($_POST['reg_pass_no_match'])){?>style="background-color:#ed2553"<?php } ?> />
            </div>
            <div class="input_container">
            <label>Umfrage ID:</label>
                <input name="anzeige" type="text" <?php if(isset($_POST['no_link']) || isset($_POST['link_exists']) ){?>style="background-color:#ed2553"<?php } ?>/>
            </div>

            <input name="create_dozent" type="hidden"  />
            <input class="button button_pos" value="Registrieren" type="submit" />
        </form>
    </div>

    <div class="container login">
        <h1>Login</h1>
        <form name="login" method="post">
            <div class="input_container">
                <label>Benutzername:</label>
            <input name="name" type="text" <?php if(isset($_POST['no_name_log']) || isset($_POST['wrong_pass']) ){?>style="background-color:#ed2553"<?php } ?>/>
            </div>
            <div class="input_container">
                <label>Passwort:</label>
            <input name="pass" type="password" <?php if(isset($_POST['no_pass_log']) || isset($_POST['wrong_pass']) ){?>style="background-color:#ed2553"<?php } ?>/>
            </div>
            <input name="login" type="hidden" />
            <input class="button button_pos" value="Login" type="submit" />
        </form>
    </div>

    <div class="container, umfrage">
        <h1>Umfrage Session</h1>
        <form name="connect" method="get">
            <div class="input_container">
                <label>Umfrage ID:</label>
            <input name="link" type="text" />
            </div>
            <input class="button button_pos" type="submit" value="Verbinden" />
        </form>
    <?php } ?>
    </div>

    <?php if(isset($_SESSION['user'])){ ?>
        <form name="logout" method="post">
            <input name="logout" type="hidden" />
            <input class="button button_pos2" type="submit" value="Logout" />
        </form>

        <button class="button button_pos2 delete_popup_open" type="button">Account Löschen</button>

        <div id="delete_popup" style="background-color: white; padding: 20px; ">
            Wollen Sie Ihren Account wirklich löschen?
            <form name="delete" method="post">
                <input name="delete" type="hidden">
                <input type="submit">
            </form>
            <button class="button delete_popup_close" type="button">Abbrechen</button>
        </div>

        <button class="button button_pos2 addUmfrage_popup_open" type="button">Neue Umfrage</button>

        <div id="addUmfrage_popup">
            <form   name="neueUmfrage" method="post" style="background-color: white; padding: 20px; ">
                <div class="input_container_pop">
                    <label>Frage:</label> <input name="frage" type="text" <?php if(isset($_POST['no_frage'])){?>style="background-color:#ed2553"<?php } ?>/> </div>
                <div class="input_container_pop">
                   <label>Antwort 1:</label><input name="aw1" id="aw1" type="text" <?php if(isset($_POST['less_2_aw'])){?>style="background-color:#ed2553"<?php } ?>/> </div>
                <div class="input_container_pop">
                    <label>Antwort 2:</label><input name="aw2" id="aw2" type="text" <?php if(isset($_POST['less_2_aw'])){?>style="background-color:#ed2553"<?php } ?>/> </div>
                <div class="input_container_pop">
                    <label>Zeit in Sekunden:</label> <input class="timer2" name="timer" type="number" value="10"<?php if(isset($_POST['no_timer']) || isset($_POST['short_timer']) ){?>style="background-color:#ed2553"<?php } ?>/> </div>
                <div class="input_container_pop">
                <label>Visualisierung:</label>
                <select class="dropdown" name="visualize" size="1">
                    <option value="0">Balken</option>
                    <option value="1">Torte</option>
                </select> </div>

                <input name="awCount" id="awCount" value="2" type="hidden" />
                <input name="addUmfrage" type="hidden" />
                <input class="button" value="Bestätigen" type="submit" />

                <button class="button addUmfrage_popup_close" type="button">Abbrechen</button>
                <button class="button" onClick='addAntwort()' type="button">Antwort Hinzufügen</button>
            </form>

        </div>

        <button class="button button_pos2 editDozent_popup_open" type="button">Account bearbeiten</button>

        <div id="editDozent_popup" style="background-color: white; padding: 20px; ">
            <form name="editDozent" method="post">
                <?php showEditDozent(); ?>

                <input name="editDozent" type="hidden">
                <input class="button button_pos2" type="submit">
                <button class="button button_pos2" type="button">Abbrechen</button>
        </div>
            </form>


        <div id="editUmfrage_popup" style="background-color: white; padding: 20px; ">
            <form name="editUmfrage" method="post">
                <?php showEditUmfrage(); ?>
                <input name="editUmfrage" type="hidden">
                <input class="button button_pos2" value="Bestätigen" type="submit">
                <button class="button button_pos2 editUmfrage_popup_close" type="button">Abbrechen</button>
                <button class="button button_pos2" onClick='addEditAntwort()' type="button">Antwort Hinzufügen</button>
            </form>
          
        </div>

        <div id="chart_popup">
            <div style="background-color: white; padding: 20px;">
                <div id="umfrage_name_dozent">
                <?php
                $umfragen = $_SESSION['user']->umfragen;
                foreach ($umfragen as $umAct){
                    if($umAct->aktiv == 1){
                        ?>
                        <div><?php echo $umAct->frage; ?></div>
                        <?php
                    }
                }
                ?>
                </div>
                <div id="timer"></div>
                <div id="chartContainer"></div>
            </div>
        </div>

        <div id="umfragen">
            <?php showUmfragen(); ?>
        </div>

    <?php } ?>

    <?php if (isset($_GET['link'])) { ?>
        <!-- <a href="index.php">Startseite</a> -->
        <h1 id="frage"></h1>
        <div class="umfrage_timer" id="timer_user"></div>
        <form name="Frage" id="frage" method="post">
            <div class="umfrage_fragen" id="antworten">
            </div>
            <input id="umfrage_id" name="umfrage_id" type="hidden" value="" />
        <input class="button button_pos3" value="Abstimmen" type="submit" />
        </form>
    <?php } ?>
        <div id="placeholder">
        </div>
    </div>
        <footer class="footer">
            <div>
                <span>
                    Copyright by Marco Hanelt und Philipp Jetschina
                </span>
            </div>
        </footer>
    </body>
</html>