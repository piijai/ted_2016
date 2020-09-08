<?php
/**
 * Created by PhpStorm.
 * User: ExVision
 * Date: 17.05.16
 * Time: 11:15
 */


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


class antwort {
    public $id;
    public $antwort;
    public $anzahl;
    public $umfrage;

    function __construct($id, $antwort, $anzahl, $umfrage_id){
        $this->id = $id;
        $this->antwort = $antwort;
        $this->anzahl = $anzahl;
        $this->umfrage = $umfrage_id;
    }
} 