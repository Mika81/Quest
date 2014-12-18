<?php

class Personnages{
    private $_id;
    private $_nom;
    private $_degats;
    
    public function getId(){
        return $this->_id;
    }
    public function getNom(){
        return $this->_nom;
    }
    public function getDegats(){
        return $this->_degats;
    }
    
    public function setId($id){
        $id = (int) $id;
        if(strlen($id)<= 5){
            $this->_id = $id;
        }
    }
    public function setNom($nom){
        $this->_nom = $nom;
        if(strlen($nom)<= 24){
            $this->_nom = $nom;
        }
    }
    public function setDegats($degats){
        $this->_degats = $degats;
        if(strlen($degats)<= 3){
            $this->_degats = $degats;
        }
    }
}