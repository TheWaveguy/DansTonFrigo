<?php

// ----------------------------------- appelle de tout les fichiers annexes nécessaires

require_once('connexionBDD.php');
require_once('FonctionsTOM.php');
require_once('component.php');
require_once('fonctionsRecherches.php');
require_once('fonctionsAffichageResultats.php');
require_once('fonctionsSuppression.php');
require_once('fonctionsInsertions.php');
require_once('fonctionsModif.php');

//-------------------------------------------------------------------------------------------

session_start();

//Barre recherche ingrédients

if(isset($_POST['rechingr'])){
	$rechingr = trim($_POST['rechingr']);
	if(!empty($rechingr)){			//trim() -> enlève les espaces de début / fin
		$bdd = connexionUserPDO();
		$rep = $bdd->query('SELECT nomIngre FROM ingredients WHERE nomIngre LIKE "%'.$rechingr.'%" ORDER BY idingredient DESC');
		$bdd = NULL;
	}
}

// ajout d'un ingrédient à la liste
if(isset($_POST['ajtlistingr']))		//bouton d'ajout à la liste d'ingrédient
{
	if($_SESSION['connecte']){
		$idingre = rechercheIdIngredient($_POST['nomIngre']);
		if(verifListeIngre($_SESSION['idcompte'], $idingre)){ //renvoi true si l'insertion peut etre faite 
			if(insertElementListeIngre($_SESSION['idcompte'], $idingre)){
				$dejaexist = false;
			}
			else{
				$dejaexist = true;
				$messg = '<p id="messageErreurListeIngre">ID INVALIDE</p>';	
			}
		}
		else{
			$dejaexist = true;
			$messg = '<p id="messageErreurListeIngre">'.$nomIngre.' est déja présent dans vos ingrédients</p>';
		}
	}
	else{
		if(isset($_SESSION['listeingredient'])){
			$nomIngre = $_POST['nomIngre'];
			if(in_array($nomIngre,$_SESSION['listeingredient'])){
				$dejaexist = true;
			}
			else{
				$dejaexist = false;
			}
			if($dejaexist == false){
				$_SESSION['listeingredient'][] = $nomIngre;
			}
			else{
				$messg = '<p id="messageErreurListeIngre">'.$nomIngre.' est déja présent dans vos ingrédients</p>';
			}
		}
		else{
			$_SESSION['listeingredient'] = [];
			$_SESSION['listeingredient'][] = $_POST['nomIngre'];
		}
	}
	

}

/*Barre recherche recette*/
if(isset($_POST['rech'])){
	$rech = trim($_POST['rech']);
	if(!empty($rech)){	//trim() -> enlève les espaces de début / fin

		$bdd = connexionUserPDO();	
		$requete = $bdd->prepare("SELECT * FROM recettes WHERE nomRecette LIKE ?");
		$requete->execute(array('%'.$rech.'%'));
		
		$tabRecettes = $requete->fetchAll(PDO::FETCH_ASSOC);	//FETCH_ASSOC : récupère les infos renvoyées sous forme d'un tableau associatif
		$recherche=true;
	}
	else{
		$recherche=false;
	}
}

// recherche d'une recette via la liste d'ingrédients
if(isset($_POST['rechercheViaIngre'])){
	if(isset($_SESSION['connecte']) && $_SESSION['connecte']){
		$bdd = connexionUserPDO();
		$sql = "SELECT DISTINCT recettes.idrecette, nomRecette, tempsPrepa, tempsCuisson, tempsRepos, tempsTotal, difficulte, noteGlobale, nbNotes, nbPersonnes
			FROM recettes 
			INNER JOIN compose ON recettes.idrecette = compose.idrecette 
			INNER JOIN ingredients ON ingredients.idingredient = compose.idingredient 
			INNER JOIN dans_liste_de ON ingredients.idingredient = dans_liste_de.idingredient		
			WHERE idcompte = ?";
			
		$requete = $bdd->prepare($sql);
		$requete->execute(array($_SESSION['idcompte']));
			
		$tabRecettes = $requete->fetchAll(PDO::FETCH_ASSOC);			//Récup infos sous forme tableau associatif

		$bdd = NULL;
		$recherche=true;
	}
	else{
		if(!empty($_SESSION['listeingredient'])){		//trim() -> enlève les espaces de début / fin
			$bdd = connexionUserPDO();
			
			$sql = "SELECT DISTINCT recettes.idrecette, nomRecette, tempsPrepa, tempsCuisson, tempsRepos, tempsTotal, difficulte, noteGlobale, nbNotes, nbPersonnes
					FROM recettes 
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
			$recherche=true;
		}
		else{
			$recherche=false;
		}
	}
}

