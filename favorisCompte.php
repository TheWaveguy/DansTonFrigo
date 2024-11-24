<?php
session_start();			//Démarrer les sessions (nécésaire si on utilise les variables de sessions)

// $bdd = new PDO('mysql:host=127.0.0.1;dbname=danstonfrigo', 'root' , '');		//trouver la bdd que j'ai créée sur mysql
require_once('connexionBDD.php');
require_once('component.php');
require_once('FonctionsTom.php');
require_once('fonctionsAffichageResultats.php');
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
				$tabFavoris = recupFavorisCompte($_SESSION['idcompte']);
				if(isset($tabFavoris) AND !empty($tabFavoris)){
					afficheResultRechercheRecette($tabFavoris);
				}
				else{
					echo '<h2> Vous n\'avez pas de favoris </h2>';
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
