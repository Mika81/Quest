<?php

// index.php

/* ************************************AUTOLOAD** */
function loadClass($classname) {
    require $classname . '.class.php';
}
spl_autoload_register('loadClass');
/* ********************************************** */


/* ********************************OPEN SESSION** */
session_start();
/* ********************************************** */


/* *Restaure l'objet si la session perso existe * */
if (isset($_SESSION['perso'])) {
    $perso = $_SESSION['perso'];
}
/* ********************************************** */


/* *********************************************DB CONNEXION ** */
$db = new PDO('mysql:host=localhost;dbname=quest', 'root', 'root');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
/* ************************************************************ */


/* ***************Instanciation de la classe PersonnagesManager */
$manager = new PersonnagesManager($db);
/* ************************************************************ */


/* ******************************DESTROY SESSION* */
if (isset($_GET['deconnexion'])) {
    /* Insertion d'un timestamp en base de donn�e pour le personnage courant, 
     * avant le session destroy, afin de sauvegarder sa derni�re connexion */
    $manager->updateLastLogin($perso);
    /* -- */
    session_destroy();
    header('location: .');
    exit();
}
/* ********************************************** */


/* *******conditions pour la création d'un nouveau personnage** */
if (isset($_POST['creer']) && isset($_POST['nom'])) {
    $perso = new Personnages(array('nom' => $_POST['nom']));

    if (!$perso->nomValide()) {
        $message = 'Le nom choisi est invalide';
        unset($perso);
    } elseif ($manager->persoExists($perso->getNom())) {
        $message = 'Le nom du personnage est déjà pris.';
        unset($perso);
    } else {
        $manager->createPerso($perso);
        $manager->updateLastLogin($perso);
    }
} elseif (isset($_POST['utiliser']) && isset($_POST['nom'])) {
    if ($manager->persoExists($_POST['nom'])) {
        $perso = $manager->selectPerso($_POST['nom']);
        $retour = $perso->lastLoginDate($perso, $manager);
        switch ($retour){
            case Personnages::RETIRER_DEGATS :
                $manager->takeOffDamages($perso);
                $message = "Bravo, vous recuperez au maximum 10 de degats !!";
                break;
            case Personnages::NE_PAS_RETIRER_DEGATS :
                $message = "Votre derniere connexion est trop recente pour "
                    . "recuperer des degats!!";
                break;
        }
    } else {
        $message = 'Ce personnage n\'existe pas !';
    }
}
/* ************************************************************* */


/* *************************conditions pour lancer une attaque** */
if(isset($_GET['frapper'])){
    if(!isset($perso)){
        $message = "Créer un personnage ou s'identifier";
    }else{
        $idPersoAFrapper = (int)$_GET['frapper'];
        if(!$manager->persoExists($idPersoAFrapper)){
            echo "Le personnage que vous voulez frapper n'existe pas !!";
        }else{
            $persoAFrapper = $manager->selectPerso($idPersoAFrapper);
            $retour = $perso->frapper($persoAFrapper);
            
            switch ($retour){
                case Personnages::CEST_MOI :
                    $message = "On ne se frappe pas soi-même !!";
                    break;
                case Personnages::PERSONNAGE_FRAPPE :
                    $message = "Le personnage a bien été frappé !!";
                    $manager->modifyPerso($perso);
                    $manager->modifyPerso($persoAFrapper);
                    break;
                case Personnages::PERSONNAGE_TUE :
                    $message = "Le personnage est mort !!";
                    $manager->modifyPerso($perso);
                    $manager->deletePerso($persoAFrapper);
                    break;
            }
        }
    }
}
/* ************************************************************* */
?>

<!DOCTYPE HTML>
<html lang="fr">
    <head>
        <title>The Quest</title>
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
        <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css">
        <link type="text/css" rel="stylesheet" href="css/bootstrap-theme.min.css.min.css">
        <link type="text/css" rel="stylesheet" href="css/cosmo.min.css.min.css">
    </head>
    <body>
        <p>Nombre de personnages créés : <?php echo $manager->countPersos(); ?></p>
        <?php
        if (isset($message)) {
            echo '<p>', $message, '</p>';
        }

        if (isset($perso)) {
            $thisPersoLastLogin = $manager->checkLastLogin($perso);
            ?>
            <p><a href="?deconnexion=1">Déconnexion</a></p>
            <fieldset>
                <legend>Mes informations</legend>
                <p>
                    Nom : <?php echo htmlspecialchars($perso->getNom()); ?><br />
                    Dégâts : <?php echo $perso->getDegats(); ?><br/>
                    Niveau : <?php echo $perso->getNiveau(); ?><br/>
                    Puissance : <?php echo $perso->getPuissance(); ?><br/>
                    Expérience : <?php echo $perso->getXp(); ?><br/>
                    Last Login : <?php echo $thisPersoLastLogin['lastLogin']; ?>
                </p>
            </fieldset>

            <fieldset>
                <legend>Qui frapper ?</legend>
                <p>
                    <?php
                    $persos = $manager->getList($perso->getNom());

                    if (empty($persos)) {
                        echo 'Personne à frapper !';
                    } else {
                        foreach ($persos as $unPerso){
                            
                            echo '<a href="?frapper='. $unPerso->getId(). '">'
                                    . ''. htmlspecialchars($unPerso->getNom()). ''
                                    . '</a> (dégâts : '. $unPerso->getDegats(). ')'
                                    . '(niveau : '. $unPerso->getNiveau(). ')'
                                    . '(puissance : '. $unPerso->getPuissance(). ')'
                                    . '(expérience : '. $unPerso->getXP(). ')'
                                    . '(création le : '. $unPerso->getCreationDate(). ')'
                                    . '<br />';
                        }
                    }
                    ?>
                </p>
            </fieldset>
            <?php
        } else {
            ?>
            <form action="" method="post">
                <p>
                    Nom : <input type="text" name="nom" maxlength="50" />
                    <input type="submit" value="Créer ce personnage" name="creer" />
                    <input type="submit" value="Utiliser ce personnage" name="utiliser" />
                </p>
            </form>
            <?php
        }
        ?>
    </body>
</html>
<?php
if (isset($perso)) {
    $_SESSION['perso'] = $perso;

print"<pre>";
echo"-----------------<br/>";
print_r($_SESSION);
echo"********";
print_r($perso);
echo"-----------------";
print"</pre>";
}