/*Bouton supprimer ingredients de la liste*/

if(isset($_POST['suppingr'])){
	if($_SESSION['connecte']){
		supprimerLiensCompteIngre($_SESSION['idcompte'], rechercheIdIngredient($_POST['nomIngre']));
	}
	else{
		supprimeringredientliste($_POST['nomIngre']);
	}
}
?>

<html lang="fr">

<?php

$linkPage = $_SERVER['PHP_SELF']; // lien de la page actuelle
$name = basename($linkPage); // que le nom de la page
$namePage = pathinfo($name, PATHINFO_FILENAME); // sans le .php
HeaderHtml($namePage);

?>

	<body>

		<div class="header">
			<?php HeaderPage(); ?>
		</div>

		<div class="menuGauche">
			<div class="side1">
                <h2 class="centrageTexte" >Vos ingrédients : </h2>

                <form method="POST" action="Accueil.php" >
                    <input type="search" name="rechingr" class="centrage saisieIngredient" size="20" placeholder=" Rechercher un ingrédient...">
                </form>
                            
            </div>

            <div class="side2">
				<?php 
				
				// affichage de la liste des ingrédients
				
				if (isset($_SESSION['connecte']) && $_SESSION['connecte']){
					$listeIngre = renvoieListeIngredientsCompte($_SESSION['idcompte']);
					if($listeIngre != NULL){
						foreach($listeIngre as $cle=>$valeur){ //listeIngre -> tableau de des noms d'ingrédients
							echo '<ul class="listeIngredients">
								<li><form method="POST" action="Accueil.php">
								<input type="text" name="nomIngre" value="'.$valeur.'" hidden>
								<img src="Images\Icones\point-orange.png" alt="image d\'un point orange" class="image-point-orange">
								<p>'.$valeur.'</p>
								<input type="submit" value="Supprimer" name="suppingr" ></form></li>
							</ul>';
						}
					}
					else{
						echo '<p class="centrageTexte"> Pas encore d\'ingrédients dans votre liste </p>';
					}
					if(isset($dejaexist) AND $dejaexist){
						if(isset($messg)){
							echo $messg;
						}
					}
				}
				else{
					if(isset($_SESSION['listeingredient'])){
						foreach($_SESSION['listeingredient'] as $cle=>$valeur){
							echo '<ul class="listeIngredients">
								<li><form method="POST" action="Accueil.php">
								<input type="text" name="nomIngre" value="'.$valeur.'" hidden>
								<img src="Images\Icones\point-orange.png" alt="image d\'un point orange" class="image-point-orange">
								<p>'.$valeur.'</p>
								<input type="submit" value="Supprimer" name="suppingr"></form></li>
							</ul>';
						}
						if(isset($dejaexist) AND $dejaexist){
							if(isset($messg)){
								echo $messg;
							}
						}
					}					
				}

				?>
            </div>
            
			<div class="side3">
                <form method="POST" action="Accueil.php">
                    <button type="submit" name="rechercheViaIngre" id="rechercheViaIngre" class="centrage" >
                       Rechercher une recette en fonction de mes ingrédients
                    </button>
                </form>
            </div> 
		</div>
		  
		  
		  
		<div class="main">
			
			<?php
			
			// affichage des résultats d'une recherche de recettes via liste d'ingre ou nom de recette
			if(isset($recherche)){ // regarde si on a une recherche via liste ingre ou nom de recette
				if($recherche){
					afficheResultRechercheRecette($tabRecettes);
				}
				else {
					echo '<h2>Aucun résultat pour cette recherche</h2>';
				}
			}
			
			elseif(isset($rechingr)){
				if(isset($rep)){
					if($rep->rowCount() > 1){
						echo '<br>'.$rep->rowCount().' résultats trouvés !';
					}
					else{
						echo '<br>'.$rep->rowCount().' résultats trouvés !';
					}
					if($rep->rowCount() > 0) {
						echo '<ul>';
						while($a = $rep->fetch()) {
							echo '<li>
									<form method="POST" action="Accueil.php" >
									<input type="text" name="nomIngre" value="'. $a['nomIngre'].'" hidden>
									<p>'. $a['nomIngre'].' <input type="submit" name="ajtlistingr" value="Ajouter à ma liste d\'ingrédients"></p></form></li>';
						}
							echo '</ul>';
							
					}
					else{
						echo '<p>Aucun résultat pour :'. $rechingr.'</p>';
					}
				}
				else{
					echo '<p>Aucun résultat pour :'. $rechingr.'</p>';
				}
			}
			
			// affichage fiche recette

			elseif(isset($_GET['nomFicheRecette'])){
				if(!empty($_GET['nomFicheRecette'])){	//trim() -> enlève les espaces de début / fin

					$bdd = connexionUserPDO();	
					$requete = $bdd->prepare("SELECT * FROM recettes WHERE nomRecette LIKE ?");
					$requete->execute(array($_GET['nomFicheRecette']));
					
					if($requete->rowCount() == 1) {
						$recette=$requete->fetch();
						afficheFicheRecette($recette);
					}
					else{
						echo '<p> Impossible de récupérer la recette </p>';
					}
				}
			}
			
			elseif(isset($_POST['boutonValidationNote'])){
				if($_POST['note'] >= 0 && $_POST['note']<=5 && isset($_POST['idrecette'])){ // test si tout est bien renvoyé par le pop up
					if(isset($_POST['vieilleNote'])){
						if($_POST['vieilleNote'] != $_POST['note']){
							changementNoteRecetteCompte($_POST['idrecette'], $_SESSION['idcompte'], $_POST['note']);
							majNoteRecette($_POST['idrecette']);
							echo '<p>Modifications effectuées</p>';
							afficheFicheRecette(renvoieRecetteViaId($_POST['idrecette']));
						}
						else{
							echo '<p>note inchangée</p>';
							afficheFicheRecette(renvoieRecetteViaId($_POST['idrecette']));
						}
					}
					else{
						insertionNoteRecetteCompte($_POST['idrecette'], $_SESSION['idcompte'], $_POST['note']);
						majNoteRecette($_POST['idrecette']);
						echo '<p>Merci pour votre apport à la communauté DansTonFrigo !</p>';
						afficheFicheRecette(renvoieRecetteViaId($_POST['idrecette']));
					}
				}
			}
			
			elseif(isset($_POST['ajoutFavori']) && isset($_POST['idrecette']) && isset($_SESSION['idcompte'])){
				if(insertionFavori($_POST['idrecette'], $_SESSION['idcompte'])){
					afficheFicheRecette(renvoieRecetteViaId($_POST['idrecette']));
				}
				else{
					afficheFicheRecette(renvoieRecetteViaId($_POST['idrecette']));
				}
			}
			
			elseif(isset($_POST['suppFavori']) && isset($_POST['idrecette']) && isset($_SESSION['idcompte'])){
				suppressionFavori($_SESSION['idcompte'], $_POST['idrecette']);
				afficheFicheRecette(renvoieRecetteViaId($_POST['idrecette']));
			}
			
			else{
				if(isset($_SESSION['connecte']) && isset($_SESSION['idcompte'])){
					echo '<h2> Vos favoris </h2>';
					$favoris = recupFavorisCompte($_SESSION['idcompte']);
					if($favoris != NULL){
						afficheResultRechercheRecette($favoris);
					}
					else{
						echo '<p> Vous n\'avez pas encore de favoris </p>';
					}
				}
				else{
					echo '<h2> N\'hésitez pas à vous connecter</h2>';
					echo '<a href="connexion.php"><button class="boutonNotationIn">Connectez vous pour accéder à toutes les fonctionnalitées</button></a>';
				}
			}
			
			?>
			
			<script>
			  let modalBtns = [...document.querySelectorAll(".boutonNotationIn")];
			  modalBtns.forEach(function (btn) {
				btn.onclick = function () {
					console.log("Hello, World!");
				  let modal = btn.getAttribute("data-modal");
				  document.getElementById(modal).style.display = "block";
				};
			  });
			  
			  let closeBtns = [...document.querySelectorAll(".close")];
			  closeBtns.forEach(function (btn) {
				btn.onclick = function () {
					console.log("Hello, World!");
				  let modal = btn.closest(".modal");
				  modal.style.display = "none";
				};
			  });
			  
			  window.onclick = function (event) {
				if (event.target.className === "modal") {
					console.log("Hello, World!");
				  event.target.style.display = "none";
				}
			  };
			</script>
		
			<!-- Footer -->
			<div class="footer">
		  		<?php footer(); ?>
			</div>

		</div>
		
	</body>
</html>