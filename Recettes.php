<?php
require_once ('component.php');
require_once ('connexionBDD.php');
require_once('fonctionsAffichageResultats.php');
session_start();


if(isset($_POST['rechercheFiltre'])){
	$bdd = connexionUserPDO();	
	$sqlWhere = "";
	$sqlFrom = "";
	$tabValeurs=[];
	
	if (isset($_POST["difficulte"]) && ($_POST["difficulte"]!="NULL")){
		$sqlWhere.=" difficulte= ? ";
		$tabValeurs[]=$_POST["difficulte"];
	}
	
	if (isset($_POST["temps"]) && ($_POST["temps"]!="NULL")){
		if($sqlWhere != ""){
			$sqlWhere.=" AND tempsTotal ".$_POST["temps"];
		}
		else{
			$sqlWhere.=" tempsTotal ".$_POST["temps"];
		}
	}
	
	if (isset($_POST["categorie"]) && ($_POST["categorie"]!="NULL")){
		if($sqlWhere != ""){
			$sqlFrom .= "INNER JOIN categorise_recette ON recettes.idrecette = categorise_recette.idrecette ";
			$sqlWhere.=" AND idcategorieRecette = ".$_POST["categorie"];
		}
		else{
			$sqlFrom .= "INNER JOIN categorise_recette ON recettes.idrecette = categorise_recette.idrecette ";
			$sqlWhere.=" idcategorieRecette = ".$_POST["categorie"];
		}
	}
	
	if (isset($_POST["nb_ingredients"]) && ($_POST["nb_ingredients"]!="NULL")){
		if($sqlWhere != ""){
			$sql = "SELECT idrecette, nomRecette, tempsPrepa, tempsCuisson, tempsRepos, tempsTotal, noteGlobale, nbNotes, difficulte, nbPersonnes 
			FROM (SELECT COUNT(idingredient) AS nbIngredients, recettes.idrecette AS idrecette, nomRecette, tempsPrepa, tempsCuisson, tempsRepos, tempsTotal, noteGlobale, nbNotes, difficulte, nbPersonnes
					FROM compose ".$sqlFrom." INNER JOIN recettes ON compose.idrecette = recettes.idrecette WHERE ".$sqlWhere." 
					GROUP BY recettes.idrecette) as temp1
			WHERE nbIngredients ".$_POST["nb_ingredients"];
						
			$requete = $bdd->prepare($sql);
			$requete->execute($tabValeurs);
		
			$tabRecettes = $requete->fetchAll(PDO::FETCH_ASSOC);
			$recherche=TRUE;
		}
		else{
			$sql = "SELECT idrecette, nomRecette, tempsPrepa, tempsCuisson, tempsRepos, tempsTotal, noteGlobale, nbNotes, difficulte, nbPersonnes 
			FROM (SELECT COUNT(idingredient) AS nbIngredients, recettes.idrecette AS idrecette, nomRecette, tempsPrepa, tempsCuisson, tempsRepos, tempsTotal, noteGlobale, nbNotes, difficulte, nbPersonnes
					FROM compose ".$sqlFrom." INNER JOIN recettes ON compose.idrecette = recettes.idrecette 
					GROUP BY recettes.idrecette) as temp1
			WHERE nbIngredients ".$_POST["nb_ingredients"];
						
			$requete = $bdd->prepare($sql);
			$requete->execute();
		
			$tabRecettes = $requete->fetchAll(PDO::FETCH_ASSOC);
			$recherche=TRUE;
		}
	}
	else{
		if($sqlWhere != ""){
			$sql = "SELECT * FROM recettes ".$sqlFrom." WHERE ".$sqlWhere;
			$requete = $bdd->prepare($sql);
			$requete->execute($tabValeurs);
			
			$tabRecettes = $requete->fetchAll(PDO::FETCH_ASSOC);
			$recherche=TRUE;
		}
		else{
			$recherche=FALSE;
		}
	}	
	
	$bdd=NULL;
}
?>

