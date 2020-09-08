<?php
/**
 * Created by PhpStorm.
 * User: ExVision
 * Date: 17.05.16
 * Time: 11:04
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

class Umfrage {
    public $id;
    public $frage;
    public $dozent;
    public $aktiv;
    public $visualize;
    public $timer;
    public $antworten;

    function __construct($id_new, $frage_new, $dozent_id_new, $aktiv_new, $visualize_new, $timer_new, $antworten){
        $this->id = $id_new;
        $this->frage = $frage_new;
        $this->dozent = $dozent_id_new;
        $this->aktiv = $aktiv_new;
        $this->visualize = $visualize_new;
        $this->timer = $timer_new;
        $this->antworten = $antworten;
    }
} 