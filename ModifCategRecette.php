<?php
session_start();
if(isset($_SESSION['user']) AND !empty($_SESSION['user']))
{
	$connecte = true;
}

require_once('component.php');

require_once('affichageTabResult.php');
require_once('connexionBDD.php');
require_once('fonctionsInsertions.php');
require_once('fonctionsRecherches.php');
require_once('affichageForm.php');
require_once('fonctionsSuppression.php');
require_once('fonctionsModif.php');
?>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<title>Modification des catégories de recette</title>
		<link href="Style_Accueil1.css" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
	</head>

	<body>
		
		<!-- Header -->
		<div class="header">
			<?php HeaderPage(); ?>
		</div>

		<!-- Side -->
		<div class="menuGauche">
		 	<?php SlideBarGauche("Side2"); ?>
		</div>
		  
		  
		<!-- main -->  
		  <div class="main">
			
			<form id="formulaire" action="ModifCategRecette.php" method="post">
				<div class="block"><p><label>Cherchez une catégorie de recette : <input type="text" name="nomCategorie" class="input-res" size='50' maxlength='30'></label></p></div>

				<input type="submit" name='boutonRecherche' class="bouton" value="Rechercher">
				<input type="submit" name='boutonAjoutCateg' class="bouton" value="Ajouter une catégorie de recette">
			</form>
			<hr>
			<?php
				if ( isset( $_POST ) && (count( $_POST ) >= 1) && ( isset($_POST['boutonRecherche']))){ 
					//ce test regarde si on a fait la 1ere recherche de la page
					afficheCategRecetteModifSupp($_POST['nomCategorie']); 
				}
				
				elseif ( isset( $_POST ) && (count( $_POST ) >= 1) && isset($_POST["boutonModifier"])){
					// check de si l'ustensile avec les nouvelles infos existe déja ou pas, si non lance les modifications
					$_POST['nomCategApres'] = trim($_POST['nomCategApres']);
					if($_POST["nomCategAvant"] != $_POST["nomCategApres"]){
						if (rechercheIdCategRecette($_POST["nomCategApres"]) == NULL){ 
							modifCatégorieRecette($_POST["nomCategAvant"], $_POST["nomCategApres"]);  
							echo "Nom de catégorie modifié";
						}
						else{
							echo "Une catégorie existe existe déja avec ce nom";
						}
					}
					else{
						echo "Nom inchangé pour la catégorie : ".$_POST["nomCategAvant"];
					}
				}

				elseif ( isset( $_POST ) && (count( $_POST ) >= 1) && (isset($_POST["boutonSupprimer"]))){
						
					supprimerCategorieRecette($_POST["nomCategAvant"]);
					echo "Suppression effectuée";
				}
				
				elseif ( isset( $_POST ) &&( count( $_POST ) > 0) && (isset($_POST["boutonAjoutCateg"]))){
					afficheFormAjoutCategorieRecette();  
				}
				
				elseif ( isset( $_POST ) &&( count( $_POST ) > 0) && (isset($_POST["boutonInsertionCateg"]))){
					if(insertCategorieRecette($_POST["nomCateg"])){  
						echo '<p>Insertion réussie ! </p>';
					}
				}
										
			?>
			
			<!-- Footer -->
			<div class="footer">
			  <?php footer(); ?>
			</div>
		  </div>
				
	</body>
</html>


