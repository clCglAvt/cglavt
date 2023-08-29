<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014 -CigaleAventure and claude@cigaleaventure.com---
 *
 * Version CAV - 2.7 - été 2022 - Intégration StripeVir dans CAV
 * Version CAV - 2.8 - hiver 2023 - Correction routine FinMois pour le moins de décembre
 *								  - fiabilisation des foreach
 *								   - ajout d'un argument optionel à Affiche_zone_texte et editfieldval
 *					 			  - Installation popup Modif/creation Suivi pour Inscription/Location
 *								  -  methode  select_
 * Version CAV - 2.9 - automne 2023 - Montéee de version V17 - ajout argument à DolEditor->Create et editfield
 *
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

/**
 *   	\file       custum/cglinscription/class/cgllocation.class.php
 *		\ingroup    cglinscription
 *		\brief      Traitement des données
 */

 
/**
 *	Put here description of your class
 */
 
 // Attente de décision concercernant le format du numéro de téléphone
 global $INDICATIF_TEL_FR;
  $INDICATIF_TEL_FR = '';
 
class CglFonctionCommune
{
	var $db;							//!< To store db handler
	var $error;							//!< To return error code (or message)
	var $errors=array();				//!< To return several error codes (or messages)
	var $element='skeleton';			//!< Id that identify managed objects
	var $table_element='skeleton';		//!< Name of table without prefix where object is stored

/* reprendre toutes les fonctions en copie et modif  de dolibarr

    /**
     *  Constructor
     *
     *  @param	DoliDb		$db      Database handler
     */
    function __construct($db)
    {
        $this->db = $db;
        return 1;
    }
	/* Fonction de transformation données
	*	transforme la  date au format YYYY/MM/JJ
	* retour DD/MM/YYYY
	*/
	function transfDateFr($strdate)
	{	
		$datefr = substr($strdate,8,2).'/'.substr($strdate,5,2).'/'.substr($strdate,0,4);
		if ($datefr == '//') $datefr = '';
		return $datefr;
	}	/* transfDateFr */
	/*
	*	récupère les heures/minutes d'une date au format DD/MM/YYYY HH:MN
	* renvoie : <HH>h <MM>
	*/
	function transfHeureFr($strdate)
	{	
		$datefr = substr($strdate,11,2).'h '.substr($strdate,14,2);
		if ($datefr == '//') $datefr = '';
		return $datefr;
	}	/* transfHeureFr */
		/*
	*	récupère les heures/minutes d'une date au format DD/MM/YYYY HH:MN
	* renvoie : <HH>:<MM>
	*/
	function transfHeureEn($strdate)
	{	
		$datefr = substr($strdate,11,2).':'.substr($strdate,14,2);
		if ($datefr == '//') $datefr = '';
		return $datefr;
	}	/* transfHeureEn */
	/*
	*	entrée d'une date au format DD/MM/YYYY HH:MN:SS ou DD/MM/YYYY HH:MM:SS avec HH:MM:SS optionnel
	* renvoie : YYYYMMDDHHMNSS
	*/
	function TransfDateFormFr()
	{
		  // Convert date with format DD/MM/YYY HH:MM:SS. This part of code should not be used.
		if (preg_match('/^([0-9]+)\/([0-9]+)\/([0-9]+)\s?([0-9]+)?:?([0-9]+)?:?([0-9]+)?/i',$string,$reg))
		{
			dol_syslog("dol_stringtotime call to function with deprecated parameter", LOG_WARNING);
			// Date est au format 'DD/MM/YY' ou 'DD/MM/YY HH:MM:SS'
			// Date est au format 'DD/MM/YYYY' ou 'DD/MM/YYYY HH:MM:SS'
			$sday = $reg[1];
			$smonth = $reg[2];
			$syear = $reg[3];
			$shour = $reg[4];
			$smin = $reg[5];
			$ssec = $reg[6];
			if ($syear < 50) $syear+=1900;
			if ($syear >= 50 && $syear < 100) $syear+=2000;
			$string=sprintf("%04d%02d%02d%02d%02d%02d",$syear,$smonth,$sday,$shour,$smin,$ssec);
		}
	} //TransfDateFormFr
	/* Obsolette a supprimer
	*	entrée d'une date au format YYYY/MM/DD... et plus
	* renvoie : DD/MM
	*/
	function transfDateFrCourt($strdate)
	{	
		$datefr = substr($strdate,8,2).'/'.substr($strdate,5,2);
		if ($datefr == '/') $datefr = '';
		return $datefr;
	}	/* transfDateFrCourt */
	/*
	*	entrée d'une date au format DD/MM/YYYY
	* renvoie : le jour de la semaine en  texte francais  
	*/
	function  transfDateJourSem($strdate)
	{
		$joursem = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
		// extraction des jour, mois, an de la date
		list($jour, $mois, $annee) = explode('/', $strdate);
		// calcul du timestamp
		$timestamp = mktime (0, 0, 0, intval($mois), intval($jour), intval($annee));
		// affichage du jour de la semaine		
		return $joursem[date("w",$timestamp)];	
	}//transfDateJourSem
	/*
	*	entrée d'une date au format DD/MM/YYYY
	* renvoie : le mois en  texte français
	*/
	function  transfDateMoisFr($strdate)
	{
			global $langs;
		$MoisFR = array('',$langs->trans('Janv'), $langs->trans('Feb'),  $langs->trans('Mar'), $langs->trans('Apr'), $langs->trans('Mai'), $langs->trans('Jun'),
			$langs->trans('Jul'), $langs->trans('Aug'), $langs->trans('Sep'), $langs->trans('Oct'), $langs->trans('Nov'), $langs->trans('Dec'));


		//$MoisFR = array('','','Janvier', 'Fevrier', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Decembre');
		// extraction des jour, mois, an de la date
		list($jour, $mois, $annee) = explode('/', $strdate);
		// calcul du timestamp
		$timestamp = mktime (0, 0, 0, intval($mois), intval($jour), intval($annee));
		// affichage du mois		
		return $MoisFR[date("n",$timestamp)];	
	}//transfDateMoisFr
	
