<?php

//  PersonnagesManager.class.php

class PersonnagesManager{
    private $_db;
    
    public function __construct($db){
        $this->setDb($db);
    }
        
    public function createPerso(Personnages $perso){
        $query = $this->_db->prepare('INSERT INTO personnages SET nom = :nom');
        $query->bindValue(':nom', $perso->getNom());
        $query->execute();
        
        $perso->hydrate(array(
            'id' => $this->_db->lastInsertId(),
            'degats' => 0,
        ));
    }
    
    public function modifyPerso(Personnages $perso){
        $query = $this->_db->prepare('UPDATE personnages SET degats = :degats WHERE id= :id');
        $query->bindValue(':degats', $perso->getDegats(), PDO::PARAM_INT);
        $query->bindValue(':id', $perso->getId(), PDO::PARAM_INT);
        
        $query->execute();
    }
    
    public function deletePerso(Personnages $perso){
        $this->_db->query('DELETE FROM personnages WHERE id = "'. $perso->getId() .'"');
    }
    
    public function selectPerso($info){
        if(is_int($info)){
            $query=$this->_db->query('SELECT * FROM personnages WHERE id='. $info);
            $donnees = $query->fetch(PDO::FETCH_ASSOC);
            return new Personnages($donnees);
        }else{
            $query=$this->_db->prepare('SELECT * FROM personnages WHERE nom= :nom');
            $query->execute(array(':nom'=>$info));
            $donnees = $query->fetch(PDO::FETCH_ASSOC);
            return new Personnages($donnees);
        }
    }
    
    public function countPersos(){
        return $this->_db->query('SELECT COUNT(*) FROM personnages')->fetchColumn();
    }
    
    public function getList($nom){
        $persos = array();
        
        $query=$this->_db->prepare('SELECT * FROM personnages WHERE nom <> :nom ORDER BY nom');
        $query->execute(array(':nom' => $nom));
        while($donnees = $query->fetch(PDO::FETCH_ASSOC)){
            $persos[] = new Personnages($donnees);
        }
        
        return $persos;
    }
    
    public function persoExists($info){
        if(is_int($info)){
            return (bool) $this->_db->query('SELECT COUNT(*) FROM personnages WHERE id="'. $info .'"')->fetchColumn();
        }
        $checkName = $this->_db->prepare('SELECT COUNT(*) FROM personnages WHERE nom= :nom');
        $checkName->execute(array(':nom' => $info));
        return (bool) $checkName->fetchColumn();
    }
    
    public function setDb(PDO $db){
        $this->_db = $db;
    }
}