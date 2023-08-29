<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014 -CigaleAventure and claude@cigaleaventure.com---
 *
 * Version CAV - 2.8 - hiver 2023
 *					 			  - Installation popup Modif/creation Suivi pour Inscription/Location
 *								   - fiabilisation des foreach
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
  
class FormCglFonctionCommune
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
	
	function AfficheParagraphe($titre, $colspan, $affinfobull = '', $parametre='')
	{
		$this->getParagraphe($titre, $colspan, $affinfobull, $parametre);
	} //AfficheParagraphe
	
	function getParagraphe($titre, $colspan, $affinfobull = '', $parametre='')
	{
		global $langs;
		
		$out = '';
		if ($titre) 
		{
			$out .= '<td class="nobordernopadding hideonsmartphone" width="40" align="left" valign="middle" '.$parametre.' ';
			if ($colspan > 1) $out .= 'colspan='.$colspan;
			$out .= '>'.img_picto('','title.png', '', 0).'&nbsp;';
			//$out .= '<span style="font-size:14px; font-weight:bold">'.$langs->trans($titre).'</span></td>';
			$out .= '<span style="font-size:12px; font-weight:bold">'.$langs->trans($titre).'</span>';
			if (!empty($affinfobull)) $out .= $affinfobull;
			$out .= '</td>';
		}
		return $out;
	} //getParagraphe
	
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
    function editfieldval($text, $htmlname, $value, $object, $perm, $typeofdata='string', $editvalue='', $extObject=null, $success=null, $moreparam='', $color)
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
		$rows=ROWS_5;
		$cols='90%';
		$doleditor = new DolEditor($htmlname, $value, '', $height, $toolbarname, $toolbarlocation,  $toolbarstartexpanded, $uselocalbrowser, $okforextendededitor, $rows, $cols);
		$ret.=$doleditor->Create(1);
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
	function Affiche_zone_texte1($htmlname, $desc = '', $id = '', $nbcol=150, $color, $checkeditor = false)
	{
		global $conf, $user;

		$out = "";
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
		$typeofdata='ckeditor:dolibarr_notes:100%:10::1:3:'.$nbcol;

		$out .=  '<!-- BEGIN PHP TEMPLATE NOTES -->';
		$out .= '	<div class="table-val-border-row" style="width:100%">'.$this->editfieldval('', $htmlname, $desc, $id, $permission, $typeofdata, $desc, null, null, '', $color).'</div>';
		$conf->fckeditor->enabled = $saveconffckeditorenabled;

	}//Affiche_zone_texte1
	/*
	*	Affiche un texte long, avec éditeur de texte
	*
		@param	string $htmlname	Nom de l'objet html
		@param	string $desc		contenu de l'objet
		@param	integer $id			id de l'objet
		@param	integer $nbcol		nombre de colonne du pavé d'édition
		@param	string $color		couleur de fond du pavé d'édition
		@param	booleen $checkeditor	1 - pavé d'édition avec éditeur - '' ou 0 - simple champ de saisi
		@param	integer $rows		valeur de ROWS_1 à ROWS_9
	*	retour string  	Code Html
	*/

	function Affiche_zone_texte($htmlname, $desc = '', $id = '', $nbcol=150, $color, $checkeditor = false, $rows = 5 )// ROWS_5
	{
		global $conf, $user;
		$out = "";
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
		$out .= '<!-- BEGIN PHP TEMPLATE NOTES -->';
		$out .= '	<div class="table-val-border-row" style="width:100%">'.$this->editfieldval('', $htmlname, $desc, $id, $permission, $typeofdata, $desc, null, null, '', $color, $rows).'</div>';
		$conf->fckeditor->enabled = $saveconffckeditorenabled;

	return $out;
	}//Affiche_zone_texte

	
} // fin de classe CglFonctionDolibarr
?>