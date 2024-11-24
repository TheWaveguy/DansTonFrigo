<?php


// ----------------------------------------------PAGE OBSOLETE------------------------------------------------




require_once('connexionBDD.php');
require_once('component.php');
require_once('fonctionsRecherches.php');
require_once('FonctionsTOM.php');
require_once('fonctionsAffichageResultats.php');

// $bdd = connexionUserPDO();
session_start();
if(isset($_SESSION['connecte']) AND !empty($_SESSION['connecte'])){
	$connecte = true;
}
else{
	$connecte = false;
}

// recherche recette via nom recette
if(isset($_POST['rech'])){
	$rech = trim($_POST['rech']);
	if(!empty($rech)){	//trim() -> enlève les espaces de début / fin

		$bdd = connexionUserPDO();	
		$requete = $bdd->prepare("SELECT * FROM recettes WHERE nomRecette LIKE ?");
		$requete->execute(array('%'.$rech.'%'));
		
		$tabRecettes = $requete->fetchAll(PDO::FETCH_ASSOC);	//FETCH_ASSOC : récupère les infos renvoyées sous forme d'un tableau associatif
	}
}

// recherche d'une recette via la liste d'ingrédients
if(isset($_POST['rechercheViaIngre']) AND !empty($_SESSION['listeingredient'])){		//trim() -> enlève les espaces de début / fin
	$bdd = connexionUserPDO();
	
	$sql = "SELECT * FROM recettes 
			INNER JOIN compose ON recettes.idrecette = compose.idrecette 
			INNER JOIN ingredients ON ingredients.idingredient = compose.idingredient 
			WHERE nomIngre LIKE ";
				
	$prems = true;
	foreach($_SESSION['listeingredient'] as $key => $nomIngre){
		if($prems){
			$sql.=" ? ";
			$prems=false;
		}
		else{
			$sql.="OR nomIngre LIKE ? ";
		}
	}
	
	$requete = $bdd->prepare($sql);
	$requete->execute($_SESSION['listeingredient']);
	
	$tabRecettes = $requete->fetchAll(PDO::FETCH_ASSOC);			//Récup infos sous forme tableau associatif

	$bdd = NULL;
}

//Suppression d'un ingredient de la liste

if(isset($_POST['suppingr'])){
	supprimeringredientliste($_POST['nomIngre']);
}
?>

<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<title>Accueil Dans Ton Frigo !</title>
		<link href="Style_Accueil1.css" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
	</head>

	<body>
		<!-- Header -->
		<div class="header">
			<?php HeaderPage(); ?>
		</div>


		<div class="menuGauche">
            <div class="side1">
                <h2 class="centrageTexte" >Vos ingrédients : </h2>

                <form method="POST" action="rechercheIngredient.php" >
                    <input type="search" name="rechingr" class="centrage saisieIngredient" size="20" placeholder=" Rechercher un ingrédient...">
                </form>
                            
            </div>

            <div class="side2">
				<?php 
				
				// affichage de la liste des ingrédients
				
                if(isset($_SESSION['listeingredient'])){
					foreach($_SESSION['listeingredient'] as $cle=>$valeur){
						echo '<ul>
							<li><form method="POST" action="rechercher.php">
							<input type="text" name="nomIngre" value="'.$valeur.'" hidden>'
							.$valeur.' <input type="submit" value="Supprimer" name="suppingr"></form></li>
						</ul>';
					}
					if(isset($_SESSION['dejaexist']) AND $_SESSION['dejaexist'] == true){
						if(isset($_SESSION['messg'])){
							echo $_SESSION['messg'];
						}
					}
				}
				?>
            </div>
            
			<div class="side3">
                <form method="POST" action="rechercher.php">
                    <button type=submit name="rechercheViaIngre" id="chercheRecetteIngredient" class="centrage" >
                       Rechercher une recette en fonction de mes ingrédients
                    </button>
                </form>
            </div>           
		</div>
		  
		<div class="main">
			<?php
			
			// affichage des résultats d'une recherche de recettes via liste d'ingre ou nom de recette
			if(isset($rech) || isset($_POST['rechercheViaIngre'])){ // regarde si on a une recherche via liste ingre ou nom de recette
				if(!empty($tabRecettes)){
					afficheResultRechercheRecette($tabRecettes);
				}
				else {
					echo '<h2>Aucun résultat pour cette recherche</h2>';
				}
			}
			else{
				echo '<h1> Bienvenue sur Dans Ton Frigo </h1>';
			}
			
			?>
			<!-- Footer -->
			<div class="footer">
			  <?php footer(); ?>
			</div>
		</div>
				
	</body>
</html>