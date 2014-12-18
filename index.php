<?php
/* * *AUTOLOAD** */

function loadClass($classname) {
    require $classname . '.class.php';
}

spl_autoload_register('chargerClasse');

/* * *********** */

$db = new PDO('mysql:host=localhost;dbname:quest', 'root', 'root');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

$manager = new PersonnagesManager($db);

if (isset($_POST['creer']) && isset($_POST['nom'])) {
    $perso = new Personnages(array('nom' => $_POST['nom']));

    if (!$perso->nomValide()) {
        $message = 'Le nom choisi est invalide';
        unset($perso);
    } elseif ($manager->exists($perso->nom())) {
        $message = 'Le nom du personnage est déjà pris.';
        unset($perso);
    } else {
        $manager->add($perso);
    }
} elseif (isset($_POST['utilisateur']) && isset($_POST['nom'])) {
    if ($manager->exists($_POST['nom'])) {
        $perso = $manager->get($_POST['nom']);
    } else {
        $message = 'Ce personnage n\'existe pas !';
    }
}
?>
<!DOCTYPE HTML>
<html lang="fr">
    <head>
        <title>The Quest</title>
        <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
    </head>
    <body>
        <p>Nombre de personnages créés : <?php echo $manager->count(); ?></p>
        <?php
        if (isset($message)) {
            echo '<p>', $message, '</p>';
        }
        ?>
        <form action="" method="post">
            <p>
                Nom : <input type="text" name="nom" maxlength="50" />
                <input type="submit" value="Créer ce personnage" name="creer" />
                <input type="submit" value="Utiliser ce personnage" name="utiliser" />
            </p>
        </form>
    </body>
</html>