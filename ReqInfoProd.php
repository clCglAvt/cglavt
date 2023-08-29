<?php 
/*
* Utilisé par les php formatant la saisie d'une ligne de commande/propal/facture avec objectline_create.tpl.php, modifié par CCA
* le 12/1/2019
*
*/		
require_once('../../main.inc.php');
require_once(DOL_DOCUMENT_ROOT.'/core/db/mysqli.class.php');
$ID = $_GET["ID"];

$sql ="SELECT price , tva_tx  ";
$sql .= "FROM " . MAIN_DB_PREFIX . "product  as p ";
$sql .= "WHERE rowid='".$ID."'";	
$rsql = $db->query($sql); 

if ($rsql) { 
	 $num = $db->num_rows($rsql);
	 $i=0;	 
		$obj = $db->fetch_object($rsql); 
		$rep = price2num($obj->price).'?'.$obj->tva_tx;
	}
	
$db->free($rsql); 
echo( $rep);

?>