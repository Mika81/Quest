<?php

//  Personnages.class.php

class Personnages{
    private $_id;
    private $_nom;
    private $_degats;
    private $_niveau;
    private $_xp;
    private $_puissance;
    private $_lastLogin;
    
    const CEST_MOI = 1;
    const PERSONNAGE_TUE = 2;
    const PERSONNAGE_FRAPPE = 3;
    const RETIRER_DEGATS = 4;
    const NE_PAS_RETIRER_DEGATS = 5;
    
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
        return $perso->recevoirCoup($this->_puissance);
    }
    
    public function recevoirCoup($puissance){
        $this->_degats += $puissance;
        if($this->_degats >= 100){
            return self::PERSONNAGE_TUE;
        }
        return self::PERSONNAGE_FRAPPE;
    }
    
    public function lastLoginDate($perso, $manager){
        $now = time();
//        $twentyFour = $now - 86400;
        $twentyFour = $now - 10; /* Variable for tests */
        $persoLastLogin = $manager->checkLastLogin($perso);
        echo "24h : ".$twentyFour."- last login : ". $persoLastLogin['lastLogin'];
        if($persoLastLogin['lastLogin'] < $twentyFour){
            if($this->_degats > 10){
                $this->_degats -= 10;
            }else{
                $this->_degats = 0;
            }
            return self::RETIRER_DEGATS;
        }else{
            return self::NE_PAS_RETIRER_DEGATS;
        }
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
    public function getLastLogin(){
        return $this->_lastLogin;
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
        $puissance = (int) $puissance;
        if($puissance >=1 && $puissance <= 100){
            $this->_puissance = $puissance;
        }
    }
    public function setLastLogin($lastLogin){
        $time = time();
        $lastLogin = (int) $time;
        $this->_lastLogin = $lastLogin;
    }
}