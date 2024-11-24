<?php

require_once('fonctionsRecherches.php');

// ------------------------------ afficheResultRechercheRecette ------------------------

function afficheResultRechercheRecette($tabRecettes){
	//echo'<h2>Résultats de la recherche :</h2>';
	echo'<div class="contentR">';
	foreach($tabRecettes as $key => $recette) {
		$tabingr = renvoieIngredientsRecette($recette['nomRecette']);
		$cpt=0;
		$tabImg = renvoieImagesViaNomRecette($recette['nomRecette']);
		if(!empty($tabImg)){ //regarde si il ya une image lié à la recette
			$image=$tabImg[0]; //récupère la premiere image qui a été renvoyée
			$imagePresente=true;
		}
		else{
			$imagePresente=FALSE;
		}
						
		//affichage de la recette
		echo '<a href="Accueil.php?nomFicheRecette='.$recette['nomRecette'].'" class="lienFicheRecette">
			<div class="recette">
			<div class="RecetteGauche">
				<h2 class="centrageTexte">'.$recette['nomRecette'].'</h2>
				<div class="recette-img">';
				if($imagePresente){
					echo '<img src="'.$image['imgRecetteChemin'].'" alt="'.$image['imgRecetteDesc'].'">';
				}
				else{
					echo '<img src="Images\Icones\defaultImage.png" alt="Pas d\'image pour cette recette">';
				}
															
				echo '</div>';
							
			echo '</div>
					
			<div class="RecetteDroite">
				<div class="recette-info">
					<div class="recette-difficulte">
						<span>Difficulté :</span>
						<span>'.$recette['difficulte'].'/5</span>
					</div>
			
					<div class="recette-temps">
						<span>Temps :</span>
						<span>'.$recette['tempsTotal'].' minutes</span>
					</div>
				</div>';
				if($tabingr != NULL){
					echo '<ul class="ListeIng">';
					for($i=0;$i<count($tabingr) && $cpt!=3;$i++){
						echo '<li class="listIngSolo">'.$tabingr[$i]["nomIngre"].'</li>';
						$cpt++;
					}
					echo '</ul>';
				}
				else{
					echo '<p> Pas d\'ingrédients </p>';
				}
		echo'</div></div></a>';
	}
	echo '</div>';
}



