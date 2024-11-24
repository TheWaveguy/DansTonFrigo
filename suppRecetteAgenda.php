<?php
session_start();
require_once('connexionBDD.php');
require_once('fonctionsSuppression.php');

if((isset($_GET['idRecette']) && !empty($_GET['idRecette']) && isset($_GET['pourMidi']) && $_GET['pourMidi']!=NULL) && isset($_GET['journee']) && !empty($_GET['journee'])){
	suppressionRecetteAgenda($_SESSION['idcompte'], $_GET['idRecette'], $_GET['journee'], $_GET['pourMidi']);
	header("Location: agendaRecettes.php");
}
else{
	header("Location: agendaRecettes.php");
}



?>