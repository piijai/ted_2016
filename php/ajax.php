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

    //SERVERVERBINDUNG
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

include("dozent.php");
include("umfrage.php");
include("antwort.php");
include("functions.php");
session_start();

    //Bei Verbindung User -> Dozent
    if (isset($_GET['link'])){
        $query = "SELECT ted2016_hj_umfrage.aktiv, ted2016_hj_umfrage.id AS 'umfrage_id', ted2016_hj_umfrage.frage, ted2016_hj_umfrage.timer, ted2016_hj_umfrage.date AS 'date' FROM ted2016_hj_umfrage JOIN ted2016_hj_dozent On ted2016_hj_umfrage.dozent_id = ted2016_hj_dozent.id WHERE ted2016_hj_dozent.link ='".$_GET['link']."';";
        $result = $connection->query($query);

        while ($row = $result->fetch_assoc()){
            if ($row['aktiv'] == 1){
                $queryAw = "SELECT * FROM ted2016_hj_antwort WHERE umfrage_id=".$row['umfrage_id'];
                $resultAw = $connection->query($queryAw);

                $antworten = array();

                while ($rowAw = $resultAw->fetch_assoc()){
                    $antworten[] = $rowAw;
                }

                $row['antworten'] = $antworten;

                $_POST['frage_aktiv'] = $row['umfrage_id'];
                $_SESSION['start_time'] = time();
                echo json_encode($row);
            }
        }
    }

    //Bei Ablauf des Timers
    if (isset($_GET['umfrage_id'])){
        unset($_POST);
        //Datenbank aktualisieren
        $query = "UPDATE ted2016_hj_umfrage SET aktiv=0 WHERE aktiv = 1";
        if ($result = $connection->query($query) === TRUE){
        }

        //Objekt aktualisieren
        $_SESSION['user']->unsetAktiveUmfrage();

        $query2 = "SELECT ted2016_hj_antwort.id, ted2016_hj_antwort.antwort AS 'antwort' , ted2016_hj_antwort.anzahl AS 'anzahl', ted2016_hj_umfrage.frage AS 'frage', ted2016_hj_umfrage.visualisierung AS 'visualisierung' FROM ted2016_hj_antwort  JOIN ted2016_hj_umfrage ON ted2016_hj_antwort.umfrage_id = ted2016_hj_umfrage.id WHERE ted2016_hj_antwort.umfrage_id =".$_GET['umfrage_id'].";";
        $result = $connection->query($query2);

        $ergebnis = [];

        while ($row = $result->fetch_assoc()){
            $ergebnis[] = $row;
        }

        $query3a = "SELECT * FROM ted2016_hj_umfrage WHERE id =".$_GET['umfrage_id'].";";
        $result3a = $connection->query($query3a);

        while ($row3a = $result3a->fetch_assoc()){
            $copyRead[] = $row3a;
        }

        $newID;
        $query3b = "INSERT INTO ted2016_hj_umfrage (frage, dozent_id, aktiv, visualisierung, timer) VALUES ('".$copyRead[0]['frage']."', ".$_SESSION['user']->id.", 0, ".$copyRead[0]['visualisierung'].", ".$copyRead[0]['timer'].");";
        if ($result = $connection->query($query3b) === TRUE){
            $newID = $connection->insert_id;
        }

        $newAws = [];

        foreach($ergebnis as $rowAwAlt){
            $query5 = "INSERT INTO ted2016_hj_antwort (antwort, anzahl, umfrage_id) VALUES ('".$rowAwAlt['antwort']."', 0, ".$newID.");";
            if ($result = $connection->query($query5) === TRUE){
                $awAct = new antwort($connection->insert_id, $rowAwAlt['antwort'], 0, $newID);
                $newAws[] = $awAct;
            }
        }

        $copy = new Umfrage($newID, $copyRead[0]['frage'], $_SESSION['user']->id, 0, $copyRead[0]['visualisierung'], $copyRead[0]['timer'], $newAws);
        $_SESSION['user']->umfragen[]=$copy;
        $_SESSION['user']->umfragen[$_SESSION['user']->getUmfrageIndex($_GET['umfrage_id'])]->aktiv=-1;


        $query6 = "UPDATE ted2016_hj_umfrage SET aktiv=(-1) WHERE id=".$_GET['umfrage_id'].";";
        if ($result = $connection->query($query6) === TRUE){
        }

        echo json_encode($ergebnis);
    }

    if (isset($_GET['umfrage_id_ergebnis'])){
        $query = "SELECT ted2016_hj_antwort.antwort AS 'antwort' , ted2016_hj_antwort.anzahl AS 'anzahl', ted2016_hj_umfrage.frage AS 'frage', ted2016_hj_umfrage.visualisierung AS 'visualisierung' FROM ted2016_hj_antwort  JOIN ted2016_hj_umfrage ON ted2016_hj_antwort.umfrage_id = ted2016_hj_umfrage.id WHERE ted2016_hj_antwort.umfrage_id =".$_GET['umfrage_id_ergebnis'].";";
        $result = $connection->query($query);

        $ergebnis = [];

        while ($row = $result->fetch_assoc()){
            $ergebnis[] = $row;
        }

        echo json_encode($ergebnis);
    }

    //Fenster schließen --> Umfrage deaktivieren
    if (isset($_GET['action']) && $_GET['action'] = "umfragenDeaktivieren"){
        $query = "UPDATE ted2016_hj_umfrage SET aktiv=0 WHERE aktiv=1;";
        if ($result = $connection->query($query) === TRUE){
            //Objekt aktualisieren
        }
        $_SESSION['user'].unsetAktiveUmfrage();
    }