	/* 
	* Date du début du mois de la date passée en paramette
	*
	* @param	$dt		date au format date unixtojd
	* @retour 	date du premier du mois de la date dt
	*/
	function DebutMois($dt)
	{	
	$dtDebMois = dol_mktime(0, 0, 0,  dol_print_date($dt, '%m'), 1, dol_print_date($dt, '%Y'));
	return $dtDebMois;
	} //DebutMois

	/* 
	* Date du fin du mois de la date passée en paramette
	*
	* @param	$dt		date au format date unixtojd
	* @retour 	date du dernier jour  du mois de la date dt
	*/
	function FinMois($dt)
	{	
	$mois = dol_print_date($dt, '%m') + 1;	
	if ($mois == 13) {
		$mois = 12;
		$jour = 31;
	}
	else $jour = 1;
	$annee = dol_print_date($dt, '%Y');	
	$dtret = dol_mktime(0, 0, 0, (int)$mois, $jour, (int)$annee);
	$dtret = strtotime ('1 days ago', $dtret);
	return $dtret;
	} //FinMois



	function transfDateEtHeure($strdate)
	{
		if (substr($strdate,4,1) == '-')
		{
				$ret =  substr($strdate,8,2 ).'/'.substr($strdate,5,2 ).'/'.substr($strdate,2,2 );
				$ret .= ' '.substr($strdate,11,2 ).'h'.substr($strdate,14,2 );
				return $ret;
		}
		if (substr($strdate,2,1 == '/')) return $strdate;
	} //transfDateEtHeure
	