<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<title>Nos recettes : Dans Ton Frigo !</title>
		<link href="Style_Accueil1.css" rel="stylesheet">

		
	</head>

	<body>
		<!-- Header -->
		<div class="header">
			<?php HeaderPage(); ?>
		</div>
		
		
		<div class="menuGauche">
			<h3 id="titreFiltre">Filtres</h3>
				<form method="POST" action="Recettes.php" id="formFiltres">
					<div class="colonneFiltres fondVert">
						<h4>Difficulte:</h4>
							<select name="difficulte" class="selectFiltreRecette">
								<option value="NULL" class="optionFiltreRecette">Peu importe</option>
								<option value="1" class="optionFiltreRecette">1 etoile</option>
								<option value="2" class="optionFiltreRecette">2 etoiles</option>
								<option value="3" class="optionFiltreRecette">3 etoiles</option>
								<option value="4" class="optionFiltreRecette">4 etoiles</option>
								<option value="5" class="optionFiltreRecette">5 etoiles</option>
							</select>

						<h4>Temps de preparation:</h4>
							<select name="temps" class="selectFiltreRecette">
								<option value="NULL" class="optionFiltreRecette">Peu importe</option>
								<option value="< 10" class="optionFiltreRecette">moins de 10 minutes</option>
								<option value="BETWEEN 10 AND 15" class="optionFiltreRecette">entre 10 et 15 minutes</option>
								<option value="BETWEEN 15 AND 30" class="optionFiltreRecette">entre 15 et 30 minutes</option>
								<option value="BETWEEN 30 AND 45" class="optionFiltreRecette">entre 30 et 45 minutes</option>
								<option value="> 45" class="optionFiltreRecette">plus de 45 minutes</option>
							</select>

						<h4>Nombre d'ingredients:</h4>
							<select name="nb_ingredients" class="selectFiltreRecette">
								<option value="NULL" class="optionFiltreRecette">Peu importe</option>
								<option value="< 3" class="optionFiltreRecette">moins de 3</option>
								<option value="BETWEEN 3 AND 5" class="optionFiltreRecette">entre 3 et 5</option>
								<option value="BETWEEN 5 AND 7" class="optionFiltreRecette">entre 5 et 7</option>
								<option value="BETWEEN 7 AND 10" class="optionFiltreRecette">entre 7 et 10</option>
								<option value="> 10" class="optionFiltreRecette">plus de 10</option>
							</select>
							
						<h4> Catégorie : </h4>
							<select name="categorie" class="selectFiltreRecette">
							<?php
								$tabCateg = recupCategories();
								echo "<option value='NULL' class='optionFiltreRecette'>Peu importe</option>";
								foreach($tabCateg as $key => $categorie){
									echo "<option value='".$categorie['idcategorieRecette']."' class='optionFiltreRecette'>".$categorie['libCategorieRecette']."</option>";
								}
								
							?>
							</select>
					</div>
					<button type="submit" name="rechercheFiltre" id="rechercheViaIngre" class="centrage" >
						Lancer la recherche avec mes filtres
					</button>
				</form>
		</div>


		<div class="main">
			
			<!-- <a href="Accueil.php" id="retourAccueil">
                <p id="texteRetourAccueil">retour à l'accueil</p>
            </a> -->
			
			<a href="Accueil.php" class="retourAccueilRecettes">
				<div class="arrow-wrapper">
					<div class="arrow"></div>
				</div>
				Retour à l'accueil
			</a>
			
			
			<?php
			if(isset($recherche)){ // regarde si on a une recherche via liste ingre ou nom de recette
				if($recherche){
					afficheResultRechercheRecette($tabRecettes);
				}
				else {
					echo '<h2>Aucun résultat pour cette recherche</h2>';
				}
			}
			?>
			<div class="footer">
		  		<?php footer(); ?>
			</div>
		</div>
	</body>
</html> 