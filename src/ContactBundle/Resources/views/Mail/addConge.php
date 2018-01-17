<?php
    include '../lib/app/init.php';
    /**
     * Initialisation
     */
    use Lib\Utilisateur;
    use Lib\Tool;
    use Lib\Suivis;
    use Lib\Agenda;
    use Lib\Upload;
    use Lib\Conge;
    use Lib\Mail;

    Utilisateur::ifConnect();
 ?>
 
 <!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width; initial-scale=1;">
    <title><?= TITLEBACK ?></title>
    <link rel="icon" type="image/png" href="<?= BASEFRONT ?>img/layout/favicon.png">
    <link href="<?= BASEFRONT ?>js/scroll/scroll.css" rel="stylesheet" type="text/css">
    <link href="<?= BASEFRONT ?>js/datepicker/datepicker.css" rel="stylesheet" type="text/css">
    <link href="<?= BASEFRONT ?>css/app.css" rel="stylesheet" type="text/css">

    <!--[if lt IE 9]>
        <script src="<?= BASEFRONT ?>js/html5.js"></script>
    <![endif]-->
</head>

<body>
<?php   
    $succes = array();
    $erreur = array();

    $type = '';

    $justificatif = '';
    $explication = '';
    $nombre_jour = 0;

    $satisfaction = '';
    $relationnel = '';
    $degresconformite = '';

    $reponse_clear = '';
    $reponseMatin = '';
    $reponseAprem = '';

    $annee = date('Y');
    $mois = (int)date('m');

    $etat = 0;
    $show_user = false;
    $lockDays = 1;

    if(isset($_GET['utilisateur']))
        $utilisateurId = $_GET['utilisateur'];
    else
        $utilisateurId = Utilisateur::getCurrentUtilisateur()->utilisateurId;

    if(isset($_GET['Ajout']) &&  in_array($_SESSION['role']['id'], array(1, 2, 3, 4, 5)) )
    {
        $show_user = true;
    }

    if(isset($_GET['Ajout']) &&  in_array($_SESSION['role']['id'], array(1, 2, 3, 4)) )
    {
        $lockDays = 0;
    }

    /**
     * Formulaire
     */
    if(isset($_POST['add'])){

        $mois = $_POST['mois'];
	    $annee = $_POST['annee'];

	    $type = $_POST['type_selected'];
	    $nombre_jour = $_POST['nombre_jour'];
        if((float)$nombre_jour <= 0)
        {
	        Tool::setFlash('Impossible d\'envoyer une demande de congés ayant 0 demi-journée demandées','erreur');
            header('location:' . BASEFRONT . 'conge/addConge.php');
            exit;
        }

	    $explication = htmlspecialchars(addslashes($_POST['explication']));

        if($type == 'AU' && (!isset($explication) || $explication == ""))
	        array_push($erreur, 'Veuillez entrer une explication sur la nature de votre absence. (Obligatoire si le type d\'absence est \'Autres\')');

	    if(isset($_POST['degresconformite']))
	        $degresconformite = htmlspecialchars(addslashes($_POST['degresconformite']));

	    if(isset($_FILES) && isset($_FILES['justificatif']) && $_FILES['justificatif']['tmp_name'])
	    {
		    if ($_FILES['justificatif']['error'] === UPLOAD_ERR_OK){
			    $justificatif = Upload::postFichier($_FILES['justificatif'],'L\'image',5000000,array('pdf','word','jpg','jpeg','doc','docx'),array());
		    }else
		    {
			    if($_FILES['justificatif']['error'] != UPLOAD_ERR_NO_FILE)
				    array_push($erreur, 'Impossible d\'upload le fichier.');
		    }
	    }

	    $reponseMatin = $_POST['reponseMatin'];
	    $reponseAprem = $_POST['reponseAprem'];

	    if($show_user)
	        $utilisateurId = $_POST['utilisateurId'];

        /**
         * Si aucune erreur alors
         */
        if(empty($erreur)){

	        $sql = $bdd->prepare("INSERT INTO `conge_demande`
                            (`congeDemandeCreated`, `congeDemandeAnnee`, `congeDemandeMois`,  `congeDemandeUtilisateurId`, `congeDemandeNombreJour`,
                             `congeDemandeTypeConge`, `congeDemandeJustificatif`, `congeDemandeExplication`,
                             `congeDemandeDetailMatin`, `congeDemandeDetailAprem`, `congeDemandeEtat` )
                             VALUES
                             (:created, :annee, :mois, :utilisateur, :nombre_jour,
                              :type, :justificatif, :explication,
                              :matin , :aprem, :etat) ");

            $sql->execute(array(
                'created' => Tool::dateTime('Y-m-d H:i:s'),
                'annee' => $annee,
                'mois' => $mois,
                'utilisateur' => $utilisateurId ,
                'nombre_jour' => $nombre_jour ,
                'type' => $type ,
                'justificatif' => $justificatif ,
                'explication' => $explication ,
                'matin' => $reponseMatin,
                'aprem' => $reponseAprem,
                'etat' => 1
            ))or die(print_r($sql->errorInfo()));

            if(isset($justificatif) && $justificatif != "")
                move_uploaded_file($_FILES['justificatif']['tmp_name'], '../file/justificatif/'.$justificatif);

	        /* Envoie d'un mail à son responsable */
	        $email_reponsable = Utilisateur::getInfoUtilisateur(Utilisateur::getInfoUtilisateur($utilisateurId)->utilisateurResponsableHierarchique)->utilisateurEmail;
	        if($email_reponsable && $email_reponsable != "")
	        {

		        $nom_collaborateur = Utilisateur::getInfoUtilisateur($utilisateurId)->utilisateurNom;
		        $prenom_collaborateur = Utilisateur::getInfoUtilisateur($utilisateurId)->utilisateurPrenom;

		        Mail::sendSimpletHtml(
			        'Nouvelle demande de congés ',
			        array(EMAILNOREPLY),
			        array($email_reponsable),
			        'Nouvelle demande de congés ',
			        'Vous avez une nouvelle demande de congés à traiter pour : '.$nom_collaborateur.' '.$prenom_collaborateur.'<br/>Type de congé : '.$type.'<br/>Nombre de jour(s) : '.$nombre_jour
		        );
	        }

	        /* Envoie d'un mail à l'émail à notifier */
	        $email_a_notifier = Utilisateur::getInfoUtilisateur($utilisateurId)->utilisateurEmailNotification;
	        if($email_a_notifier && $email_a_notifier != "" && !empty($email_a_notifier) && filter_var($email_a_notifier, FILTER_VALIDATE_EMAIL))
	        {
		        Mail::sendSimpletHtml(
			        'Nouvelle demande de congés ',
			        array(EMAILNOREPLY),
			        array($email_a_notifier),
			        'Nouvelle demande de congés ',
                    'Vous avez une nouvelle demande de congés à traiter pour : '.$nom_collaborateur.' '.$prenom_collaborateur.'<br/>Type de congé : '.$type.'<br/>Nombre de jour(s) : '.$nombre_jour

		        );
	        }

            /**
             * Reset des variables
             */
	        $nombre_jour = 0;
	        $type = '';
	        $justificatif = '';
	        $explication = '';
	        $annee = date('Y');
	        $mois = (int)date('m');
	        $reponseMatin = '';
            $reponseAprem = '';

            Tool::setFlash('Demande de congés envoyée avec succès ! Elle sera soumise à validation.');
	        if(isset($_GET['step']))
	        {
		        if($_GET['step'] == 'gestion')
		        {
			        header('location:' . BASEFRONT . 'conge/managerConge.php?etat=1');
			        exit;
		        }
		        if($_GET['step'] == 'supervision')
		        {
			        header('location:' . BASEFRONT . 'conge/managerCongeSupervision.php?etat=1');
			        exit;
		        }
	        }

	        header('location:'.BASEFRONT.'conge/mesDemandes.php');
	        exit();
        }
    }



?>

    <main id="main">

        <?php
            include '../include/menu.php';
        ?>

        <div id="container">

            <?php
                include '../include/header.php';
            ?>

            <div id="contentTitre">
                <?php
                if(isset($_GET['step']) && ($_GET['step'] == 'gestion' || $_GET['step'] == 'supervision') )
                    echo '<h1>Ajouter une demande de congés pour un employé</h1>';
                else
                    echo '<h1>Faire une demande de congés</h1>';
                ?>
            </div>

            <div id="content">

                <?php
                    if(!empty($erreur)){ Tool::getMessage($erreur, 'erreur'); }
                    if(!empty($succes)){ Tool::getMessage($succes, 'succes'); }
                ?>
	            <?= Tool::getFlash() ?>

                <form action="#" id="form_addConge" data-user-informer="0" data-user-info-regle="0" method="post" enctype="multipart/form-data">

                    <div class="zone_bleu">

                        <div class="colonne_double">
                            <?php
                            if($show_user)
                            {
                                ?>
                                <label>Employé concerné *</label>
                                <select name="utilisateurId" onchange="changeMois('en_cours',$(this));" data-url="<?= BASEFRONT?>conge/tableau_Conge.php" id="utilisateurId" class="form-elem big">
                                    <option value="<?= Utilisateur::getCurrentUtilisateur()->utilisateurId ?>">Veuillez choisir l'employé</option>
                                    <?php
                                    $all_managed = explode(',',Utilisateur::getListeUtilisateurManager());
                                    $all_managed_liste = "";
                                    foreach ($all_managed as $managed)
                                    {
                                        if($managed)
                                            $all_managed_liste .= $managed.',';
                                    }
                                    $all_managed_liste = substr($all_managed_liste,0,-1);

                                    switch ($_SESSION['role']['id'])
                                    {
                                        case 3:
                                        case 4:
                                            if(isset($_GET['Ajout']) && $_GET['Ajout'] == '2') // Si on est en Gestion
                                                $requete_all_users = "SELECT utilisateurNom, utilisateurPrenom, utilisateurEmail, utilisateurId FROM utilisateur WHERE utilisateurEtat = 1 AND utilisateurRole > 1  AND (utilisateurAgenceId = '" . Utilisateur::getCurrentUtilisateur()->utilisateurAgenceId . "' )  AND (utilisateurId IN ('" . $all_managed_liste . "') ) ORDER BY utilisateurRole ASC , utilisateurNom ASC;";
                                            if(isset($_GET['Ajout']) && $_GET['Ajout'] == '1') // Si on est en Supervision
                                            {
	                                            // on check SI utilisateur à accès à une ou des autres agences :
	                                            $liste_autre_agence = Utilisateur::verifieGestionAgences();
	                                            $requete_all_users = "SELECT utilisateurNom, utilisateurPrenom, utilisateurEmail, utilisateurId FROM utilisateur WHERE utilisateurEtat = 1 AND utilisateurRole > 1  AND (utilisateurAgenceId IN (" . $liste_autre_agence . ") ) ORDER BY utilisateurRole ASC , utilisateurNom ASC;";
                                            }
                                        break;
                                        case 5:
                                            $requete_all_users = "SELECT utilisateurNom, utilisateurPrenom, utilisateurEmail, utilisateurId FROM utilisateur WHERE utilisateurEtat = 1 AND utilisateurRole > 1  AND (utilisateurId IN ('" . $all_managed_liste . "') ) ORDER BY utilisateurRole ASC , utilisateurNom ASC;";
                                            break;
                                        default:
                                            $requete_all_users = "SELECT utilisateurNom, utilisateurPrenom, utilisateurEmail, utilisateurId FROM utilisateur WHERE utilisateurEtat = 1 AND utilisateurRole > 1  ORDER BY utilisateurRole ASC , utilisateurNom ASC;";
                                            break;
                                    }

                                    $sql_all_user = $bdd->query($requete_all_users);
                                    if(!$sql_all_user ||  $sql_all_user->rowCount() == 0) { }
                                    else
                                    {
                                        while($data_user = $sql_all_user->fetchObject()) {
                                            if($data_user->utilisateurId == $utilisateurId)
                                                echo'<option value="'.$data_user->utilisateurId.'" selected>'.$data_user->utilisateurNom.' '.$data_user->utilisateurPrenom.' - '.$data_user->utilisateurEmail.' </option>';
                                            else
                                                echo'<option value="'.$data_user->utilisateurId.'">'.$data_user->utilisateurNom.' '.$data_user->utilisateurPrenom.' - '.$data_user->utilisateurEmail.' </option>';

                                        }
                                    }
                                    ?>
                                </select><br>
                                <input type="hidden" id="lockDays" name="lockDays" value="<?= $lockDays ?>">
                                <?php
                            }
                            ?>

                            <label>Type de congés *</label>
                            <select id="type" name="type" title="Type de congé (obligatoire)" class="form-elem big">
                                <option value="" <?php if($type == '') echo 'selected'; ?>>Veuillez choisir le type de congés</option>
								<option value="CP" <?php if($type == 'CP') echo 'selected'; ?> >Congé Payé</option>
								<option value="CSS" <?php if($type == 'CSS') echo 'selected'; ?> >Congé Sans Solde</option>
								<option value="AM" <?php if($type == 'AM') echo 'selected'; ?> >Arrêt Maladie</option>
								<option value="AT" <?php if($type == 'AT') echo 'selected'; ?> >Accident du Travail</option>
								<option value="EF" <?php if($type == 'EF') echo 'selected'; ?> >Evénement Familial</option>
								<option value="JR" <?php if($type == 'JR') echo 'selected'; ?> >Journée Récupérée</option>
								<option value="CM" <?php if($type == 'CM') echo 'selected'; ?> >Congé Maternité</option>
								<option value="CPat" <?php if($type == 'CPat') echo 'selected'; ?> >Congé Paternité</option>
								<option value="VM" <?php if($type == 'VM') echo 'selected'; ?> >Visite Médicale</option>
								<option value="FO" <?php if($type == 'FO') echo 'selected'; ?> >Formation</option>
								<option value="AU" <?php if($type == 'AU') echo 'selected'; ?> >Autres</option>
                                <option value="RTT" <?php if($type == 'RTT') echo 'selected'; ?> >RTT</option>								
                            </select><br>
                            <input type="hidden" id="type_selected" name="type_selected" value="<?= $type?>" class="form-elem big">


                            <label>Justificatif</label>
                            <?= $justificatif ?>
                            <input type="file" name="justificatif" class="form-elem big">
                            <div class="form-legende">
                                Poids maximum : 5Mo<br>
                                Format : pdf, doc, docx, jpeg, jpg
                            </div>
                        </div>

                        <div class="colonne_double">
                            <label>Explication</label>
                            <textarea name="explication" class="form-elem big" ><?= $explication ?></textarea>
                        </div>

                    </div>


                    <input type="hidden" name="nombre_jour" value="<?= $nombre_jour ?>" class="nombre_jour form-elem big">
                    <div class="bande_top_tableau">
                        <div class="gauche">Nombre de jours demandés
                            <div class="nb_jour_visu"><?= $nombre_jour ?></div>
                            <div class="hachurer"></div>Les jours de congés concernés par cette demande sont hachurés
                        </div>
                    </div>
                    <div class="zone_bleu">
                        <?php Conge::afficheCongeUtilisateur($utilisateurId,$mois,$annee,0,false,1,true); ?>

                    </div>
                    <div class="clear"></div>

                    <button name="add" type="submit" class="form-submit turquoise medium addConge" data-url="<?= BASEFRONT ?>include/checkCongeAttente.php"><i class="fa fa-check"></i> Envoyer votre demande</button>


                </form>



                </div>
            </div>

        </div>

    </main>

    <script type="text/javascript" src="<?= BASEFRONT ?>js/jquery/jquery.js"></script>
    <script type="text/javascript" src="<?= BASEFRONT ?>js/jquery/jquery-ui.js"></script>
    <script type="text/javascript" src="<?= BASEFRONT ?>js/scroll/scroll.js"></script>
    <script type="text/javascript" src="<?= BASEFRONT ?>js/tinymce/tinymce.min.js"></script>
    <script type="text/javascript" src="<?= BASEFRONT ?>js/sweetalert2/sweetalert2.min.js"></script>
    <script type="text/javascript" src="<?= BASEFRONT ?>js/app.js"></script>

  


</body>
</html>
