<?php

//  Personnages.class.php

class Personnages{
    private $_id;
    private $_nom;
    private $_degats;
    private $_niveau;
    private $_xp;
    private $_puissance;
    
    const CEST_MOI = 1;
    const PERSONNAGE_TUE = 2;
    const PERSONNAGE_FRAPPE = 3;
    
    public function __construct(array $donnes){
        $this->hydrate($donnes);
    }
    
    public function hydrate(array $donnees){
        foreach($donnees as $key => $value){
            $method = 'set'.ucfirst($key);
            if(method_exists($this, $method)){
                $this->$method($value);
            }
        }
    }
    
    public function nomValide(){
        return !empty($this->_nom);
    }
    
    public function frapper(Personnages $perso){
        if ($perso->getId() == $this->_id){
            return self::CEST_MOI;
        }
        $this->_puissance = (floor(($this->_niveau)/2)+5);
        $this->_xp += $this->_niveau;
        if($this->_xp >= 100){
            $this->_xp = 0;
            $this->_niveau += 1;
        }
        return $perso->recevoirCoup(($this->_puissance)+1);
    }
    
    public function recevoirCoup($puissance){
        $this->_degats += $puissance;
        if($this->_degats >= 100){
            return self::PERSONNAGE_TUE;
        }
        return self::PERSONNAGE_FRAPPE;
    }
    
    public function getId(){
        return $this->_id;
    }
    public function getNom(){
        return $this->_nom;
    }
    public function getDegats(){
        return $this->_degats;
    }
    public function getNiveau(){
        return $this->_niveau;
    }
    public function getXp(){
        return $this->_xp;
    }
    public function getPuissance(){
        return $this->_puissance;
    }
    
    public function setId($id){
        $id = (int) $id;
        if(strlen($id)<= 5){
            $this->_id = $id;
        }
    }
    public function setNom($nom){
        if(strlen($nom)<= 24 && is_string($nom)){
            $this->_nom = $nom;
        }
    }
    public function setDegats($degats){
        $degats = (int) $degats;
        if(strlen($degats)<= 3){
            if($degats >= 0 && $degats <= 100){
                $this->_degats = $degats;
            }
        }
    }
    public function setNiveau($niveau){
        $niveau= (int) $niveau;
        if($niveau >=1 && $niveau <= 100){
            $this->_niveau = $niveau;
        }
    }
    public function setXp($xp){
        $xp= (int) $xp;
        if($xp >=0 && $xp <= 100){
            $this->_xp = $xp;
        }
    }
    public function setPuissance($puissance){
        $puissance= (int) $puissance;
        if($puissance >=1 && $puissance <= 100){
            $this->_puissance = $puissance;
        }
    }
}