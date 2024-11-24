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

<html lang="fr">
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
				
				if(isset($_POST['ajoutRecetteAgenda'])){
					if(isset($_POST['choixDate']) && isset($_POST['choixMidi']) && isset($_POST['choixRecette'])){
						$idrecette = rechercheIdRecette($_POST['choixRecette']);
						if($idrecette!=NULL){
							if(checkRecetteDejaUtiliseAgenda($_SESSION['idcompte'], $_POST['choixDate'], $_POST['choixMidi'], $_POST['choixRecette'])){
								if(checkPlaceRecetteAgenda($_SESSION['idcompte'], $_POST['choixDate'], $_POST['choixMidi'])){
									insertionRecetteAgenda($_SESSION['idcompte'], $_POST['choixDate'], $_POST['choixMidi'], $idrecette);
									echo 'Recette ajoutée avec succès';
								}
								else echo 'Plus assez de place sur cet horaire de la journée';
							}
							else echo 'Recette déja présente sur cet horaire de la journée';
						}
						else echo 'Recette inexistante';
					}
					else{
						echo 'Erreur de récupération des données de la recette à ajouter à l\'emploi du temps';
					}
				}
				
				echo '<h2> Agenda </h2>';
				
				echo '<div id="conteneurFiltreDate"><form method="post" action="agendaRecettes.php" id="rechercheDate">
						<label for="choixDateFiltre"> Rechercher une date :</label>
						<input type="date" value="2023-06-10" name="choixDate" id="choixDate" required>
						<input type="submit" value="Rechercher" name="rechercheJournee" class="bouton">
					</form></div>';
				
				if(isset($_POST['rechercheJournee']) && isset($_POST['choixDate']) && !empty($_POST['choixDate'])){ //regarde si il y eu une recherche de journée ou juste un affichage de tout
					$tabjournees[]['dateJournee'] = $_POST['choixDate'];
				}
				else{
					$tabjournees = recupjourneesEDT($_SESSION['idcompte']);		
				}
				
				if(isset($tabjournees) AND !empty($tabjournees)){
					echo '<div class="containerAgenda">';
					foreach($tabjournees as $key => $journee){
						
						$date = $journee['dateJournee']." 08:00:00";
						$dt = \DateTime::createFromFormat( "Y-m-d H:i:s", $date );
						
					// Date en français
						$semaine = array(1=>" Lundi "," Mardi "," Mercredi "," Jeudi ",
						" vendredi "," samedi ", " Dimanche ");
						$mois =array(1=>" janvier "," février "," mars "," avril "," mai "," juin ",
						" juillet "," août "," septembre "," octobre "," novembre "," décembre ");
						echo '<div class="itemAgenda"><p class="dateJournee">'.$semaine[$dt->format('N')] ," ",$dt->format('d'),"", $mois[$dt->format('n')], $dt->format('Y').'</p><br>';
						
						$tabRecettes = renvoieRecettesViaJournee($journee['dateJournee'], TRUE);
						
						echo '<div class="conteneurRecettes"><hr>
							<h2>Recettes du midi</h2>
							<div class="partieMidi">';
						if(!empty($tabRecettes)){
							afficheResultRecettesAgenda($tabRecettes);
						}
						else{
							echo '<p>Pas de recettes pour le midi</p>';
						}
						
						
						$tabRecettes = renvoieRecettesViaJournee($journee['dateJournee'], FALSE);
						echo '</div>
							<hr>
							<h2>Recettes du soir</h2>
							<div class="partieSoir">';
						if(!empty($tabRecettes)){
							afficheResultRecettesAgenda($tabRecettes);
						}
						else{
							echo '<p>Pas de recettes pour le soir</p>';
						}
						
						echo '</div>
							</div>
						</div>';
					}
					echo '</div>';
				}
				else{
					echo '<p> Vous n\'avez pas encore d\'agenda</p>';
				}

				
				
				// formulaire d'ajout de recette dans l'agenda
				echo '<form method="post" action="agendaRecettes.php" class="conteneurAjoutAgenda">
				
						<div id="boxChoixDate"> 
							<label for="choixDate" > Choisir la date : </label>
							<input type="date" value="2023-06-10" name="choixDate" id="choixDate" required>
						</div>
						
						<div id="boxChoixMidi">
							<label for="midi">Midi</label>
							<input type="radio" name="choixMidi" id="midi" value="1" checked>
							<label for="soir">Soir</label>
							<input type="radio" name="choixMidi" id="soir" value="0">
						</div>
						
						<div id="boxChoixRecette">
							<input type="text" name="choixRecette" placeholder="Choisissez une recette" required>
						</div>
						
						<div id="boxValidation">
							<input type="submit" value="Ajouter à l\'agenda" name="ajoutRecetteAgenda" class="bouton">
						</div>
					</form>';
					
					/*
						<form method="POST" action="Accueil.php" >
							<input type="search" name="rechingr" class="centrage saisieIngredient" size="20" placeholder=" Rechercher un ingrédient...">
						</form>
					*/
			
			
			?>
			</div>

			<!-- Footer -->
			<div class="footer">
				<?php footer() ?>
			</div>
		  </div>


	</body>
</html>