	/*
	* param	$strdate 	=> date au format D/M/Y H:M 	
	* param	 retourne 	==> date au format YYYY-MM-DD HH:MN:SS
	*/
	function transfDateMysql($strdate)
	{
		$pos1 = 0;
		$pos2= strpos($strdate, '/');
		$lg = strlen($strdate);
		if (empty($strdate)) return;
		if ($pos2 != strlen($strdate) -1) $pos3 = strpos($strdate, '/', $pos2+1);
		else return $strdate;
		if ($pos3 == 0) return ;
		
		// on consid-re qu'un sportif sera ag頤e moins de 95 ans - pour mettre 19 ou 20 comme si裬e de naissance
		$now = dol_now('tzuser');
		
		$annsiecle = strftime("%Y",$now) + 5;

		$text = substr($strdate,$pos3+1 );
		if ( $text <100)
			{
				if ($text > $annsiecle) $annee = '19'.$text;
				else $annee = '20'.$text;
			}
		else $annee = $text;
		$mois = substr($strdate,$pos2+1,$pos3 - $pos2-1);
		if (strlen($mois) == 1) $mois = '0'.$mois;
		$jour = substr($strdate,$pos1, $pos2);
		if (strlen($jour) == 1) $jour = '0'.$jour;		
		$datemysql = $annee.'-'.$mois.'-'.$jour;
		return $datemysql;
	}	/* transfDateMysql */
		/*
	* param	$strdate 	=> date au format D/M/Y H:M (H:M optionel)
	$ param	flgh		=> 1 si on doit mettre les h/m/s, 0 sinon
	* param	 retourne 	==> date au format YYYYMMDD000000
	*/
	function transfDateFormatIdate($strdate)
	{
	
		$pos1 = 0;
		$pos2= strpos($strdate, '/');
		$lg = strlen($strdate);
		if (empty($strdate)) return;
		if ($pos2 != strlen($strdate) -1) $pos3 = strpos($strdate, '/', $pos2+1);
		else return $strdate;
		if ($pos3 == 0) return ;
		
		//pour mettre 19 ou 20 comme siècle de naissance -  on considère qu'un sportif sera agé moins de 95 ans - 
		$now = dol_now('tzuser');
		
		$annsiecle = strftime("%Y",$now) + 5;

		$text = substr($strdate,$pos3+1 );
		if ( $text <100)
			{
				if ($text > $annsiecle) $annee = '19'.$text;
				else $annee = '20'.$text;
			}
		else $annee = $text;
		$mois = substr($strdate,$pos2+1,$pos3 - $pos2-1);
		if (strlen($mois) == 1) $mois = '0'.$mois;
		$jour = substr($strdate,$pos1, $pos2);
		if (strlen($jour) == 1) $jour = '0'.$jour;		
		$dateFormatIdate = $annee.$mois.$jour.'000000';
		return $dateFormatIdate;
		
	}//transfDateFormatIdate

		
	function cglencode($str) 
	{
		//array("'","/","\\","*","?","\"","<",">","|","[","]",",",";","=")
//		$strret = dol_string_nospecial($str, ' ',array("'","/","\\","*","\"","<",">","|","[","]",'"',"&");
		$strret = dol_string_nospecial($str, ' ',array("'","\\","/","*","\"","<",">","|","[","]"));
		return($strret);
	} // cglencode

	/*
	*
	 * @param	string	$selected    option préselectionnée
	 * @param	string	$htmlname    Nom HTML du select
	 * @param	array()	$option	     array("valeur"=>"label") 
	 * @param	integer	$useempty    1 avec choix vide
	 * @param	integer	$allchoice   1=tous les choix
	 *
	 * NOTE - le texte à afficher se présente avec le nom label dans l'ordre sql
	 *
	 * @return	string
	*
	*/
	public function select_($selected = '', $htmlname , $options,  $useempty = 0, $allchoice = 1)
	{
		global $db, $langs;

		$out = '';

		$out = '<select id="select_'.$htmlname.'" name="'.$htmlname.'" class="'.$htmlname.' flat minwidth75imp">';
		if ($useempty) {
			$out .= '<option value="0"></option>';
		}
		if ($allchoice) {
			$out .= '<option value="-1">'.$langs->trans('ToutChoix').'</option>';
		}

		if (is_array($options) and !empty($options)) {
			foreach ($options as $valeur => $label) {
				$out .= '<option ';
				$out .= ($selected == $valeur) ? 'selected="selected"' : '';
				$out .= ' value="'.$valeur;
				$out .= '">';
				$out .= $label;
				$out .= '</option>';
			}
		}
		$out .= '</select>';
	

		return $out;
	
	} //select_
	
