<?php
/* lancement http://localhost/dolibarr/custom/cglinscription/bilan2015.php
*/
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014 -CigaleAventure and claude@cigaleaventure.com---
 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
/*
A FAIRE
Taille des barres de sélections
*/
/**
 *   	\file       custom/cglinscription/verification.php
 *		\ingroup    cglinscription
 *		\brief      donne les résultats de vérifications
 *					argument 'TestGeneralPresenceEcriture' pour vérifier que touts les paiements des bulletins d'une année saisie ont les écritues correspondant aux paiements
 */

 
// Change this following line to use the correct relative path (../, ../../, etc)
$res=0;
if (file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';
require_once ('./class/cglinscription.class.php');
require_once ('./class/bulletin.class.php');

// Load traductions files requiredby by page
$langs->load("other");
$langs->load("cglinscription@cglinscription");

$annee = GETPOST ("annee", 'int');
if (empty($annee)) $annee =  strftime('%Y',dol_now());
$test = GETPOST ("test", 'alpha');


    /**
     *    	Return a link on thirdparty (with picto)
     *
     *		@param	int	$withpicto	Add picto into link (0=No picto, 1=Include picto with link, 2=Picto only)
     *		@param	string	$option		Target of link ('', 'customer', 'prospect', 'supplier')
     *		@param	int	$maxlen		Max length of text
     *		@param	int	$id		Identifiant de l'objet
     *		@return	string				String with URL
     */
	

/***************************************************
* VIEW
*
* Put here all code to build page
****************************************************/

llxHeader('','Lcglinscription');


require_once	DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
require_once	DOL_DOCUMENT_ROOT.'/custom/cglavt/class/cglFctCommune.class.php';
$dt = '200808092100';
$dt1 = '2008-08-09 21:00';
$dt2 = '2008/08/09 21:00';
$dt3 = '09/08/2008 21:00';
$dolnow = dol_now();
$dolnow1 = dol_now('gmt');
$w = new CglFonctionCommune ($db);

print '<br>	heure actuelle : 21h';

print '<br> prendre le modèle de agenda : <br>&nbsp&nbsp&nbsp&nbsp<b>saisie date, ';
print '<br>&nbsp&nbsp&nbsp&nbsp<b>transerfert dans variable PHP</b>,';
print '<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <i> recupère date heure par dol_mktime </i>';
print '<br>&nbsp&nbsp&nbsp&nbsp<b> transfert en base</b>,';
print '<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <i> datec = $this->db-><b>idate</b>($dolnow) </i>';
print '<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <i> datep = $this->db-><b>idate</b>($this->datep) </i>';
print ' <br>&nbsp&nbsp&nbsp&nbsp<b>lecture de base</b>e ';
print '<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <i> datep = $this->db-><b>jdate</b>($obl->datep) </i>';
print '<br>&nbsp&nbsp&nbsp&nbsp<b>affichage</b>';
print '<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <i> $datep=($datep?$datep:$object->datep) </i>';
print '<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <i> <b>select_date</b></i>';
print '<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <i> si date vide $set_time = dol_now("tzuser")-(<b>getServerTimeZoneInt<b>("now")*3600)</i>';

print '<br>';

print '<br> reprendre d"abord <b>dol_mktime,</b> dol_now, puis dol_print_date, puis   dol_getdate e les fonctions date de fonctions.lib et de dates.lib';


exit;



print '<br><b>DE CHAR VERS TIME</b>';
print '<br>&nbsp&nbsp&nbsp&nbsp<b>	jdate  - heure de '.$dt.':</b>'.$db->jdate($dt);
print '<br>&nbsp&nbsp&nbsp&nbsp<b>	jdate  fonctionne avec une date char de type YYYYMMDDHHMNSS ou YYYY-MM-DD HH:MN ou YYYY/MM/DD HH:MN:</b>';
print '<br>&nbsp&nbsp&nbsp&nbsp	jdate  ne fonctionne pas avec une date char de type DD/MM/YYYY HH:MN';
print '<br>&nbsp&nbsp&nbsp&nbsp<b>	jdate  renvoie une date en nb seconde mais pas au format now():</b>';
print '<br>';
print '<br>==================================================';
print '<br><b>DE TIME VERS CHAR</b>';
print '<br>';

print '<br>==================================================';
print '<br><b>DE CHAR VERS HEURE</b>';
print '<br>&nbsp&nbsp&nbsp&nbsp<b>	jdate & dol_print_date  - heure de '.$dt.':</b>'.dol_print_date($db->jdate($dt),'hour');
print '<br>&nbsp&nbsp&nbsp&nbsp<b>	dol_print_date  fonctionne avec une date une date en nb seconde mais pas au format now():</b>';
print '<br>&nbsp&nbsp&nbsp&nbsp	dol_print_date  ne fonctionne pas avec une date char de type now()';
print '<br>';

$fnow= new DateTime($dt);
print '<br>	<b>DateTime  - '.$dt.'</b>:'. $fnow->format('H:i');
print '<br>&nbsp&nbsp&nbsp&nbsp<b>	dol_print_date  fonctionne avec une date une date en nb seconde mais pas au format now():</b>';
print '<br>&nbsp&nbsp&nbsp&nbsp	dol_print_date  ne fonctionne pas  avec une date char de type now()';
print '<br>&nbsp&nbsp&nbsp&nbsp	dol_print_date  ne tient pas compte du décalage avec une date char de type DateTime';
$fnow= new DateTime();
print '<br>DateTime  - maintenant:'. $fnow->format('H:i');

print '<br><b><i>*********DE CHAR VERS TIME VERS CHAR</i></b>';

print '<br><b>	jdate & dol_print_date - heure de '.$dt.':</b>'.dol_print_date($db->jdate($dt),'hour');

print '<br>';
print '<br>';

print '<br><b>FAUX</b>';

$fnow= new DateTime();
print '<br>	DateTime  - heure de maintenant :'. $fnow->format('H:i');

print '<br>';

print '<br><b>	jdate   - maintenant</b>'.$db->jdate($dolnow);
print '<br><b>	jdate & dol_print_date  - maintenant</b>'.dol_print_date($db->jdate($dolnow),'hour');
print '<br>';

print '<br>	strftime  - heure:'.strftime('%H',$dolnow);
print '<br>';
print '<br>	dol_print_date  - heure de maintenant:'.dol_print_date($dolnow,'hour');
print '<br>	dol_print_date  - heure de maintenant1:'.dol_print_date($dolnow1,'hour');
print '<br>';
print '<br><i>	jdate  & dol_print_date  - heure de maintenant</i>:'.dol_print_date($db->jdate($dolnow),'hour');
print '<br><i>	jdate  - heure de maintenant</i>:'.$db->jdate($dolnow);

print '<br>';
print '<br>	idate  - heure de maintenant:'.dol_print_date($db->idate($dolnow),'hour');

print '<br>	idate  - heure de '.$dt.': '.dol_print_date($db->idate($dt),'hour');
print '<br>';

print '<br>	dol_stringtotime  - heure de maintenant :'. dol_stringtotime($dolnow,1);
print '<br>	dol_stringtotime  - '.$dt.':'. dol_stringtotime($dt,1);
print '<br> ===============dol_stringtotime nécessite une retranscription en chaine de caractères';
print '<br>';

print '<br>	transfDateFr  - heure de maintenant :'. $w->transfDateFr($dolnow,1);
print '<br> ===============transfDateFr nécessite une date en chaine de caractères';
print '<br>	transfHeureFr  - '.$dt.':'. $w->transfHeureFr($dt,1);
print '<br> ==============transfHeureFr: Voir le type de date nécessaire';
print '<br>';


$fnow= new DateTime();
print '<br>	DateTime  - heure de maintenant :'. $fnow->format('H:i');
print '<br>';



print '<br>	time  - heure de maintenant :'. time();
print '<br>	time  - '.$dt.':'. time($dt);
print '<br> ===============time nécessite une retranscription en time';
print '<br>';

print '<br> voir aussi  dol_mktime, dol_getdate';
/*
$date=dol_now();
		$yyyy = intval(strftime("%Y",$date));
		$mm =  intval(strftime("%m",$date));
		$num=sprintf("%04s",1);
		$mois= sprintf("%02s",$mm);
		$annee=sprintf("%04s",$yyyy);
		if (($anneebul == $yyyy) and ($mm == $moisbul) )
			dateretrait= '".$w->transfDateMysql($this->locdateretrait) 
$datepai = $w->transfDateFormatIdate($this->date_paiement);


*/

print '<br> La fonction bulletion->CalculJH est abérante. Vérifier qu"elle n"est pas utiliser et la supprimer';


?>


