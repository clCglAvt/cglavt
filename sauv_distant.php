<?php
/*
define('DOL_DOCUMENT_ROOT','D:/dolibarr/www/dolibarrCAVV404/htdocs');
require_once DOL_DOCUMENT_ROOT.'/main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/functions.lib.php';
*/
	
//	commande"C:\Program Files (x86)\Mozilla Firefox\firefox.exe" "localhost/dolibarr/custom/cglavt/sauv.php?base=CglAvt&entreprise=CglAvt30120&version=804"
//	commande"C:\Program Files (x86)\Mozilla Firefox\firefox.exe" "https://www.fourmi.cigaleaventure.com/custom/cglavt/sauv.php?base=cigaleav_dolibarr&entreprise=CglAvt30120&version=804"


// Lancement 
//	D:\dolibarr\www\dolibarrCAVV404\htdocs\custom\cglavt
//	D:\dolibarr\bin\php\php5.5.12\php.exe -f D:\dolibarr\www\dolibarrCAVV404\htdocs\custom\cglavt\sauv.php

if (file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';

$base=$_GET["base"];
if (empty($base)) $base = $_POST["base"];
$motdepasse=$_GET["entreprise"];
$version=$_GET["version"];
if (empty($motdepasse)) $base = $_POST["entreprise"];
function ecrire($file, $texte)
{	
	fwrite($file,$texte);	
} // ecrire

function jour($nbsem)
{
	static $tbjour;
	$tbjour = array("1"=>"lundi","2" => 'mardi',"3" => 'mercredi',"4" => 'jeudi',"4" => 'vendredi',"6" => 'samedi', "7" => 'Dimanche');
	return $tbjour[$nbsem];
	
} //jour

function mois($nbmois)
{
	static $tbmois;
	$tbmois = array("1" => 'janvier',"2" => 'février',"3" => 'mars',"4" => 'avril',"5" => 'mai',"6" => 'juin', "7" => 'juillet',"8" => 'aout',"9" => 'septembre',"10" => 'octobre',"11" => 'novembre',"12" => 'decembre');
	return $tbmois[$nbmois];
	
} //jour

$resul = array();

$ffichier = fopen($dolibarr_main_data_root."/sauvegarde/rapportsauvegarde.txt","a");
ecrire ($ffichier, "\n******************************************\n");
ecrire ($ffichier, "*******************************************\n");

$jour=jour(date('N'));
$mois=mois(date('n'));

ecrire ($ffichier, "en date du ".$jour.date(' j ').$mois.date(' Y')." à ".date('H:i'). "\n");
ecrire ($ffichier, "-----------------------------------\n");
ecrire ($ffichier, "             Cigale Aventure'  \n");
ecrire ($ffichier, "-----------------------------------\n");
ecrire ($ffichier, "sauvegarde Base de données de  Cigale Aventure\n");

//mdp - CglAvt30120
// Nom base-  CglAvt - dolibarrv4


$cmd = "mysqldump -u dolibarrmysql -p  --password=".$motdepasse." --result-file=".$dolibarr_main_data_root."/sauvegarde/dolibarr.".$base.".".$version.".sql ".$base;
exec ($cmd, $result, $ret);
if ($ret == 2) { ecrire ($ffichier, 'Probleme de mot de passe ou de choix de base'); exit; }


ecrire ($ffichier, "-----------------------------------\n");
ecrire ($ffichier, "recuperation conf.php\n");
ecrire ($ffichier, "-----------------------------------\n");


$cmd = "copy /Y ".$dolibarr_main_document_root."/conf/conf.php ".$dolibarr_main_data_root."/sauvegarde ";
unset ($result);
$resul = array();

exec ($cmd, $result, $ret);


ecrire ($ffichier, "-----------------------------------\n");
ecrire ($ffichier, "recuperation alias\n");
ecrire ($ffichier, "-----------------------------------\n");


$cmd = "copy /Y ".$dolibarr_main_document_root."/../../alias ".$dolibarr_main_data_root."/sauvegarde  ";
unset ($result);
$resul = array();

exec ($cmd, $result, $ret);

ecrire ($ffichier, "-----------------------------------\n");
ecrire ($ffichier, "recuperation des documents de dolibarr_documents est assurée par SyncBackFree\n");
ecrire ($ffichier, "pas de copie sur dropbox\n");
ecrire ($ffichier, "-----------------------------------\n");

//$cmd = "xcopy /S /Y /B D:\dolibarr\dolibarr_documents\sauvegarde\dolibarr.CglAvt.4.0.4.sql C:\Users\CAV\Dropbox\"SAUVEGARDE DOLIBAR CAV";


ecrire ($ffichier, "-----------------------------------\n");
ecrire ($ffichier, "Fin de traitement\n");
ecrire ($ffichier, "-----------------------------------\n");


ecrire ($ffichier, " à ".date('H:i'). "\n");

ecrire ($ffichier, "\n******************************************\n");
ecrire ($ffichier, "*******************************************\n");

fclose($ffichier);
print '<html><head>';
echo '<script type="text/javascript">function _closeWindow() { window.opener = self; self.close();}</script>';
print '</head><body>';
echo '<script type="text/javascript">_closeWindow();</script>';
print '</body>';

/*
rem Dump mysql + fichiers Dolibarr + Conf ==> Dropbox et sur FreeBox
rem doc http://wiki.dolibarr.org/index.php/Sauvegardes
*/

?>