	/**
	 * Return le code HTML pour sélectionner une remise
	 *
	 * @param	string	$value    preselected category
	 * @param	string	$htmlname    name of HTML select list
	 * @param	integer	$option   inutilisé
	 * @param	integer	$disabled    true ==> non affiché, false ==> affiché
	 * @param	integer	$useempty    1=Add empty choice
	 * @return	string
	 */
	
	function select_typeremise($value='',$htmlname,$option='',$disabled=false,$useempty='') 
	{
		global $langs;

        $remfixe="remfixe"; $rempourc="rempourc";

        $disabled = ($disabled ? ' disabled' : '');

        $result = '<select class="flat" id="'.$htmlname.'" name="'.$htmlname.'"'.$disabled.' '.$option.'>'."\n";
        if ($useempty) $result .= '<option value="-1"'.(($value < 0)?' selected':'').'></option>'."\n";
        if (($value == 'remfixe') || ($value == 1))
        {
            $result .= '<option value="1" selected>'.$langs->trans("RemiseFix").'</option>'."\n";
            $result .= '<option value="2">'.$langs->trans("RemPourc").'</option>'."\n";
        }
        else
       {
       		$selected=(($useempty && $value != '0' && $value != 'rempourc')?'':' selected');
            $result .= '<option value="1">'.$langs->trans("RemiseFix").'</option>'."\n";
            $result .= '<option value="2"'.$selected.'>'.$langs->trans("RemPourc").'</option>'."\n";
        }
        $result .= '</select>'."\n";
        return $result;
	} //select_typeremise
	
	
	
    /**
     * Output val field for an editable field
     *
     * @param	string	$text			Text of label (not used in this function)
     * @param	string	$htmlname		Name of select field
     * @param	string	$value			Value to show/edit
     * @param	object	$object			Object
     * @param	boolean	$perm			Permission to allow button to edit parameter
     * @param	string	$typeofdata		Type of data ('string' by default, 'amount', 'email', 'numeric:99', 'text' or 'textarea:rows:cols', 'day' or 'datepicker', 'ckeditor:dolibarr_zzz:width:height:savemethod:toolbarstartexpanded:rows:cols', 'select:xxx'...)
     * @param	string	$editvalue		When in edit mode, use this value as $value instead of value (for example, you can provide here a formated price instead of value). Use '' to use same than $value
     * @param	object	$extObject		External object
     * @param	string	$success		Success message
     * @param	string	$moreparam		More param to add on a href URL
     * @return  string					HTML edit field
     */
    function editfieldval($text, $htmlname, $value, $object, $perm, $typeofdata='string', $editvalue='', $extObject=null, $success=null, $moreparam='', $color, $rows)
    {
        global $conf,$langs;
        $ret='';

        // Check parameters
        if (empty($typeofdata)) return 'ErrorBadParameter';
		$ret.="\n";
//		$ret.='<form method="post" action="'.$_SERVER["PHP_SELF"].($moreparam?'?'.$moreparam:'').'">';
		$ret.='<input type="hidden" name="action" value="set'.$htmlname.'">';
		//$ret.='<input type="hidden" name="id" value="'.$object->id.'">';
		$ret.='<table class="nobordernopadding" cellpadding="0" cellspacing="0" width="100%">';
		$ret.='<tr><td '.$color.'>';
		
		
/*
						$okforextended=true;
				if ($tabname == MAIN_DB_PREFIX.'c_email_templates' && empty($conf->global->FCKEDITOR_ENABLE_MAIL)) $okforextended=false;
				$doleditor = new DolEditor($fieldlist[$field], (! empty($obj->{$fieldlist[$field]})?$obj->{$fieldlist[$field]}:''), '', 140, 'dolibarr_mailings', 'In', 0, false, $okforextended, ROWS_5, '90%');
				print $doleditor->Create(1);
	
*/
		
		$tmp=explode(':',$typeofdata);
		require_once DOL_DOCUMENT_ROOT.'/core/class/doleditor.class.php';
		//$doleditor=new DolEditor($htmlname, ($editvalue?$editvalue:$value), ($tmp[2]?$tmp[2]:''), ($tmp[3]?$tmp[3]:'0'), ($tmp[1]?$tmp[1]:'dolibarr_notes'), 'In', ($tmp[5]?$tmp[5]:0), false, true, ($tmp[6]?$tmp[6]:'0'), ($tmp[7]?$tmp[7]:'0'));
		$height=140;
		$toolbarname='dolibarr_notes';
		$toolbarlocation='In';
		$toolbarstartexpanded=false;
		$uselocalbrowser=true;
		$okforextendededitor=false;
		//$rows=ROWS_5;
		$cols='90%';
		$doleditor = new DolEditor($htmlname, $value, '', $height, $toolbarname, $toolbarlocation,  $toolbarstartexpanded, $uselocalbrowser, $okforextendededitor, $rows, $cols);
		$ret.=$doleditor->Create(1, '', true, '', '', $moreparam, '');
		$ret.='</td>';
/*		$ret.='<td align="left" '.$color.'>';
//		$ret.='<input type="submit" class="button" name="modify" value="'.$langs->trans("Modify").'">';
		$ret.='<br>'."\n";
//		$ret.='<input type="submit" class="button" name="cancel" value="'.$langs->trans("Cancel").'">';
		$ret.='</td>';
*/
		$ret.='</tr></table>'."\n";
//		$ret.='</form>'."\n";
		
        return $ret;
    } // editfieldval


