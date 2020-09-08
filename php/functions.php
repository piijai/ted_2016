<?php

/* 
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
*/

    include("Notification.php");

    //SERVERVERBINDUNG XAMPP
    /*
    $servername = "localhost";
    $user = "root";
    $pass = "";
    $db = "webengineering_sa";
    */

//SERVERVERBINDUNG HOCHSCHULE

    $servername = "localhost";
    $user = "web";
    $pass = "stAweb2016";
    $db = "web";


    //Connection
    $connection = mysqli_connect($servername, $user, $pass, $db);

    //check
    if ($connection->connect_error){
        die("Verbindung fehlgeschlagen: ".$this->connection->connect_error);
    }

    //POST-ABFRAGEN
    //Dozent Formular abgeschickt
    if(isset($_POST['create_dozent'])){
        create_dozent($connection);
    }

    //Login Formular abgeschickt
    if(isset($_POST['login'])){
        login($connection);
    }

    //Logout-Button gedrückt
    if(isset($_POST['logout'])){
        session_destroy();
        unset($_SESSION);
    }

    //Dozent löschen Button gedrückt
    if(isset($_POST['delete'])){
        delete_dozent($connection);
    }

    //Umfrage hinzufügen Button gedrückt
    if(isset($_POST['addUmfrage'])){
        addUmfrage($connection);
    }

    //Umfrage löschen Button gedrückt
    if(isset($_POST['deleteUmfrage'])){
        deleteUmfrage($connection, $_POST['deleteUmfrage']);
    }

    //Umfrage aktivieren Button gedrückt
    if(isset($_POST['aktivieren'])){
        activateUmfrage($connection, $_POST['aktivieren']);
    }

    //Dozent bearbeiten Button gedrückt
    if(isset($_POST['editDozent'])){
        editDozent($connection);
    }

    //Umfrage bearbeiten Button gedrückt
    if(isset($_POST['editUmfrage'])){
        editUmfrage($connection);
    }

    //Verbinden Button gedrückt
    if (isset($_GET['link'])){
        //connect($connection);
    }

    if (isset($_POST['aw'])){
        answer($connection);
    }


    //FUNKTIONEN

    //Dozent hinzufügen
    function create_dozent($connection){

        //Namen überprüfen
        if(empty($_POST['name'])){
            //Abbrechen falls kein Name gesetzt ist
            $_POST['no_name_reg'] = true;
            return false;
        } else {
            //Überprüfen ob Name bereits vergeben ist
            $name = $_POST['name'];
            $query = "SELECT * FROM ted2016_hj_dozent WHERE name='".$name."';";
            $result = $connection->query($query);
            if($result->num_rows > 0){
                $_POST['name_exists'] = true;
                return false;
            }
        }
        //Passwort überprüfen
        //Abbrechen falls Passwort nicht gesetzt, kürzer als 7 Zeichen, oder ungleich Passwort 2 ist
        if (empty($_POST['pass_1'])){
            $_POST['no_pass_1_reg'] = true;
            return false;
        } elseif (empty($_POST['pass_2'])){
            $_POST['no_pass_2_reg'] = true;
            return false;
        } elseif(strlen($_POST['pass_1'])<7) {
            $_POST['short_pass'] = true;
            return false;
        } elseif ($_POST['pass_1'] != $_POST['pass_2']){
            $_POST['reg_pass_no_match'] = true;
            return false;
        }
        //Anzeigename überprüfen
        //Abbrechen falls Anzeigename nicht gesetzt
        if (empty($_POST['anzeige'])){
            $_POST['no_link'] = true;
            return false;
        } else {
            $link = $_POST['anzeige'];
            $query = "SELECT * FROM ted2016_hj_dozent WHERE link='".$link."';";
            $result = $connection->query($query);
            if($result->num_rows > 0){
                //Abbrechen falls Anzeigename schon verwendet
                $_POST['link_exists'] = true;
                return false;
            } else {
                //Datensatz erzeugen
                $pass = hash("sha256", $_POST['pass_1']);
                $query = "INSERT INTO ted2016_hj_dozent (name, pass, link) VALUES ('".$name."', '".$pass."', '".$link."');";
                if ($result = $connection->query($query) === TRUE){
                    ?> <script language="javascript" type="text/javascript">alert("Neuer Dozent erfolgreich erstellt");</script><?php
                } else {
                    var_dump($connection);
                }
            }
        }
    }

    //Einloggen
    function login($connection){
        //Überprüfen ob Name gesetzt ist
        if(!empty($_POST['name'])){
            $name = $_POST['name'];
        } else {
            $_POST['no_name_log'] = true;
            return false;
        }
        //Überprüfen ob Passwort gesetzt ist
        if (isset($_POST['pass']) && !empty($_POST['pass'])){
            $pass = hash("sha256", $_POST['pass']);
        } else {
            $_POST['no_pass_log'] = true;
            return false;
        }
        //Datensatz abrufen
        $query = "SELECT * FROM ted2016_hj_dozent WHERE name ='".$name."';";
        $result = $connection->query($query);

        //Überprüfen ob Passwort übereinstimmt
        if ($result->num_rows==0){
            $_POST['wrong_pass'] = true;
            return false;
        }

        //Dozent-Objekt erstellen
        while($row = $result->fetch_assoc()){

            //Umfragen zu Dozent abrufen
            if ($row['pass'] == $pass){
                $queryUmfrage = "SELECT * FROM ted2016_hj_umfrage WHERE dozent_id =".$row['id']." ORDER BY date DESC;";
                $resultUmfrage = $connection->query($queryUmfrage);

                $umfragen = array();

                //Antworten zu Umfrage abrufen
                while ($rowUmfrage = $resultUmfrage->fetch_assoc()){
                    $queryAntwort = "SELECT * FROM ted2016_hj_antwort WHERE umfrage_id =".$rowUmfrage['id'].";";
                    $resultAntwort = $connection->query($queryAntwort);

                    $antworten = array();

                    //Antwort Array erstellen
                    while ($rowAntwort = $resultAntwort->fetch_assoc()){
                        $antworten[] = new Antwort($rowAntwort['id'], $rowAntwort['antwort'], $rowAntwort['anzahl'], $rowAntwort['umfrage_id']);
                    }

                    //Umfragen Array erstellen
                    $umfragen[]= new Umfrage($rowUmfrage['id'], $rowUmfrage['frage'], $rowUmfrage['dozent_id'], $rowUmfrage['aktiv'], $rowUmfrage['visualisierung'], $rowUmfrage['timer'], $antworten);
                }

                //Dozent-Objekt erstellen
                $_SESSION['user'] = new Dozent($row['id'], $row['name'], $umfragen, $row['link']);
            } else{
                $_POST['wrong_pass'] = true;
                return false;
            }
        }
    }

    //Dozent löschen
    function delete_dozent($connection){
        $query = "DELETE FROM ted2016_hj_dozent WHERE id=".$_SESSION['user']->id.";";

       if ($result = $connection->query($query) === TRUE){
           ?> <script language="javascript" type="text/javascript">alert("Dozent erfolgreich gelöscht");</script><?php
           session_destroy();
           unset($_SESSION);
       }
    }

    //Umfrage hinzufügen
    function addUmfrage($connection){
        //Eingaben überprüfen
        //Bricht ab wenn: Keine Frage eingegeben wurde, weniger als 2 Antworten angegeben wurden, kein Timer gesetzt wurde
        if(empty($_POST['frage'])){
            $_POST['no_frage'] = true;
            return false;
        } elseif (empty($_POST['aw1']) || empty($_POST['aw2'])){
            $_POST['less_2_aw'] = true;
            return false;
        } elseif (empty($_POST['timer'])){
            $_POST['no_timer'] = true;
            return false;
        } else {
            //Frage erstellen
            $frage = $_POST['frage'];

            $visualisierung = $_POST['visualize'];
            $timer = $_POST['timer'];

            //Timer überprüfen
            //Bricht ab wenn Timer < 10 Sekunden ist
            if ($timer < 10){
                $_POST['short_timer'] = true;
                return false;
            } else {
                //In DB schreiben
                $query = "INSERT INTO ted2016_hj_umfrage (frage, aktiv, visualisierung, timer, dozent_id) VALUES ('".$frage."', 0, ".$visualisierung.", ".$timer.", ".$_SESSION['user']->id.");";
                if ($result = $connection->query($query) === TRUE){
                    //Array für Antworten erstellen
                    $aw = array();

                    for ($i = 1; $i <= $_POST['awCount']; $i++){
                        $aw[] = $_POST['aw'.$i];
                    }

                    $umfrage_id = $connection->insert_id;
                    $antworten = array();

                    //Antworten in DB schreiben
                    foreach($aw as $aw_act){
                        if (!empty($aw_act)){
                            $query = "INSERT INTO ted2016_hj_antwort (antwort, anzahl, umfrage_id) VALUES ('".$aw_act."', 0, ".$umfrage_id.");";
                            if ($result = $connection->query($query) === TRUE){
                                $antworten[] = new Antwort($connection->insert_id, $aw_act, 0, $umfrage_id);
                            }
                       }
                    }

                    //Umfrage-Objekt erstellen und Dozenten zuordnen
                    $umfrage = new Umfrage($umfrage_id, $frage, $_SESSION['user']->id, 0, $visualisierung, $timer, $antworten);
                    $_SESSION['user']->umfragen[] = $umfrage;

                    ?> <script language="javascript" type="text/javascript">alert("Neue Umfrage erfolgreich erstellt");</script><?php
                } else {
                    return false;
                }
            }
        }
    }

    //Umfragen anzeigen --> HTML
    function showUmfragen(){

        echo "<h1>Gespeicherte Umfragen:</h1>";

        foreach($_SESSION['user']->umfragen as $umfrage){
            if ($umfrage->aktiv>=0){
                $output = "<div id='umfrage".$umfrage->id."'";
                //Grüne Markierung falls aktiviert
                /* if ($umfrage->aktiv == 1){
                    $output .= " style='background-color:green'";
                }*/
                $output .= ">";
                $output .= "<div class='question'>".$umfrage->frage."</div>";
                //Antworten hinzufügen
                $output .= "<ul class='list'>";
                foreach ($umfrage->antworten as $antwort){
                    $output .= "<li class='answer'>".$antwort->antwort."</li>";
                } $output .= "</ul>";
                //Buttons hinzufügen
                $output .= "<form method='post'><input name='deleteUmfrage' type='hidden' value='".$umfrage->id."' /><input class='button button_pos2' type='submit' value='Löschen'/></form>";
                $output .= "<form method='post'><input name='aktivieren' type='hidden' value='".$umfrage->id."' /><input name='state' type='hidden' value='".$umfrage->aktiv."' /><input name='timer' type='hidden' value='".$umfrage->timer."' /><input class='button button_pos2' type='submit' value='Aktivieren/Deaktivieren'/></form>";
                $output .= "<form method='post' id='showEditUmfrage_".$umfrage->id."'><input name='showEditUmfrage' type='hidden' value='".$umfrage->id."' /><input class='button button_pos2' type='submit' value='Bearbeiten'/></form>";
                $output .= "</div>";
                //Code ausgeben
                echo $output;
            }
        }

        echo "<h1>Vergangene Umfragen:</h1>";

        foreach($_SESSION['user']->umfragen as $umfrage){
            if ($umfrage->aktiv == -1){
                $output = "<div id='umfrage".$umfrage->id."'";
                $output .= ">";
                $output .= "<div class='question'>".$umfrage->frage."</div>";
                //Antworten hinzufügen
                $output .= "<ul class='list'>";
                foreach ($umfrage->antworten as $antwort){
                    $output .= "<li class='answer'>".$antwort->antwort."</li>";
                } $output .= "</ul>";
                //Buttons hinzufügen
                $output .= "<form method='post'><input name='deleteUmfrage' type='hidden' value='".$umfrage->id."' /><input class='button button_pos2' type='submit' value='Löschen'/></form>";
                $output .= "<form method='post' id='showResultUmfrage_".$umfrage->id."'><input name='showResultUmfrage' type='hidden' value='".$umfrage->id."' /><input class='button button_pos2' type='submit' value='Ergebnis anzeigen'/></form>";
                $output .= "</div>";
                //Code ausgeben
                echo $output;
            }
        }
    }

    //Umfrage löschen
    function deleteUmfrage($connection, $id){
        //Variable löschen
        unset($_SESSION['user']->umfragen[$_SESSION['user']->getUmfrageIndex($id)]);
        $_SESSION['user']->umfragen = array_values($_SESSION['user']->umfragen);
        //Aus DB löschen
        $query = "DELETE FROM ted2016_hj_umfrage WHERE id=".$id.";";

        if ($result = $connection->query($query) === TRUE){
            ?> <script language="javascript" type="text/javascript">alert("Umfrage erfolgreich gelöscht");</script><?php
        }
    }

    //Umfrage aktivieren
    function activateUmfrage($connection, $id){
        //Methodenaufruf Klasse Dozent
        $aktiv = $_SESSION['user']->setAktiveUmfrage($id);

        //In DB schreiben (ausgewählte Umfrage)
        $query = "UPDATE ted2016_hj_umfrage SET aktiv=".$aktiv.", date='".date('Y-m-d H:i:s')."' WHERE id=".$id.";";
        if ($result = $connection->query($query) === TRUE){
        }
        //Alle anderen Umfragen deaktivieren
        $query = "UPDATE ted2016_hj_umfrage SET aktiv=0 WHERE id!=".$id." AND aktiv = 1;";
        if ($result = $connection->query($query) === TRUE){
        }
    }

    //"Dozent bearbeiten" Overlay anzeigen
    function showEditDozent(){
        $output = "<div class='input_container_pop'><label>Name:</label><input name='name' type='text' value='".$_SESSION['user']->name."' ";

        if(isset($_POST['no_name_edit']) || isset($_POST['name_exists_edit']) ){
            $output.= "style='background-color:red'";
        }
        $output.="/></div></br>";

        $output .= "<div class='input_container_pop'><label>Altes Passwort:</label><input name='pass_old' type='password'";
        if(isset($_POST['no_pass_old']) || isset($_POST['wrong_pass_old']) ){
            $output.= "style='background-color:red'";
        }
        $output.="/></div></br>";

        $output .= "<div class='input_container_pop'><label>Neues Passwort:</label><input name='pass_new_1' type='password'";
        if(isset($_POST['no_pass_new_1']) || isset($_POST['pass_new_no_match']) || isset($_POST['pass_new_short'])){
            $output.= "style='background-color:red'";
        }
        $output.="/></div></br>";

        $output .= "<div class='input_container_pop'><label>Neues Passwort wiederholen:</label><input name='pass_new_2' type='password'";
        if(isset($_POST['no_pass_new_2']) || isset($_POST['pass_new_no_match']) || isset($_POST['pass_new_short'])){
            $output.= "style='background-color:red'";
        }
        $output.="/></div></br>";

        $output.= "<div class='input_container_pop'><label>Anzeigename:</label><input name='link' type='text' value='".$_SESSION['user']->link."' ";
        if(isset($_POST['no_link_edit']) || isset($_POST['link_exists_edit']) ){
            $output.= "style='background-color:red'";
        }
        $output.="/></div></br>";

        echo $output;
    }

    //Dozent bearbeiten
    function editDozent($con){
        //Eingaben überprüfen
        //Abbruch bei: Name nicht gesetzt, Anzeigename nicht gesetzt
        if (empty($_POST['name'])){
            $_POST['no_name_edit'] = true;
            return false;
        } elseif (empty($_POST['link'])){
            $_POST['no_link_edit'] = true;
            return false;
        } else {
            $name = $_POST['name'];
            $link = $_POST['link'];

            //Abfrage ob Name bereits vergeben
            $query = "SELECT name, link FROM ted2016_hj_dozent WHERE id !=".$_SESSION['user']->id.";";
            $result = $con->query($query);

            //Abbruch bei: Name bereits vergeben, Anzeigename bereits vergeben
            if ($result->num_rows != 0){
                while ($row = $result->fetch_assoc()){
                    if ($row['name'] == $name){
                        $_POST['name_exists_edit'] = true;
                        break;
                    }
                    if ($row['link'] == $link){
                        $_POST['link_exists_edit'] = true;
                        break;
                    }
                }
            }
            if (isset($_POST['name_exists_edit']) || isset($_POST['link_exists_edit'])){
                return false;
            } else{
                //Objektvariablen neu setzen
                $_SESSION['user']->name = $name;
                $_SESSION['user']->link = $link;

                //Datensatz aktualisieren
                $query = "UPDATE ted2016_hj_dozent SET name='".$name."', link='".$link."' WHERE id=".$_SESSION['user']->id.";";
                if ($result = $con->query($query) === TRUE){
                }

                //Eingaben überprüfen
                //Abbruch bei: Passwort 1 oder 2 nicht gesetzt, altes Passwort nicht gesetzt
                if (!empty($_POST['pass_old']) || !empty($_POST['pass_new_2'])){
                    if (empty($_POST['pass_new_1'])){
                        $_POST['no_pass_new_1'] = true;
                    }
                }
                if (!empty($_POST['pass_new_1']) || !empty($_POST['pass_new_2'])){
                    if (empty($_POST['pass_old'])){

                        $_POST['no_pass_old'] = true;
                    }
                }
                if (!empty($_POST['pass_old']) || !empty($_POST['pass_new_1'])){
                    if (empty($_POST['pass_new_2'])){
                        $_POST['no_pass_new_2'] = true;
                    }
                }

                if (isset($_POST['no_pass_old']) || isset($_POST['no_pass_new_1']) || isset($_POST['no_pass_new_2'])){
                    return false;
                } elseif(!empty($_POST['pass_old'])) {

                    //Passwort überprüfen
                    $pass_old = hash("sha256", $_POST['pass_old']);

                    $query = "SELECT pass FROM ted2016_hj_dozent WHERE id=".$_SESSION['user']->id.";";
                    $result=$con->query($query);

                    $row = $result->fetch_assoc();
                    if ($row['pass'] == $pass_old){
                        //Übereinstimmung Passwort 1 und 2 überprüfen
                        if ($_POST['pass_new_1'] == $_POST['pass_new_2']){
                            //Länge Passwort überprüfen
                            if(strlen($_POST['pass_new_1']) >= 7){
                                $pass_new = hash("sha256", $_POST['pass_new_1']);

                                //Datensatz aktualisieren
                                $queryWrite = "UPDATE ted2016_hj_dozent SET pass='".$pass_new."' WHERE id=".$_SESSION['user']->id.";";
                                if ($result = $con->query($queryWrite) === TRUE){
                                }
                            } else {
                                $_POST['pass_new_short'] = true;
                                return false;
                            }
                        } else {
                            $_POST['pass_new_no_match'] = true;
                            return false;
                        }
                    } else {
                        $_POST['wrong_pass_old'] = true;
                        return false;
                    }

                }
            }
        }
    }

    //"Umfrage bearbeiten" Overlay anzeigen
    function showEditUmfrage(){
        //Umfrage finden
        $umfrageIndex = $_SESSION['user']->getUmfrageIndex($_POST['showEditUmfrage']);
        $umfrage = $_SESSION['user']->umfragen[$umfrageIndex];

        //Formular erzeigen
        $output = "<div class='input_container_pop'><label>Frage:</label><input name='frage_edit' type='text' value='".$umfrage->frage."' ";
        //Fehleranzeige falls kein Name gesetzt
        if(isset($_POST['no_frage_edit'])){
            $output.= "style='background-color:red'";
        }
        $output.="/></div>";

        //Antworten-Counter
        $count = 0;
        //Antworten anzeigen
        foreach ($umfrage->antworten as $antwort){
            $count++;
            $output.= "<div class='input_container_pop'><label>Antwort ".$count.": </label><input name='aw_".$count."_edit' id='aw_".$count."_edit' type='text' value='".$antwort->antwort."' ";
            //Fehleranzeige falls weniger als zwei Antworten gesetzt sind
            if(isset($_POST['less_2_aw_edit'])){
                $output.= "style='background-color:red'";
            }
            $output.="/></div>";
        }

        //Antwort-Anzahl hinterlegt
        $output.= "<input name='awCount_edit' id='awCount_edit' value='".$count."' type='hidden' />";

        //Zeit-Auswahl
        $output .= "<div class='input_container_pop'><label>Zeit in Sekunden:</label><input class='timer2' name='timer_edit' type='number' value='".$umfrage->timer."' ";
        //Fehleranzeige falls keine Zeit gesetzt ist oder Zeit zu kurz ist
        if(isset($_POST['no_timer_edit']) || isset($_POST['short_timer_edit'])){
            $output.= "style='background-color:red'";
        }
        $output.="/></div>";

        //Visualisierung-Auswahl
        $output .= "<div class='input_container_pop'><label>Visualisierung:</label><select class='dropdown' name='visualize_edit' size='1'";

        $output.=">";
        //Werte für Visualisierung
        $output.= "<option value='0' ";
        if ($umfrage->visualize ==0){
            $output.= "selected";
        }
        $output.= ">Balken</option>";
        $output.= "<option value='1' ";
        if ($umfrage->visualize == 1){
            $output.= "selected";
        }
        $output.= ">Torten</option>";
        $output.= "</select>";

        //Umfrage Index hinterlegt
        $output.= "<input name='id' value='".$umfrageIndex."' type='hidden' /></div>";

        //Ausgabe
        echo $output;
    }

    //Umfrage bearbeiten
    function editUmfrage($connection){
        //Umfrage-Object laden
        $umfrage=$_SESSION['user']->umfragen[$_POST['id']];

        //Eingaben überprüfen
        //Abbruch bei: keine Frage eingegeben, weniger als 2 Antworten angegeben, keine Zeit gesetzt
        if(empty($_POST['frage_edit'])){
            $_POST['no_frage_edit'] = true;
            $_POST['showEditUmfrage'] = $umfrage->id;
            return false;
        } elseif (empty($_POST['aw_1_edit']) || empty($_POST['aw_2_edit'])){
            $_POST['less_2_aw_edit'] = true;
            $_POST['showEditUmfrage'] = $umfrage->id;
            return false;
        } elseif (empty($_POST['timer_edit'])){
            $_POST['no_timer_edit'] = true;
            $_POST['showEditUmfrage'] = $umfrage->id;
            return false;
        } else {
            $frage = $_POST['frage_edit'];

            $visualisierung = $_POST['visualize_edit'];
            $timer = $_POST['timer_edit'];

            //Eingabe überprüfen
            //Abbruch bei: Zeit < 10 Sek gesetzt
            if ($timer < 10){
                $_POST['short_timer_edit'] = true;
                $_POST['showEditUmfrage'] = $umfrage->id;
                return false;
            } else {
                //Datensatz aktualisieren
                $query = "UPDATE ted2016_hj_umfrage SET frage='".$frage."', visualisierung=".$visualisierung.", timer=".$timer." WHERE id=".$umfrage->id.";";
                if ($result = $connection->query($query) === TRUE){
                    $aw = array();

                    for ($i = 1; $i <= $_POST['awCount_edit']; $i++){
                        $aw[] = $_POST['aw_'.$i.'_edit'];
                    }

                    //Objekt aktualisieren
                    $_SESSION['user']->umfragen[$_POST['id']]->frage = $frage;
                    $_SESSION['user']->umfragen[$_POST['id']]->visualize = $visualisierung;
                    $_SESSION['user']->umfragen[$_POST['id']]->timer= $timer;

                    $antworten = array();

                    //Alte Antworten löschen
                    foreach($umfrage->antworten as $antwort){
                        $query = "DELETE FROM ted2016_hj_antwort WHERE id=".$antwort->id.";";
                        if ($result = $connection->query($query) === TRUE){
                        }
                    }

                    //Neue Antworten setzen
                    //Datenbank aktualisieren
                    foreach($aw as $aw_act){
                        if (!empty($aw_act)){
                            $query = "INSERT INTO ted2016_hj_antwort (antwort, anzahl, umfrage_id) VALUES ('".$aw_act."', 0, ".$umfrage->id.");";
                            if ($result = $connection->query($query) === TRUE){
                                $antworten[] = new Antwort($connection->insert_id, $aw_act, 0, $umfrage->id);
                            }
                        }
                    }
                    //Objekte aktualisieren
                    $_SESSION['user']->umfragen[$_POST['id']]->antworten = $antworten;

                    ?> <script language="javascript" type="text/javascript">alert("Umfrage erfolgreich bearbeitet");</script><?php
                } else {
                    return false;
                }
            }
        }
    }

    //Umfrage deaktivieren
    function umfragenDeaktivieren(){
        //Datensatz aktualisieren
        $query = "UPDATE ted2016_hj_umfrage SET aktiv=0 WHERE aktiv=1;";
        if ($result = $connection->query($query) === TRUE){
            //Objekt aktualisieren
            $_SESSION['user'].unsetAktiveUmfrage();
        }
    }

    //Mit Dozent verbinden (NICHT BENUTZT)
    function connect($con){
        //Aktuelle Frage anzeigen lassen
        $query = "SELECT ted2016_hj_umfrage.frage AS 'frage', ted2016_hj_umfrage.id AS 'umfrage_id', ted2016_hj_umfrage.timer AS 'timer', ted2016_hj_umfrage.aktiv AS 'aktiv', ted2016_hj_dozent.id AS 'dozent_id' FROM ted2016_hj_umfrage JOIN ted2016_hj_dozent On ted2016_hj_umfrage.dozent_id = ted2016_hj_dozent.id WHERE ted2016_hj_dozent.link ='".$_GET['link']."' AND ted2016_hj_umfrage.aktiv=1;";
        $result = $con->query($query);

        while ($row = $result->fetch_assoc()){
            if(!isset($_COOKIE[umfrage_id])){
                setcookie(umfrage_id, 0, time() + (86400 * 30), "/", "localhost");
                echo $row['frage'];
            } else if ($_COOKIE[umfrage_id] == 0){
                echo $row['frage'];
            } else {
                echo "Frage bereits beantwortet";
            }
        }
    }

    //Student beantwortet Umfrage
    function answer($con){
        if(!empty($_POST['aw'])){
            $umfrage_id = $_POST['umfrage_id'];
            $aw = $_POST['aw'];

            $query = "UPDATE ted2016_hj_antwort SET anzahl = anzahl+1 WHERE antwort='".$aw."' AND umfrage_id=".$umfrage_id.";";
            if ($result = $con->query($query) === TRUE){
                setCookie($umfrage_id, 1, time() + (86400 * 30));
            }
        }
    }