function afficheFicheRecette($recette){
	$tabingr = renvoieIngredientsRecette($recette['nomRecette']);
	$tabUstensiles = renvoieUstensilesRecette($recette['nomRecette']);
	$tabEtapes = renvoieEtapesRecette($recette['nomRecette']);
	$tabImg = renvoieImagesViaNomRecette($recette['nomRecette']);
	if(!empty($tabImg)){ //regarde si il ya une image lié à la recette
		$image=$tabImg[0]; //récupère la premiere image qui a été renvoyée
		$imagePresente=true;
	}
	else{
		$imagePresente=FALSE;
	}
	
	if(isset($_SESSION['connecte']) && isset($_SESSION['idcompte'])){
		if(checkDejaFavori($_SESSION['idcompte'], $recette['idrecette'])){
			echo '<div class="con-like">
				<form method="POST" action="Accueil.php">
					<input type="number" name="idrecette" value="'.$recette['idrecette'].'" hidden>
					<input title="Enlever des favoris" type="submit" class="like" name="suppFavori">
					<div class="checkmark">
						<br>
						<br>
						<img class="outline imageFavori" src="Images\Icones\favori-rempli.png" alt="image favori rempli">
					</div>
				</form>
			</div>';
		}
		else{
			echo '<div class="con-like">
				<form method="POST" action="Accueil.php">
					<input type="number" name="idrecette" value="'.$recette['idrecette'].'" hidden>
					<input title="Enlever des favoris" type="submit" class="like" name="ajoutFavori">
					<div class="checkmark">
						<br>
						<br>
						<img class="outline imageFavori" src="Images\Icones\favori-vide.png" alt="image favori vide">
					</div>
				</form>
			</div>';
		}
			
		
		
	}
	
	echo '<h2 class="centrageTexte nomRecette">'.$recette['nomRecette'].'</h2>';
	echo '<div id="modalOne" class="modal">
			<div class="modal-content">
				<div class="contact-form">
					<a class="close">&times;</a>
					<form action="Accueil.php" method="post">';
					
					if(isset($_SESSION['connecte']) && isset($_SESSION['idcompte'])){
						$noteCompte = rechercheNoteLiéCompte($recette['idrecette'], $_SESSION['idcompte']);
						if($noteCompte!=NULL){
							echo '<input type="number" name="vieilleNote" value="'.$noteCompte['note'].'" hidden>
								<p> Vous avez déja mis une note de '.$noteCompte['note'].'/5 à cette recette</p>';
						}
					}
						
					echo'<div>
						<input type="number" name="note" min="0" max="5" placeholder="Note" required/>
						</div>
						
						<input type="number" name="idrecette" value="'.$recette['idrecette'].'" hidden>

						<button type="submit" href="Accueil.php" class="boutonValidationNote" name="boutonValidationNote" >Valider la note</button>
					</form>
				</div>
			</div>
		</div>
	
	<div id="containerPrincipalFiche">
		<div id="partieHaute">
			<div id="partieHauteGauche">
				<h2 class="centrageTexte">Difficulté :</h2>
				<img src="Images\Icones\\'.$recette['difficulte'].'etoiles.png" alt="image de '.$recette['difficulte'].' étoiles" id="imageEtoilesRecette">
				<h2 class="centrageTexte">Note :</h2>
				<img src="Images\Icones\\'.$recette['noteGlobale'].'etoiles.png" alt="image de '.$recette['noteGlobale'].' étoiles" id="imageEtoilesRecette">';
				
				if(isset($_SESSION['connecte']) && $_SESSION['connecte']){
					echo '<button class="boutonNotationIn" data-modal="modalOne" >Noter la recette</button>';
				}
				else{
					echo '<a href="connexion.php"><button class="boutonNotationIn">Connectez vous pour noter</button></a>';
				}
				
				echo '<h2 class="centrageTexte">Temps de préparation:'.$recette['tempsTotal'].'min</h2>
				
			</div>
			
			<div id="partieHauteDroite">
				<div id="imageFicheRecette">';
				if($imagePresente){
					echo '<img src="'.$image['imgRecetteChemin'].'" alt="'.$image['imgRecetteDesc'].'">';
				}
				else{
					echo '<img src="Images\Icones\defaultImage.png" alt="Pas d\'image pour cette recette">';
				}
		echo'	</div>
			</div>
		</div>
	
		<div id="partieBasse">
			<div id="partieBasseGauche">
					<h2> Ingrédients </h2>';
					if($tabingr != NULL){
						echo '<ul class="ListeIng">';
						for($i=0;$i<count($tabingr);$i++){
							echo '<li class="listIngSolo ">'.$tabingr[$i]["nomIngre"].'</li>';
						}
						echo '</ul>';
					}
					else{
						echo '<p> Pas d\'ingrédients </p>';
					}
					
				echo'<br><h2> Ustensiles </h2>';
					if($tabUstensiles != NULL){
						echo '<ul class="ListeIng">';
						for($i=0;$i<count($tabUstensiles);$i++){
							echo '<li class="listIngSolo ">'.$tabUstensiles[$i]["nomUstensile"].'</li>';
						}
						echo '</ul>';
					}
					else{
						echo '<p> Pas d\'ustensiles </p>';
					}
			echo '</div>
			
			<div id="partieBasseDroite">';
				if($tabEtapes != NULL){
					echo '<h2> Prerequis </h2>
					<p class="centrageTexte texteGras">'.$tabEtapes[0]["descEtape"].'</p>
					<h2> Préparation </h2>
					<ul class="ListeEtapes ">';
					for($i=1;$i<count($tabEtapes);$i++){
						echo '<li class="listEtapesSolo "><p class="texteGras">Etape '.$i.' : </p>'.$tabEtapes[$i]["descEtape"].'</li>';
					}
					echo '</ul>';
				}
				else{
					echo '<p> Pas d\'étapes </p>';
				}
				
			echo '</div>		
		</div>';
	
	echo '</div>';
}



// ------------------------------ afficheResultRecettesAgenda ------------------------

function afficheResultRecettesAgenda($tabRecettes){
	foreach($tabRecettes as $key => $recette) {
		$tabImg = renvoieImagesViaNomRecette($recette['nomRecette']);
		if(!empty($tabImg)){ //regarde si il ya une image lié à la recette
			$image=$tabImg[0]; //récupère la premiere image qui a été renvoyée
			$imagePresente=true;
		}
		else{
			$imagePresente=FALSE;
		}
						
		//affichage de la recette
		echo '<div class="columnRecette">
				<a href="Accueil.php?nomFicheRecette='.$recette['nomRecette'].'" class="lienFicheRecetteAgenda">';
				if($imagePresente){
					echo '<div class="recetteAgenda" style="background-image:url('.$image['imgRecetteChemin'].')">';
				}
				else{
					echo '<div class="recetteAgenda" style="background-color:#F2D3BD">';
				}
																
				echo'<p class="centrageTexte">'.$recette['nomRecette'].'</p>';							
				echo '</div>
				</a>
				<a class="suppressionRecetteAgenda" href="suppRecetteAgenda.php?idRecette='.$recette['idrecette'].'&pourMidi='.$recette['pourMidi'].'&journee='.$recette['dateJournee'].'">Supprimer</a>
			</div>
			';
		
	}
}


?>