	/*
	* Obsolette - voir Affiche_zone_texte de  html.cglFctCommun.class.php
	*/
	function Affiche_zone_texte1($htmlname, $desc = '', $id = '', $nbcol=150, $color, $checkeditor = false, $rows = 5 )// ROWS_5
	{
		global $conf, $user, $event_filtre_car_saisie;
		// copy de core/tpl/notes.tpl.php
		$module = 'societe->';
		$form1 = new Form($this->db);
		$wdesc = str_replace ('<br />',chr(13).chr(10), $desc);
		$desc = $wdesc;
		$colwidth=(isset($colwidth)?$colwidth:25);
		$permission=(isset($permission)?$permission:(isset($user->rights->$module->creer)?$user->rights->$module->creer:0));    // If already defined by caller page
		$permission=$user->rights->societe->creer;
		$saveconffckeditorenabled = $conf->fckeditor->enabled;
		if ($checkeditor ) 		$conf->fckeditor->enabled = '1';		
		else 	$conf->fckeditor->enabled = '';
		//$typeofdata='ckeditor:dolibarr_notes:100%:10::1:3:'.$nbcol;
		$typeofdata .= ':'.$nbcol;
		print '<!-- BEGIN PHP TEMPLATE NOTES -->';
		print '	<div class="table-val-border-row" style="width:100%">';
		print $this->editfieldval('', $htmlname, $desc, $id, $permission, $typeofdata, $desc, null, null, $event_filtre_car_saisie, $color, $rows);
		print '</div>';
		$conf->fckeditor->enabled = $saveconffckeditorenabled;

	}//Affiche_zone_texte

/*
* transforme une tab au format tab[id] = id en un paramètre d'url
*
* 	@param	$tab	array
*	@param	$name	nom du tableau dans l'url
*	@retour string   tab[<id>]=<id>&tab[<id1>]=<id1>
*/
function TransfTabIdUrl($tab, $name)
{
	$ret = '';
	
	if ( !empty($tab)) {
		foreach ($tab as $key => $value)
		{
			if (!empty($ret)) $ret .='&';
			$ret .= $name.'['.$key.']='.$value;				
		} // Foreach
	}
	return ($ret);
	
	}//TransfTabIdUrl


	
} // fin de classe CglFonctionDolibarr
?>