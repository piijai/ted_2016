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

class Dozent {
    public $id;
    public $name;
    public $umfragen;
    public $link;

    function __construct($id_new, $name_new, $umfragen, $link){
        $this->id = $id_new;
        $this->name = $name_new;
        $this->umfragen = $umfragen;
        $this->link = $link;
    }

    //Umfrage-Index in Array zurückgeben
    public function getUmfrageIndex($umfrage_id){
        $count = 0;
        foreach($this->umfragen as $umfrage){
            if ($umfrage->id == $umfrage_id){
                return $count;
            } else {
                $count += 1;
            }
        }
    }

    //Umfrage aktivieren
    public function setAktiveUmfrage($umfrage_id){
        $aktivAct = $this->umfragen[$this->getUmfrageIndex($umfrage_id)]->aktiv;

        if ($aktivAct == 0){
            $aktiv = 1;
        } else {
            $aktiv = 0;
        }

        foreach($this->umfragen as $umfrage){
            if ($umfrage->aktiv == 1){
                $umfrage->aktiv = 0;
            }
        }
        $this->umfragen[$this->getUmfrageIndex($umfrage_id)]->aktiv = $aktiv;

        return $aktiv;
    }

    //Aktive Umfrage deaktivieren
    public function unsetAktiveUmfrage(){
        foreach($this->umfragen as $umfrage){
            if ($umfrage->aktiv == 1){
                $umfrage->aktiv = 0;
            }
        }
    }
} 