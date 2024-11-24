<?php
session_start();			//Démarrer les sessions (nécésaire si on utilise les variables de sessions)

// $bdd = new PDO('mysql:host=127.0.0.1;dbname=danstonfrigo', 'root' , '');		//trouver la bdd que j'ai créée sur mysql
require_once('connexionBDD.php');
require_once('component.php');
require_once('FonctionsTom.php');
require_once('fonctionsRecherches.php');
require_once('fonctionsSuppression.php');
require_once('fonctionsInsertions.php');

?>


<html>
	<head>
		<meta charset="UTF-8">
		<title>Mon profil</title>
		<link rel="stylesheet" href="Style_Accueil1.css">
		 <!-- <meta http-equiv="refresh" content="60"> -->			<!-- Auto-refresh -->
	</head>
	<body>
		
		<!-- Header -->
		<div class="header">
			<?php headerPage() ?>
		</div>
		
		<div class="menuGauche">
			<?php SlideBarGauche("SideProfil") ?>
		</div>
		  
		<div class="main">
			<div align="center">
			<?php
				echo '<h2>Profil de '.$_SESSION['nomUtilisateur'].' </h2>
				<br><br>
				Nom d\'utilisateur : '.$_SESSION['nomUtilisateur']. 
				'<br>
				Mail : '.$_SESSION['mail']. 
				'<br>';
				if(isset($_SESSION['idcompte']) AND $_SESSION['idcompte'] == $_SESSION['idcompte'])
					//le "isset($_SESSION['idcompte'])" permet d'obligé d'être connecté -> pas de possibilité de le voir si tu visite le profil sans être connecté
				{
					echo '<a href="editionprofil.php" class="bouton boutonFooter">Editer mon profil</a>
					<a href="deconnexion.php" class="bouton boutonFooter">Se déconnecter</a>';
				}
				if ($_SESSION['admin']){
					echo '<br><a href="Administration.php" class="bouton boutonFooter">Accéder à l\'administration</a>';
				}
				
			?>
			</div>

			<!-- Footer -->
			<div class="footer">
				<?php footer() ?>
			</div>
		  </div>


	</body>
</html>
