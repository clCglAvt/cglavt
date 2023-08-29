<?php
/* Copyright (C) 2007-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2014 -CigaleAventure and claude@cigaleaventure.com---
 *
 * Version CAV - 2.7 - juin 2022
 *					 - remplacer SESSION[newtoken] par newtoken()
 *					 - Migration Dolibarr V15
 * Version CAV - 2.8 - hiver 2023 -
 *			- recupération fonction selectyesno pour ajout de possibilité onchange...
 * Version CAV - 2.8.4 - printemps 2023 -
 *			- amélioration interface.. Rapport d'activité.
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

 /**************************/
 /**************************/
 /**************************/
 // ATTEENTION showdocuments de listematerileloue parait reprise du Coeur. Ne faudrait-il pas la mettre ici ??
 // ATTENTIOn contact_property_array dans html.formcommun.class. Ne faudrait-il pas la mettre ici ??  
 /**************************/
 /**************************/
  
require_once DOL_DOCUMENT_ROOT."/custom/cglavt/class/cglFctCommune.class.php";
/**
 *	Put here description of your class
 */
class CglFonctionDolibarr 
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
	
	/*
	* fonction s'appuaybnt sur la recherche dolibarr de TVA : functions.lib.php fonction get_product_vat_for_country
	*/
	function taux_TVAstandard ()
	{
		global $conf;
		$fk_pays = substr($conf->global->MAIN_INFO_SOCIETE_COUNTRY,0,1);	
		static $ret;
		if (empty($ret)) {
	
			$sql = "SELECT taux as vat_rate";
			$sql.= " FROM ".MAIN_DB_PREFIX."c_tva as t ";
			$sql.= " WHERE t.active=1 AND t.fk_pays ='".$fk_pays."'";
			$sql.= " ORDER BY t.taux DESC, t.recuperableonly ASC";
			$sql.= $this->db->plimit(1);
			$ret = -1;
			$resql=$this->db->query($sql);
			if ($resql)
			{
				$obj=$this->db->fetch_object($resql);
				if ($obj)
				{
					$ret=$obj->vat_rate;
				}
				$this->db->free($sql);
			}
			else dol_print_error($this->db);
		}
		return $ret;

	}//taux_TVAstandard
	
    /**
	* recopié de V8.0.3 - de dolibarr/core/html.form.class.php
	* pour simplifier l'affichage des minute et mettre une plage à l'affichage des heures
	 *	Show a HTML widget to input a date or combo list for day, month, years and optionaly hours and minutes.
	 *  Fields are preselected with :
	 *            	- set_time date (must be a local PHP server timestamp or string date with format 'YYYY-MM-DD' or 'YYYY-MM-DD HH:MM')
	 *            	- local date in user area, if set_time is '' (so if set_time is '', output may differs when done from two different location)
	 *            	- Empty (fields empty), if set_time is -1 (in this case, parameter empty must also have value 1)
	 *
	 *	@param	timestamp	$set_time 		Pre-selected date (must be a local PHP server timestamp), -1 to keep date not preselected, '' to use current date with 00:00 hour (Parameter 'empty' must be 0 or 2).
	 *	@param	string		$prefix			Prefix for fields name
	 *	@param	int			$h				1 or 2=Show also hours (2=hours on a new line), -1 has same effect but hour and minutes are prefilled with 23:59 if date is empty, 3 show hour always empty
	 *	@param	int			$m				1=Show also minutes, -1 has same effect but hour and minutes are prefilled with 23:59 if date is empty, 3 show minutes always empty
	 *	@param	int			$empty			0=Fields required, 1=Empty inputs are allowed, 2=Empty inputs are allowed for hours only
	 *	@param	string		$form_name 		Not used
	 *	@param	int			$d				1=Show days, month, years
	 * 	@param	int			$addnowlink		Add a link "Now"
	 * 	@param	int			$nooutput		Do not output html string but return it
	 * 	@param 	int			$disabled		Disable input fields
	 *  @param  int			$fullday        When a checkbox with this html name is on, hour and day are set with 00:00 or 23:59
	 *  @param	string		$addplusone		Add a link "+1 hour". Value must be name of another select_date field.
	 *  @param  datetime    $adddateof      Add a link "Date of invoice" using the following date.
	 *  @param  int			$heuredeb       Numéro de l'heure de début du sélect  
	 *  @param  int			$heurefin       Numéro de l'heure de fin du sélect        
	 *  @param  int			$minpas         Pas d'avancement des minute (0+minpas, 0+minpas+minpas, 0+minpas+minpas+minpas...)     
	 * 	@return	string|null						Nothing or string if nooutput is 1
	 *  @see	form_date, select_month, select_year, select_dayofweek
	 */
	function select_date($set_time='', $prefix='re', $h=0, $m=0, $empty=0, $form_name="", $d=1, $addnowlink=0, $nooutput=0, $disabled=0, $fullday='', $addplusone='', $adddateof='', $heuredeb = 0, $heurefin = 24, $minpas=1)
	{
		global $conf,$langs;

		$retstring='';

		if($prefix=='') $prefix='re';
		if($h == '') $h=0;
		if($m == '') $m=0;
		$emptydate=0;
		$emptyhours=0;
		if ($empty == 1) { $emptydate=1; $emptyhours=1; }
		if ($empty == 2) { $emptydate=0; $emptyhours=1; }
		$orig_set_time=$set_time;

		if ($set_time === '' && $emptydate == 0)
		{
			include_once DOL_DOCUMENT_ROOT.'/core/lib/date.lib.php';
			$set_time = dol_now('tzuser')-(getServerTimeZoneInt('now')*3600); // set_time must be relative to PHP server timezone
		}

		// Analysis of the pre-selection date
		if (preg_match('/^([0-9]+)\-([0-9]+)\-([0-9]+)\s?([0-9]+)?:?([0-9]+)?/',$set_time,$reg))	// deprecated usage
		{
			// Date format 'YYYY-MM-DD' or 'YYYY-MM-DD HH:MM:SS'
			$syear	= (! empty($reg[1])?$reg[1]:'');
			$smonth	= (! empty($reg[2])?$reg[2]:'');
			$sday	= (! empty($reg[3])?$reg[3]:'');
			$shour	= (! empty($reg[4])?$reg[4]:'');
			$smin	= (! empty($reg[5])?$reg[5]:'');
		}
		elseif (strval($set_time) != '' && $set_time != -1)
		{
			// set_time est un timestamps (0 possible)
			$syear = dol_print_date($set_time, "%Y");
			$smonth = dol_print_date($set_time, "%m");
			$sday = dol_print_date($set_time, "%d");
			if ($orig_set_time != '')
			{
				$shour = dol_print_date($set_time, "%H");
				$smin = dol_print_date($set_time, "%M");
				$ssec = dol_print_date($set_time, "%S");
			}
			else
			{
				$shour = '';
				$smin = '';
				$ssec = '';
			}
		}
		else
		{
			// Date est '' ou vaut -1
			$syear = '';
			$smonth = '';
			$sday = '';
			$shour = !isset($conf->global->MAIN_DEFAULT_DATE_HOUR) ? ($h == -1 ? '23' : '') : $conf->global->MAIN_DEFAULT_DATE_HOUR;
			$smin = !isset($conf->global->MAIN_DEFAULT_DATE_MIN) ? ($h == -1 ? '59' : '') : $conf->global->MAIN_DEFAULT_DATE_MIN;
			$ssec = !isset($conf->global->MAIN_DEFAULT_DATE_SEC) ? ($h == -1 ? '59' : '') : $conf->global->MAIN_DEFAULT_DATE_SEC;
		}
		if ($h == 3) $shour = '';
		if ($m == 3) $smin = '';

		// You can set MAIN_POPUP_CALENDAR to 'eldy' or 'jquery'
		$usecalendar='combo';
		if (! empty($conf->use_javascript_ajax) && (empty($conf->global->MAIN_POPUP_CALENDAR) || $conf->global->MAIN_POPUP_CALENDAR != "none")) {
			$usecalendar = ((empty($conf->global->MAIN_POPUP_CALENDAR) || $conf->global->MAIN_POPUP_CALENDAR == 'eldy')?'jquery':$conf->global->MAIN_POPUP_CALENDAR);
		}
		//if (! empty($conf->browser->phone)) $usecalendar='combo';

		if ($d)
		{
			// Show date with popup
			if ($usecalendar != 'combo')
			{
				$formated_date='';
				//print "e".$set_time." t ".$conf->format_date_short;
				if (strval($set_time) != '' && $set_time != -1)
				{
					//$formated_date=dol_print_date($set_time,$conf->format_date_short);
					$formated_date=dol_print_date($set_time,$langs->trans("FormatDateShortInput"));  // FormatDateShortInput for dol_print_date / FormatDateShortJavaInput that is same for javascript
				}

				// Calendrier popup version eldy
				if ($usecalendar == "eldy")
				{
					// Zone de saisie manuelle de la date
					$retstring.='<input id="'.$prefix.'" name="'.$prefix.'" type="text" class="maxwidth75" maxlength="11" value="'.$formated_date.'"';
					$retstring.=($disabled?' disabled':'');
					$retstring.=' onChange="dpChangeDay(\''.$prefix.'\',\''.$langs->trans("FormatDateShortJavaInput").'\'); "';  // FormatDateShortInput for dol_print_date / FormatDateShortJavaInput that is same for javascript
					$retstring.='>';

					// Icone calendrier
					if (! $disabled)
					{
						$retstring.='<button id="'.$prefix.'Button" type="button" class="dpInvisibleButtons"';
						$base=DOL_MAIN_URL_ROOT.'/core/';
						$retstring.=' onClick="showDP(\''.$base.'\',\''.$prefix.'\',\''.$langs->trans("FormatDateShortJavaInput").'\',\''.$langs->defaultlang.'\');"';
						$retstring.='>'.img_object($langs->trans("SelectDate"),'calendarday','class="datecallink"').'</button>';
					}
					else $retstring.='<button id="'.$prefix.'Button" type="button" class="dpInvisibleButtons">'.img_object($langs->trans("Disabled"),'calendarday','class="datecallink"').'</button>';

					$retstring.='<input type="hidden" id="'.$prefix.'day"   name="'.$prefix.'day"   value="'.$sday.'">'."\n";
					$retstring.='<input type="hidden" id="'.$prefix.'month" name="'.$prefix.'month" value="'.$smonth.'">'."\n";
					$retstring.='<input type="hidden" id="'.$prefix.'year"  name="'.$prefix.'year"  value="'.$syear.'">'."\n";
				}
				elseif ($usecalendar == 'jquery')
				{
					if (! $disabled)
					{
						// Output javascript for datepicker
						$retstring.="<script type='text/javascript'>";
						$retstring.="$(function(){ $('#".$prefix."').datepicker({
							dateFormat: '".$langs->trans("FormatDateShortJQueryInput")."',
							autoclose: true,
							todayHighlight: true,";
							if (! empty($conf->dol_use_jmobile))
							{
								$retstring.="
								beforeShow: function (input, datePicker) {
									input.disabled = true;
								},
								onClose: function (dateText, datePicker) {
									this.disabled = false;
								},
								";
							}
							// Note: We don't need monthNames, monthNamesShort, dayNames, dayNamesShort, dayNamesMin, they are set globally on datepicker component in lib_head.js.php
							if (empty($conf->global->MAIN_POPUP_CALENDAR_ON_FOCUS))
							{
							$retstring.="
								showOn: 'button',
								buttonImage: '".DOL_URL_ROOT."/theme/".$conf->theme."/img/object_calendarday.png',
								buttonImageOnly: true";
							}
							$retstring.="
							}) });";
						$retstring.="</script>";
					}

					// Zone de saisie manuelle de la date
					$retstring.='<div class="nowrap inline-block">';
					$retstring.='<input id="'.$prefix.'" name="'.$prefix.'" type="text" class="maxwidth75" maxlength="11" value="'.$formated_date.'"';
					$retstring.=($disabled?' disabled':'');
					$retstring.=' onChange="dpChangeDay(\''.$prefix.'\',\''.$langs->trans("FormatDateShortJavaInput").'\'); "';  // FormatDateShortInput for dol_print_date / FormatDateShortJavaInput that is same for javascript
					$retstring.='>';

					// Icone calendrier
					if (! $disabled)
					{
						/* Not required. Managed by option buttonImage of jquery
                		$retstring.=img_object($langs->trans("SelectDate"),'calendarday','id="'.$prefix.'id" class="datecallink"');
                		$retstring.="<script type='text/javascript'>";
                		$retstring.="jQuery(document).ready(function() {";
                		$retstring.='	jQuery("#'.$prefix.'id").click(function() {';
                		$retstring.="    	jQuery('#".$prefix."').focus();";
                		$retstring.='    });';
                		$retstring.='});';
                		$retstring.="</script>";*/
					}
					else
					{
						$retstring.='<button id="'.$prefix.'Button" type="button" class="dpInvisibleButtons">'.img_object($langs->trans("Disabled"),'calendarday','class="datecallink"').'</button>';
					}

					$retstring.='</div>';
					$retstring.='<input type="hidden" id="'.$prefix.'day"   name="'.$prefix.'day"   value="'.$sday.'">'."\n";
					$retstring.='<input type="hidden" id="'.$prefix.'month" name="'.$prefix.'month" value="'.$smonth.'">'."\n";
					$retstring.='<input type="hidden" id="'.$prefix.'year"  name="'.$prefix.'year"  value="'.$syear.'">'."\n";
				}
				else
				{
					$retstring.="Bad value of MAIN_POPUP_CALENDAR";
				}
			}
			// Show date with combo selects
			else
			{
				//$retstring.='<div class="inline-block">';
				// Day
				$retstring.='<select'.($disabled?' disabled':'').' class="flat valignmiddle maxwidth50imp" id="'.$prefix.'day" name="'.$prefix.'day">';

				if ($emptydate || $set_time == -1)
				{
					$retstring.='<option value="0" selected>&nbsp;</option>';
				}

				for ($day = 1 ; $day <= 31; $day++)
				{
					$retstring.='<option value="'.$day.'"'.($day == $sday ? ' selected':'').'>'.$day.'</option>';
				}

				$retstring.="</select>";

				$retstring.='<select'.($disabled?' disabled':'').' class="flat valignmiddle maxwidth75imp" id="'.$prefix.'month" name="'.$prefix.'month">';
				if ($emptydate || $set_time == -1)
				{
					$retstring.='<option value="0" selected>&nbsp;</option>';
				}

				// Month
				for ($month = 1 ; $month <= 12 ; $month++)
				{
					$retstring.='<option value="'.$month.'"'.($month == $smonth?' selected':'').'>';
					$retstring.=dol_print_date(mktime(12,0,0,$month,1,2000),"%b");
					$retstring.="</option>";
				}
				$retstring.="</select>";

				// Year
				if ($emptydate || $set_time == -1)
				{
					$retstring.='<input'.($disabled?' disabled':'').' placeholder="'.dol_escape_htmltag($langs->trans("Year")).'" class="flat maxwidth50imp valignmiddle" type="number" min="0" max="3000" maxlength="4" id="'.$prefix.'year" name="'.$prefix.'year" value="'.$syear.'">';
				}
				else
				{
					$retstring.='<select'.($disabled?' disabled':'').' class="flat valignmiddle maxwidth75imp" id="'.$prefix.'year" name="'.$prefix.'year">';

					for ($year = $syear - 10; $year < $syear + 10 ; $year++)
					{
						$retstring.='<option value="'.$year.'"'.($year == $syear ? ' selected':'').'>'.$year.'</option>';
					}
					$retstring.="</select>\n";
				}
				//$retstring.='</div>';
			}
		}

		if ($d && $h) $retstring.=($h==2?'<br>':' ');

		if ($h)
		{
			// Show hour
			$retstring.='<select'.($disabled?' disabled':'').' class="flat valignmiddle maxwidth50 '.($fullday?$fullday.'hour':'').'" id="'.$prefix.'hour" name="'.$prefix.'hour">';
			if ($emptyhours) $retstring.='<option value="-1">&nbsp;</option>';
			for ($hour = $heuredeb; $hour <= $heurefin; $hour++)
			{
				if (strlen($hour) < 2) $hour = "0" . $hour;
				$retstring.='<option value="'.$hour.'"'.(($hour == $shour)?' selected':'').'>'.$hour.(empty($conf->dol_optimize_smallscreen)?'':'H').'</option>';
			}
			$retstring.='</select>';
			if ($m && empty($conf->dol_optimize_smallscreen)) $retstring.=":";
		}

		if ($m)
		{
			// Show minutes
			$retstring.='<select'.($disabled?' disabled':'').' class="flat valignmiddle maxwidth50 '.($fullday?$fullday.'min':'').'" id="'.$prefix.'min" name="'.$prefix.'min">';
			if ($emptyhours) $retstring.='<option value="-1">&nbsp;</option>';
			for ($min = 0; $min < 60 ; $min+=(int)$minpas)
			{
				if (strlen($min) < 2) $min = "0" . $min;
				$retstring.='<option value="'.$min.'"'.(($min == $smin)?' selected':'').'>'.$min.(empty($conf->dol_optimize_smallscreen)?'':'').'</option>';
			}
			$retstring.='</select>';

			$retstring.='<input type="hidden" name="'.$prefix.'sec" value="'.$ssec.'">';
		}

		// Add a "Now" link
		if ($conf->use_javascript_ajax && $addnowlink)
		{
			// Script which will be inserted in the onClick of the "Now" link
			$reset_scripts = "";

			// Generate the date part, depending on the use or not of the javascript calendar
			$reset_scripts .= 'jQuery(\'#'.$prefix.'\').val(\''.dol_print_date(dol_now(),'day').'\');';
			$reset_scripts .= 'jQuery(\'#'.$prefix.'day\').val(\''.dol_print_date(dol_now(),'%d').'\');';
			$reset_scripts .= 'jQuery(\'#'.$prefix.'month\').val(\''.dol_print_date(dol_now(),'%m').'\');';
			$reset_scripts .= 'jQuery(\'#'.$prefix.'year\').val(\''.dol_print_date(dol_now(),'%Y').'\');';
			/*if ($usecalendar == "eldy")
            {
                $base=DOL_URL_ROOT.'/core/';
                $reset_scripts .= 'resetDP(\''.$base.'\',\''.$prefix.'\',\''.$langs->trans("FormatDateShortJavaInput").'\',\''.$langs->defaultlang.'\');';
            }
            else
            {
                $reset_scripts .= 'this.form.elements[\''.$prefix.'day\'].value=formatDate(new Date(), \'d\'); ';
                $reset_scripts .= 'this.form.elements[\''.$prefix.'month\'].value=formatDate(new Date(), \'M\'); ';
                $reset_scripts .= 'this.form.elements[\''.$prefix.'year\'].value=formatDate(new Date(), \'yyyy\'); ';
            }*/
			// Update the hour part
			if ($h)
			{
				if ($fullday) $reset_scripts .= " if (jQuery('#fullday:checked').val() == null) {";
				//$reset_scripts .= 'this.form.elements[\''.$prefix.'hour\'].value=formatDate(new Date(), \'HH\'); ';
				$reset_scripts .= 'jQuery(\'#'.$prefix.'hour\').val(\''.dol_print_date(dol_now(),'%H').'\');';
				if ($fullday) $reset_scripts .= ' } ';
			}
			// Update the minute part
			if ($m)
			{
				if ($fullday) $reset_scripts .= " if (jQuery('#fullday:checked').val() == null) {";
				//$reset_scripts .= 'this.form.elements[\''.$prefix.'min\'].value=formatDate(new Date(), \'mm\'); ';
				$reset_scripts .= 'jQuery(\'#'.$prefix.'min\').val(\''.dol_print_date(dol_now(),'%M').'\');';
				if ($fullday) $reset_scripts .= ' } ';
			}
			// If reset_scripts is not empty, print the link with the reset_scripts in the onClick
			if ($reset_scripts && empty($conf->dol_optimize_smallscreen))
			{
				$retstring.=' <button class="dpInvisibleButtons datenowlink" id="'.$prefix.'ButtonNow" type="button" name="_useless" value="now" onClick="'.$reset_scripts.'">';
				$retstring.=$langs->trans("Now");
				$retstring.='</button> ';
			}
		}

		// Add a "Plus one hour" link
		if ($conf->use_javascript_ajax && $addplusone)
		{
			// Script which will be inserted in the onClick of the "Add plusone" link
			$reset_scripts = "";

			// Generate the date part, depending on the use or not of the javascript calendar
			$reset_scripts .= 'jQuery(\'#'.$prefix.'\').val(\''.dol_print_date(dol_now(),'day').'\');';
			$reset_scripts .= 'jQuery(\'#'.$prefix.'day\').val(\''.dol_print_date(dol_now(),'%d').'\');';
			$reset_scripts .= 'jQuery(\'#'.$prefix.'month\').val(\''.dol_print_date(dol_now(),'%m').'\');';
			$reset_scripts .= 'jQuery(\'#'.$prefix.'year\').val(\''.dol_print_date(dol_now(),'%Y').'\');';
			// Update the hour part
			if ($h)
			{
				if ($fullday) $reset_scripts .= " if (jQuery('#fullday:checked').val() == null) {";
				$reset_scripts .= 'jQuery(\'#'.$prefix.'hour\').val(\''.dol_print_date(dol_now(),'%H').'\');';
				if ($fullday) $reset_scripts .= ' } ';
			}
			// Update the minute part
			if ($m)
			{
				if ($fullday) $reset_scripts .= " if (jQuery('#fullday:checked').val() == null) {";
				$reset_scripts .= 'jQuery(\'#'.$prefix.'min\').val(\''.dol_print_date(dol_now(),'%M').'\');';
				if ($fullday) $reset_scripts .= ' } ';
			}
			// If reset_scripts is not empty, print the link with the reset_scripts in the onClick
			if ($reset_scripts && empty($conf->dol_optimize_smallscreen))
			{
				$retstring.=' <button class="dpInvisibleButtons datenowlink" id="'.$prefix.'ButtonPlusOne" type="button" name="_useless2" value="plusone" onClick="'.$reset_scripts.'">';
				$retstring.=$langs->trans("DateStartPlusOne");
				$retstring.='</button> ';
			}
		}

		// Add a "Plus one hour" link
		if ($conf->use_javascript_ajax && $adddateof)
		{
			$tmparray=dol_getdate($adddateof);
			$retstring.=' - <button class="dpInvisibleButtons datenowlink" id="dateofinvoice" type="button" name="_dateofinvoice" value="now" onclick="jQuery(\'#re\').val(\''.dol_print_date($adddateof,'day').'\');jQuery(\'#reday\').val(\''.$tmparray['mday'].'\');jQuery(\'#remonth\').val(\''.$tmparray['mon'].'\');jQuery(\'#reyear\').val(\''.$tmparray['year'].'\');">'.$langs->trans("DateInvoice").'</a>';
		}

		if (! empty($nooutput)) return $retstring;

		print $retstring;
		return;
	}

	/**
     *  Output html form to select a third party
     *
     *	@param	string	$selected       Preselected type
     *	@param  string	$htmlname       Name of field in form
     *  @param  string	$filter         Optionnal filters criteras (example: 's.rowid <> x')
     *	@param	int		$showempty		Add an empty field
     * 	@param	int		$showtype		Show third party type in combolist (customer, prospect or supplier)
     * 	@param	int		$forcecombo		Force to use combo box
     *  @param	array	$event			Event options. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
	 *	@param	int		$flgVille		1 si on veut la ville dans le libelle du tiers
     * 	@return	string					HTML string with
     */
    function select_company($selected='',$htmlname='socid',$filter='',$showempty=0, $showtype=0, $forcecombo=0, $event=array(), $flgVille)
    {
        global $conf,$user,$langs, $db;

        $out='';
        // On recherche les societes
        $sql = "SELECT s.rowid, s.nom, s.client, s.fournisseur, s.code_client, s.code_fournisseur, s.town";
        $sql.= " FROM ".MAIN_DB_PREFIX ."societe as s";
        if (!$user->rights->societe->client->voir && !$user->societe_id) $sql .= "";
        $sql.= "  ";
	// $sql ;=" s.entity IN (".getEntity('societe', 1).") AND";
        if (! empty($user->societe_id)) $sql.= " WHERE s.rowid = '".$user->societe_id."' AND";
		elseif ($filter) $sql .= 'WHERE ';
        if ($filter) $sql.= "   (".$filter.")"; 
  //       $sql.= " WHERE fk_typent <> 2 ";  
        $sql.= " ORDER BY nom ASC";
        dol_syslog(get_class($this)."::select_company sql=".$sql);
        $resql=$db->query($sql);
        if ($resql)
        {
            if ($conf->use_javascript_ajax && $conf->global->COMPANY_USE_SEARCH_TO_SELECT && ! $forcecombo)
            {
                //$minLength = (is_numeric($conf->global->COMPANY_USE_SEARCH_TO_SELECT)?$conf->global->COMPANY_USE_SEARCH_TO_SELECT:2);
                $out.= ajax_combobox($htmlname, $event, $conf->global->COMPANY_USE_SEARCH_TO_SELECT ,0, "100%");
			}

            $out.= '<select id="'.$htmlname.'" class="flat" name="'.$htmlname.'">';
            if ($showempty) $out.= '<option value="-1"></option>';
            $num = $db->num_rows($resql);
            $i = 0;
            if ($num)        {
                while ($i < $num)      {
                    $obj = $db->fetch_object($resql);
                    $label=$obj->nom;
					if ($flgVille == 1) $label.= ' ('.$obj->town.')';
                    if ($showtype)      {
                        if ($obj->client || $obj->fournisseur) $label.=' (';
                        if ($obj->client == 1 || $obj->client == 3) $label.=$langs->trans("Customer");
                        if ($obj->client == 2 || $obj->client == 3) $label.=($obj->client==3?', ':'').$langs->trans("Prospect");
                        if ($obj->fournisseur) $label.=($obj->client?', ':'').$langs->trans("Supplier");
                        if ($obj->client || $obj->fournisseur) $label.=')';
                    }
                    if ($selected > 0 && $selected == $obj->rowid)
                    {
                        $out.= '<option value="'.$obj->rowid.'" selected="selected">'.$label.'</option>';
                    }
                    else
                    {
                        $out.= '<option value="'.$obj->rowid.'">'.$label.'</option>';
                    }
                    $i++;
                }
            }
            $out.= '</select>';
        }
        else
        {
            dol_print_error($db);
        }

        return $out;
    }//select_company

    /**
     *	Return list of all contacts (for a third party or all)
     *
     *	@param	int		$socid      	Id ot third party or 0 for all
     *	@param  string	$selected   	Id contact pre-selectionne
     *	@param  string	$htmlname  	    Name of HTML field ('none' for a not editable field)
     *	@param  int		$showempty     	0=no empty value, 1=add an empty value, 2=add line 'Internal' (used by user edit)
     *	@param  string	$exclude        List of contacts id to exclude
     *	@param	string	$limitto		Disable answers that are not id in this array list
     *	@param	string	$showfunction   Add function into label
     *	@param	string	$moreclass		Add more class to class style
     *	@param	bool	$options_only	Return options only (for ajax treatment)
     *	@param	string	$showsoc	    Add company into label
     * 	@param	int		$forcecombo		Force to use combo box
     *  @param	array	$event			Event options. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
     *	@return	 int						<0 if KO, Nb of contact in list if OK
     */
    function select_contact($socid,$selected='',$htmlname='contactid',$showempty=0,$exclude='',$limitto='',$showfunction=0, $moreclass='', $options_only=false, $showsoc=0, $forcecombo=0, $event=array())
    {
        global $conf,$langs, $db;

        $langs->load('companies');

        $out='';

        // On recherche les societes
        $sql = "SELECT sp.rowid, sp.lastname, sp.firstname, sp.poste";
        if ($showsoc > 0) {
        	$sql.= " , s.nom as company";
        }
        $sql.= " FROM ".MAIN_DB_PREFIX ."socpeople as sp";
        if ($showsoc > 0) {
        	$sql.= " LEFT OUTER JOIN  ".MAIN_DB_PREFIX ."societe as s ON s.rowid=sp.fk_soc ";
        }
        $sql.= " WHERE sp.entity IN (".getEntity('societe', 1).")";
		if (! empty($conf->global->CONTACT_HIDE_INACTIVE_IN_COMBOBOX)) $sql.= " AND sp.statut <> 0 ";
        if ($socid > 0) $sql.= " AND sp.fk_soc=".$socid;
        $sql.= " ORDER BY sp.lastname ASC";

       dol_syslog(get_class($this)."::select_contacts sql=".$sql);
        $resql=$db->query($sql);
        if ($resql)
        {
            $num=$db->num_rows($resql);

            if ($conf->use_javascript_ajax && $conf->global->CONTACT_USE_SEARCH_TO_SELECT && ! $forcecombo && ! $options_only)
            {
            	$out.= ajax_combobox($htmlname, $event, $conf->global->CONTACT_USE_SEARCH_TO_SELECT ,0, "100%");
            }

            if ($htmlname != 'none' || $options_only) $out.= '<select class="flat'.($moreclass?' '.$moreclass:'').'" id="'.$htmlname.'" name="'.$htmlname.'">';
            if ($showempty == 1) $out.= '<option value="0"'.($selected=='0'?' selected="selected"':'').'></option>';
            if ($showempty == 2) $out.= '<option value="0"'.($selected=='0'?' selected="selected"':'').'>'.$langs->trans("Internal").'</option>';
            $num = $db->num_rows($resql);
            $i = 0;
            if ($num)
            {
                include_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
                $contactstatic=new Contact($db);

                while ($i < $num)
                {
                    $obj = $db->fetch_object($resql);

                    $contactstatic->id=$obj->rowid;
                    $contactstatic->lastname=$obj->lastname;
                    $contactstatic->firstname=$obj->firstname;

                    if ($htmlname != 'none')
                    {
                        $disabled=0;
                        if (is_array($exclude) && count($exclude) && in_array($obj->rowid,$exclude)) $disabled=1;
                        if (is_array($limitto) && count($limitto) && ! in_array($obj->rowid,$limitto)) $disabled=1;
                        if ($selected && $selected == $obj->rowid)
                        {
                            $out.= '<option value="'.$obj->rowid.'"';
                            if ($disabled) $out.= ' disabled="disabled"';
                            $out.= ' selected="selected">';
                            $out.= $contactstatic->getFullName($langs);
                            if ($showfunction && $obj->poste) $out.= ' ('.$obj->poste.')';
                            if (($showsoc > 0) && $obj->company) $out.= ' - ('.$obj->company.')';
                            $out.= '</option>';
                        }
                        else
                        {
                            $out.= '<option value="'.$obj->rowid.'"';
                            if ($disabled) $out.= ' disabled="disabled"';
                            $out.= '>';
                            $out.= $contactstatic->getFullName($langs);
                            if ($showfunction && $obj->poste) $out.= ' ('.$obj->poste.')';
                            if (($showsoc > 0) && $obj->company) $out.= ' - ('.$obj->company.')';
                            $out.= '</option>';
                        }
                    }
                    else
					{
                        if ($selected == $obj->rowid)
                        {
                            $out.= $contactstatic->getFullName($langs);
                            if ($showfunction && $obj->poste) $out.= ' ('.$obj->poste.')';
                            if (($showsoc > 0) && $obj->company) $out.= ' - ('.$obj->company.')';
                        }
                    }
                    $i++;
                }
            }
            else
			{
            	$out.= '<option value="-1"'.($showempty==2?'':' selected="selected"').' disabled="disabled">'.$langs->trans($socid?"NoContactDefinedForThirdParty":"NoContactDefined").'</option>';
            }
            if ($htmlname != 'none' || $options_only)
            {
                $out.= '</select>';
            }

            $this->num = $num;
            return $out;
        }
        else
        {
            dol_print_error($db);
            return -1;
        }
    } /*select_contact*/
	
    /**
     *	Return list of all contacts (for a third party or all)
     *
     *	@param	int		$socid      	Id ot third party or 0 for all
     *	@param  string	$selected   	Id contact pre-selectionne
     *	@param  string	$htmlname  	    Name of HTML field ('none' for a not editable field)
	  *	@param  string	$filter  	    Filtre de la selection
     *	@param  int		$showempty     	0=no empty value, 1=add an empty value, 2=add line 'Internal' (used by user edit)
     *	@param  string	$exclude        List of contacts id to exclude
     *	@param	string	$limitto		Disable answers that are not id in this array list
     *	@param	string	$showfunction   Add function into label
     *	@param	string	$moreclass		Add more class to class style
     *	@param	bool	$options_only	Return options only (for ajax treatment)
     *	@param	string	$showsoc	    Add company into label
     * 	@param	int		$forcecombo		Force to use combo box
     *  @param	array	$events			Event options. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
     *	@return	 int					<0 if KO, Nb of contact in list if OK
     */
    function select_contacts($socid,$selected='',$htmlname='contactid',$filter = '', $showempty=0,$exclude='',$limitto='',$showfunction=0, $moreclass='', $options_only=false, $showsoc=0, $forcecombo=0, $events=array())
    {
        global $conf,$langs, $db;

        $langs->load('companies');

        $out='';

        // On recherche les societes
        $sql = "SELECT sp.rowid, sp.lastname, sp.statut, sp.firstname, sp.poste";
        if ($showsoc > 0) {
        	$sql.= " , s.nom as company";
        }
        $sql.= " FROM ".MAIN_DB_PREFIX ."socpeople as sp";
        if ($showsoc > 0) {
        	$sql.= " LEFT OUTER JOIN  ".MAIN_DB_PREFIX ."societe as s ON s.rowid=sp.fk_soc ";
        }
        $sql.= " WHERE sp.entity IN (".getEntity('societe', 1).")";
        if ($socid > 0) $sql.= " AND sp.fk_soc=".$socid;
		if (!empty($filter))  $sql .= ' AND ' . $filter;
        if (! empty($conf->global->CONTACT_HIDE_INACTIVE_IN_COMBOBOX)) $sql.= " AND sp.statut<>0 ";
        $sql.= " ORDER BY sp.lastname ASC";
        dol_syslog("List Suivi Client"."::select_contacts sql=".$sql);
        $resql=$db->query($sql);
        if ($resql)
        {
            $num=$db->num_rows($resql);

            if ($conf->use_javascript_ajax && $conf->global->CONTACT_USE_SEARCH_TO_SELECT && ! $forcecombo && ! $options_only)
            {
            	$out.= ajax_combobox($htmlname, $events, $conf->global->CONTACT_USE_SEARCH_TO_SELECT ,0, "100%");
            }

            if ($htmlname != 'none' || $options_only) $out.= '<select class="flat'.($moreclass?' '.$moreclass:'').'" id="'.$htmlname.'" name="'.$htmlname.'">';
            if ($showempty == 1) $out.= '<option value="0"'.($selected=='0'?' selected="selected"':'').'></option>';
            if ($showempty == 2) $out.= '<option value="0"'.($selected=='0'?' selected="selected"':'').'>'.$langs->trans("Internal").'</option>';
            $num = $db->num_rows($resql);
            $i = 0;
            if ($num)
            {
                include_once DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php';
                $contactstatic=new Contact($db);

                while ($i < $num)
                {
                    $obj = $db->fetch_object($resql);

                    $contactstatic->id=$obj->rowid;
                    $contactstatic->lastname=$obj->lastname;
                    $contactstatic->firstname=$obj->firstname;
					if ($obj->statut == 1){
                    if ($htmlname != 'none')
                    {
                        $disabled=0;
                        if (is_array($exclude) && count($exclude) && in_array($obj->rowid,$exclude)) $disabled=1;
                        if (is_array($limitto) && count($limitto) && ! in_array($obj->rowid,$limitto)) $disabled=1;
                        if ($selected && $selected == $obj->rowid)
                        {
                            $out.= '<option value="'.$obj->rowid.'"';
                            if ($disabled) $out.= ' disabled="disabled"';
                            $out.= ' selected="selected">';
                            $out.= $contactstatic->getFullName($langs);
                            if ($showfunction && $obj->poste) $out.= ' ('.$obj->poste.')';
                            if (($showsoc > 0) && $obj->company) $out.= ' - ('.$obj->company.')';
                            $out.= '</option>';
                        }
                        else
                        {
                            $out.= '<option value="'.$obj->rowid.'"';
                            if ($disabled) $out.= ' disabled="disabled"';
                            $out.= '>';
                            $out.= $contactstatic->getFullName($langs);
                            if ($showfunction && $obj->poste) $out.= ' ('.$obj->poste.')';
                            if (($showsoc > 0) && $obj->company) $out.= ' - ('.$obj->company.')';
                            $out.= '</option>';
                        }
                    }
                    else
					{
                        if ($selected == $obj->rowid)
                        {
                            $out.= $contactstatic->getFullName($langs);
                            if ($showfunction && $obj->poste) $out.= ' ('.$obj->poste.')';
                            if (($showsoc > 0) && $obj->company) $out.= ' - ('.$obj->company.')';
                        }
                    }
				}
                    $i++;
                }
            }
            else
			{
            	$out.= '<option value="-1"'.($showempty==2?'':' selected="selected"').' disabled="disabled">'.$langs->trans($socid?"NoContactDefinedForThirdParty":"NoContactDefined").'</option>';
            }
            if ($htmlname != 'none' || $options_only)
            {
                $out.= '</select>';
            }

            //$num = $num;
            return $out;
        }
        else
        {
            dol_print_error($db);
            return -1;
        }
    }// select_contacts	
	
	/**
	 *    repris de V8.0.3 pour permettre la saisie d'une ancre
	 *		Show a confirmation HTML form or AJAX popup.
	 *     Easiest way to use this is with useajax=1.
	 *     If you use useajax='xxx', you must also add jquery code to trigger opening of box (with correct parameters)
	 *     just after calling this method. For example:
	 *       print '<script type="text/javascript">'."\n";
	 *       print 'jQuery(document).ready(function() {'."\n";
	 *       print 'jQuery(".xxxlink").click(function(e) { jQuery("#aparamid").val(jQuery(this).attr("rel")); jQuery("#dialog-confirm-xxx").dialog("open"); return false; });'."\n";
	 *       print '});'."\n";
	 *       print '</script>'."\n";
	 *
	 *     @param  	string		$page        	   	Url of page to call if confirmation is OK. Can contains paramaters (param 'action' and 'confirm' will be reformated)
	 *     @param	string		$title       	   	Title
	 *     @param	string		$question    	   	Question
	 *     @param 	string		$action      	   	Action
	 *	   @param  	array		$formquestion	   	An array with complementary inputs to add into forms: array(array('label'=> ,'type'=> , ))
	 *												type can be 'hidden', 'text', 'password', 'checkbox', 'radio', 'date', 'morecss', 'moreattr', 'ancre'...
	 * 	   @param  	string		$selectedchoice  	'' or 'no', or 'yes' or '1' or '0'
	 * 	   @param  	int			$useajax		   	0=No, 1=Yes, 2=Yes but submit page with &confirm=no if choice is No, 'xxx'=Yes and preoutput confirm box with div id=dialog-confirm-xxx
	 *     @param  	int			$height          	Force height of box
	 *     @param	int			$width				Force width of box ('999' or '90%'). Ignored and forced to 90% on smartphones.
	 *     @param	int			$disableformtag		1=Disable form tag. Can be used if we are already inside a <form> section.
	 *     @return 	string      	    			HTML ajax code if a confirm ajax popup is required, Pure HTML code if it's an html form
	 */
	function formconfirm($page, $title, $question, $action, $formquestion='', $selectedchoice='', $useajax=0, $height=210, $width=500, $disableformtag=0)
	{
		global $langs,$conf;
		global $useglobalvars;

		$more='';
		$formconfirm='';
		$inputok=array();
		$inputko=array();

		// Clean parameters
		$newselectedchoice=empty($selectedchoice)?"no":$selectedchoice;
		if ($conf->browser->layout == 'phone') $width='95%';
		if (is_array($formquestion) && ! empty($formquestion))
		{
			// First add hidden fields and value
			foreach ($formquestion as $key => $input)
			{
				if (is_array($input) && ! empty($input))
				{
					if ($input['type'] == 'hidden')
					{
						$more.='<input type="hidden" id="'.$input['name'].'" name="'.$input['name'].'" value="'.dol_escape_htmltag($input['value']).'">'."\n";
					}
				}
			}
			// Now add questions
			$more.='<table class="paddingtopbottomonly" width="100%">'."\n";
			$more.='<tr><td colspan="3">'.(! empty($formquestion['text'])?$formquestion['text']:'').'</td></tr>'."\n";
			foreach ($formquestion as $key => $input)
			{
				if (is_array($input) && ! empty($input))
				{
					$size=(! empty($input['size'])?' size="'.$input['size'].'"':'');
					$moreattr=(! empty($input['moreattr'])?' '.$input['moreattr']:'');
					$morecss=(! empty($input['morecss'])?' '.$input['morecss']:'');

					if ($input['type'] == 'text')
					{
						$more.='<tr><td>'.$input['label'].'</td><td colspan="2" align="left"><input type="text" class="flat'.$morecss.'" id="'.$input['name'].'" name="'.$input['name'].'"'.$size.' value="'.$input['value'].'"'.$moreattr.' /></td></tr>'."\n";
					}
					else if ($input['type'] == 'password')
					{
						$more.='<tr><td>'.$input['label'].'</td><td colspan="2" align="left"><input type="password" class="flat'.$morecss.'" id="'.$input['name'].'" name="'.$input['name'].'"'.$size.' value="'.$input['value'].'"'.$moreattr.' /></td></tr>'."\n";
					}
					else if ($input['type'] == 'select')
					{
						$more.='<tr><td>';
						if (! empty($input['label'])) $more.=$input['label'].'</td><td valign="top" colspan="2" align="left">';
						$more.=$this->selectarray($input['name'],$input['values'],$input['default'],1,0,0,$moreattr,0,0,0,'',$morecss);
						$more.='</td></tr>'."\n";
					}
					else if ($input['type'] == 'checkbox')
					{
						$more.='<tr>';
						$more.='<td>'.$input['label'].' </td><td align="left">';
						$more.='<input type="checkbox" class="flat'.$morecss.'" id="'.$input['name'].'" name="'.$input['name'].'"'.$moreattr;
						if (! is_bool($input['value']) && $input['value'] != 'false' && $input['value'] != '0') $more.=' checked';
						if (is_bool($input['value']) && $input['value']) $more.=' checked';
						if (isset($input['disabled'])) $more.=' disabled';
						$more.=' /></td>';
						$more.='<td align="left">&nbsp;</td>';
						$more.='</tr>'."\n";
					}
					else if ($input['type'] == 'radio')
					{
						$i=0;
						foreach($input['values'] as $selkey => $selval)
						{
							$more.='<tr>';
							if ($i==0) $more.='<td class="tdtop">'.$input['label'].'</td>';
							else $more.='<td>&nbsp;</td>';
							$more.='<td width="20"><input type="radio" class="flat'.$morecss.'" id="'.$input['name'].'" name="'.$input['name'].'" value="'.$selkey.'"'.$moreattr;
							if ($input['disabled']) $more.=' disabled';
							$more.=' /></td>';
							$more.='<td align="left">';
							$more.=$selval;
							$more.='</td></tr>'."\n";
							$i++;
						}
					}
					else if ($input['type'] == 'date')
					{
						$more.='<tr><td>'.$input['label'].'</td>';
						$more.='<td colspan="2" align="left">';
						$more.=$this->select_date($input['value'],$input['name'],0,0,0,'',1,0,1);
						$more.='</td></tr>'."\n";
						$formquestion[] = array('name'=>$input['name'].'day');
						$formquestion[] = array('name'=>$input['name'].'month');
						$formquestion[] = array('name'=>$input['name'].'year');
						$formquestion[] = array('name'=>$input['name'].'hour');
						$formquestion[] = array('name'=>$input['name'].'min');
					}
					else if ($input['type'] == 'other')
					{
						$more.='<tr><td>';
						if (! empty($input['label'])) $more.=$input['label'].'</td><td colspan="2" align="left">';
						$more.=$input['value'];
						$more.='</td></tr>'."\n";
					}

					else if ($input['type'] == 'onecolumn')
					{
						$more.='<tr><td colspan="3" align="left">';
						$more.=$input['value'];
						$more.='</td></tr>'."\n";
					}
//CCA 12/12/2018 - Ancre					
					else if ($input['type'] == 'ancre')
					{
						$urlmoreancre.='#';
						$urlmoreancre.=$input['value'];
					}
// Fin Modif CCA 12/12/2018
				}
			}
			$more.='</table>'."\n";
		}

		// JQUI method dialog is broken with jmobile, we use standard HTML.
		// Note: When using dol_use_jmobile or no js, you must also check code for button use a GET url with action=xxx and check that you also output the confirm code when action=xxx
		// See page product/card.php for example
		if (! empty($conf->dol_use_jmobile)) $useajax=0;
		if (empty($conf->use_javascript_ajax)) $useajax=0;

		if ($useajax)
		{
			$autoOpen=true;
			$dialogconfirm='dialog-confirm';
			$button='';
			if (! is_numeric($useajax))
			{
				$button=$useajax;
				$useajax=1;
				$autoOpen=false;
				$dialogconfirm.='-'.$button;
			}
			$pageyes=$page.(preg_match('/\?/',$page)?'&':'?').'action='.$action.'&confirm=yes';
			$pageno=($useajax == 2 ? $page.(preg_match('/\?/',$page)?'&':'?').'confirm=no':'');
			// Add input fields into list of fields to read during submit (inputok and inputko)
			if (is_array($formquestion))
			{
				foreach ($formquestion as $key => $input)
				{
					//print "xx ".$key." rr ".is_array($input)."<br>\n";
					if (is_array($input) && isset($input['name'])) array_push($inputok,$input['name']);
					if (isset($input['inputko']) && $input['inputko'] == 1) array_push($inputko,$input['name']);
				}
			}
			// Show JQuery confirm box. Note that global var $useglobalvars is used inside this template
			$formconfirm.= '<div id="'.$dialogconfirm.'" title="'.dol_escape_htmltag($title).'" style="display: none;">';
			if (! empty($more)) {
				$formconfirm.= '<div class="confirmquestions">'.$more.'</div>';
			}
			$formconfirm.= ($question ? '<div class="confirmmessage">'.img_help('','').' '.$question . '</div>': '');
			$formconfirm.= '</div>'."\n";

			$formconfirm.= "\n<!-- begin ajax form_confirm page=".$page." -->\n";
			$formconfirm.= '<script type="text/javascript">'."\n";
			$formconfirm.= 'jQuery(document).ready(function() {
            $(function() {
            	$( "#'.$dialogconfirm.'" ).dialog(
            	{
                    autoOpen: '.($autoOpen ? "true" : "false").',';
					if ($newselectedchoice == 'no')
					{
						$formconfirm.='
						open: function() {
            				$(this).parent().find("button.ui-button:eq(2)").focus();
						},';
					}
					$formconfirm.='
                    resizable: false,
                    height: "'.$height.'",
                    width: "'.$width.'",
                    modal: true,
                    closeOnEscape: false,
                    buttons: {
                        "'.dol_escape_js($langs->transnoentities("Yes")).'": function() {
                        	var options="";
                        	var inputok = '.json_encode($inputok).';
                         	var pageyes = "'.dol_escape_js(! empty($pageyes)?$pageyes:'').'";
                         	if (inputok.length>0) {
                         		$.each(inputok, function(i, inputname) {
                         			var more = "";
                         			if ($("#" + inputname).attr("type") == "checkbox") { more = ":checked"; }
                         		    if ($("#" + inputname).attr("type") == "radio") { more = ":checked"; }
                         			var inputvalue = $("#" + inputname + more).val();
                         			if (typeof inputvalue == "undefined") { inputvalue=""; }
                         			options += "&" + inputname + "=" + encodeURIComponent(inputvalue);
                         		});
                         	}
                         	var urljump = pageyes + (pageyes.indexOf("?") < 0 ? "?" : "") + options + "'.$urlmoreancre.'";
                         	//alert(urljump);
            				if (pageyes.length > 0) { location.href = urljump; }
                            $(this).dialog("close");
                        },
                        "'.dol_escape_js($langs->transnoentities("No")).'": function() {
                        	var options = "";
                         	var inputko = '.json_encode($inputko).';
                         	var pageno="'.dol_escape_js(! empty($pageno)?$pageno:'').'";
                         	if (inputko.length>0) {
                         		$.each(inputko, function(i, inputname) {
                         			var more = "";
                         			if ($("#" + inputname).attr("type") == "checkbox") { more = ":checked"; }
                         			var inputvalue = $("#" + inputname + more).val();
                         			if (typeof inputvalue == "undefined") { inputvalue=""; }
                         			options += "&" + inputname + "=" + encodeURIComponent(inputvalue);
                         		});
                         	}
                         	var urljump=pageno + (pageno.indexOf("?") < 0 ? "?" : "") + options;
                         	//alert(urljump);
            				if (pageno.length > 0) { location.href = urljump; }
                            $(this).dialog("close");
                        }
                    }
                }
                );

            	var button = "'.$button.'";
            	if (button.length > 0) {
                	$( "#" + button ).click(function() {
                		$("#'.$dialogconfirm.'").dialog("open");
        			});
                }
            });
            });
            </script>';
			$formconfirm.= "<!-- end ajax form_confirm -->\n";
		}
		else
		{
			$formconfirm.= "\n<!-- begin form_confirm page=".$page." -->\n";

			if (empty($disableformtag)) $formconfirm.= '<form method="POST" action="'.$page.'" class="notoptoleftroright">'."\n";

			$formconfirm.= '<input type="hidden" name="action" value="'.$action.'">'."\n";
			if (empty($disableformtag)) $formconfirm.= '<input type="hidden" name="token" value="'.newtoken().'">'."\n";

			$formconfirm.= '<table width="100%" class="valid">'."\n";

			// Line title
			$formconfirm.= '<tr class="validtitre"><td class="validtitre" colspan="3">'.img_picto('','recent').' '.$title.'</td></tr>'."\n";

			// Line form fields
			if ($more)
			{
				$formconfirm.='<tr class="valid"><td class="valid" colspan="3">'."\n";
				$formconfirm.=$more;
				$formconfirm.='</td></tr>'."\n";
			}

			// Line with question
			$formconfirm.= '<tr class="valid">';
			$formconfirm.= '<td class="valid">'.$question.'</td>';
			$formconfirm.= '<td class="valid">';
			$formconfirm.= $this->selectyesno("confirm",$newselectedchoice);
			$formconfirm.= '</td>';
			$formconfirm.= '<td class="valid" align="center"><input class="button valignmiddle" type="submit" value="'.$langs->trans("Validate").'"></td>';
			$formconfirm.= '</tr>'."\n";

			$formconfirm.= '</table>'."\n";

			if (empty($disableformtag)) $formconfirm.= "</form>\n";
			$formconfirm.= '<br>';

			$formconfirm.= "<!-- end form_confirm -->\n";
		}

		return $formconfirm;
	}

	
	/**
	 * Affiche un champs select contenant la liste des formations disponibles.
	 *
	 * @param int $selectid ࠰reselectionner
	 * @param string $htmlname select field
	 * @param string $sort Value to show/edit (not used in this function)
	 * @param int $showempty empty field
	 * @param int $forcecombo use combo box
	 * @param array $event
	 * @return string select field
	 */
		
	function select_session($selectid, $htmlname = 'Activite', $sort = 'date', $showempty = 0, $forcecombo = 0, $event = array(), $filters = array()) 
	{	
		global $conf, $user, $langs, $db, $bull, $id_act;
		$out = '';
		
		if ($sort == 'intitule')
			$order = 'intitule_custo';
		elseif ($sort == 'passe' ) $order = 's.dated desc, heured asc';
		else	$order = 's.dated asc, heured asc' ;
	
		$sql = "SELECT s.rowid, intitule_custo, dated, heured, ref_interne, nb_place";		
		$sql .= " FROM ".MAIN_DB_PREFIX."agefodd_session_calendrier, ";
		$sql .= MAIN_DB_PREFIX."agefodd_session as s left join ".MAIN_DB_PREFIX."agefodd_place  as p on p.rowid = fk_session_place " ;
		$sql .= " left join ".MAIN_DB_PREFIX."agefodd_session_stagiaire as st on st.fk_session_agefodd = s.rowid ";
		$sql .= " WHERE s.status <4 ";
		$sql .= " AND fk_agefodd_session = s.rowid";	
		$sql .= " AND s.entity =1 ";
		
		if (empty($bull->type_session_cgl)) 	$sql .= " AND ( isnull(s.type_session) or s.type_session = 1 ) ";
		else {
			$local_type_session_agf = $bull->type_session_cgl - 1;
			if ($local_type_session_agf >=0 )
			 		$sql .= " AND (s.type_session = ". $local_type_session_agf." or isnull(s.type_session)) ";
			else	$sql .= " AND s.type_session = ". $local_type_session_agf."  ";
		}

		if (count($filters)>0) {
			foreach($filters as $filter)
				$sql .= $filter;
		}		
		$sql .= " group by s.rowid, intitule_custo , dated,heured, nb_place";
		$sql .= " ORDER BY " . $order;
		
		dol_syslog ( get_class ( $this ) . "::select_session sql=" . $sql );
		$resql = $db->query ( $sql );
		if ($resql) {
			if ($conf->use_javascript_ajax && $conf->global->AGF_TRAINING_USE_SEARCH_TO_SELECT && ! $forcecombo) {
				$out .= ajax_combobox ( $htmlname, $event );
			}
			
			$out .= '<select id="' . $htmlname . '" class="flat" name="' . $htmlname . '">';
			if ($showempty)
				$out .= '<option value="-1"></option>';
			$num = $db->num_rows ( $resql );
			$i = 0;
			if ($num) {
				$wdep = new CglDepart ($this->db);
				while ( $i < $num ) {
					$obj = $db->fetch_object ( $resql );
					if ($i == 0)  	$id_act = $obj->rowid;					
					$nbinscrit = $wdep->NbPartDep(2,$obj->rowid);			
					$nbpreinscrit = $wdep->NbPartDep(1,$obj->rowid);
					if (empty($nbinscrit)) $nbinscrit	= 0;
					if (empty($nbpreinscrit)) $nbpreinscrit	= 0;
					//$label  = $obj->intitule_custo.' - '.dol_print_date($obj->dated,'%D %d/%m').'-';
					//$label .= dol_print_date($obj->heured,'%H').' - '.$obj->ref_interne;
					$wf = new CglFonctionCommune($this->db);
					$date_fr = $wf->transfDateFr($obj->dated);
					//$jourSem = substr($wf->transfDateJourSem($date_fr),0,3);
					$jourSem = $wf->transfDateJourSem($date_fr);
					$label  = $obj->intitule_custo.' - '.$jourSem.' '.$wf->transfDateFrCourt($obj->dated).'-';
					$label .= $wf->transfHeureFr($obj->heured).' - '.$obj->ref_interne;
					unset ($wf);
					$som=$nbpreinscrit+$nbinscrit;
					$label .= ' - ('.$obj->nb_place.'/'.$som.')';
					//$label= $obj->intitule_custo.'_'.dol_print_date($obj->dated,'%D %d/%m').
					
					if ($selectid > 0 && $selectid == $obj->rowid) {
						$out .= '<option value="' . $obj->rowid . '" selected="selected"  >'. $label . '</option>';
						$id_act = $obj->rowid;
					} else {
						$out .= '<option value="' . $obj->rowid . '">' . $label . '</option>';
					}
					$i ++;
				}
			}
			$out .= '</select>';
		} else {
			dol_print_error ( $db );
		}
		$db->free ( $resql );
		return $out;
	
	} /* select_session */
	/**
	 * affiche un champs select contenant la liste des sites de formation déjà référéencés.
	 *
	 * @param int $selectid Id de la session selectionner
	 * @param string $htmlname Name of HTML control
	 * @param int $showempty empty field
	 * @param int $forcecombo use combo box
	 * @param array $event
	 * @return string The HTML control
	 */
	function select_site($selectid, $htmlname = 'place', $showempty = 0, $forcecombo = 0, $event = '')
	{
		global $conf, $langs;
		
		$sql = "SELECT p.rowid, p.ref_interne";
		$sql .= " FROM " . MAIN_DB_PREFIX . "agefodd_place as p";
		$sql .= " WHERE archive = 0";
		$sql .= " AND p.entity IN (" . getEntity('agsession') . ")";
		$sql .= " ORDER BY p.ref_interne";
		
		dol_syslog(get_class($this) . "::select_site_forma ", LOG_DEBUG);
		$result = $this->db->query($sql);
		
		if ($result) {
			$out .= '<select id="' . $htmlname . '" class="flat" name="' . $htmlname . '" ';
			if(!empty($event)) $out .= $event;
			$out .= '>';
			if ($showempty)
				$out .= '<option value="-1"></option>';
			$num = $this->db->num_rows($result);
			$i = 0;
			if ($num) {
				while ( $i < $num ) {
					$obj = $this->db->fetch_object($result);
					$label = $obj->ref_interne;
					
					if ($selectid > 0 && $selectid == $obj->rowid) {
						$out .= '<option value="' . $obj->rowid . '" selected="selected">' . $label . '</option>';
					} else {
						$out .= '<option value="' . $obj->rowid . '">' . $label . '</option>';
					}
					$i ++;
				}
			}
			$out .= '</select>';
			$this->db->free($result);
			return $out;
		} else {
			$this->error = "Error " . $this->db->lasterror();
			dol_syslog(get_class($this) . "::select_site_forma " . $this->error, LOG_ERR);
			return - 1;
		}
	}
    /**
     *		Update bank account record in database - update complet 
     *
     *		@param	object	$objbank		Object to update			
     *		@param	User	$user			Object user making update
     *		@param	User	$user			Object user making update
     *		@param 	int		$notrigger		0=Disable all triggers
     *		@return	int						<0 if KO, >0 if OK
     */
    function bankline_update($objbank, $user,$notrigger=0)
    {
        $this->db->begin();
        $sql = "UPDATE ".MAIN_DB_PREFIX."bank SET";
        $sql.= " amount = ".price2num($objbank->amount);
        if (!empty ($objbank->num_chq)) 	$sql.= ", num_chq = '".$objbank->num_chq."'";
        if (!empty ($objbank->label))   	$sql.= ", label = ".$objbank->label;
        if (!empty ($objbank->fk_account))	$sql.= ", fk_account = ".$objbank->fk_account;
        if (!empty ($objbank->fk_type))		$sql.= ", fk_type = ".$objbank->fk_type;
        if (!empty ($objbank->num_releve))	$sql.= ", num_releve = ".$objbank->num_releve;
        if (!empty ($objbank->rappro))		$sql.= ", rappro = ".$objbank->rappro;
        if (!empty ($objbank->note))		$sql.= ', note = "'.$objbank->note.'"';
        if (!empty ($objbank->fk_bordereau)) $sql.= ", fk_bordereau = ".$objbank->fk_bordereau;
        if (!empty ($objbank->banque)) 		$sql.= ', banque = "'.$objbank->banque.'"';
        if (!empty ($objbank->emetteur))	$sql.= ', emetteur = "'.$objbank->emetteur.'"';
        if (!empty ($objbank->fk_user_author)) $sql.= ', fk_user_author = '.$objbank->fk_user_author;
        if (!empty ($objbank->fk_user_rappro)) $sql.= ", fk_user_rappro = ".$objbank->fk_user_rappro;
        if (!empty ($objbank->datev)) 		$sql.= ', datev="'.$this->db->idate($objbank->datev).'"';
        if (!empty ($objbank->dateo)) 			$sql.= ', dateo="'.$this->db->idate($objbank->dateo).'"';
        $sql.= " WHERE rowid = ".$objbank->rowid;
        dol_syslog(get_class($this)."::update sql=".$sql);
        $resql = $this->db->query($sql);
        if ($resql)
        {
            $this->db->commit();
            return 1;
        }
        else
        {
            $this->db->rollback();
            $this->error=$this->db->error();
            dol_syslog(get_class($this)."::update ".$this->error, LOG_ERR);
            return -1;
        }
    }
	

	/**
	 * Affiche un champs select contenant la liste des formations disponibles.
	 *
	 * @param int $selectid à preselectionner
	 * @param string $htmlname select field
	 * @param string $sort Value to show/edit (not used in this function)
	 * @param int $showempty empty field
	 * @param int $forcecombo use combo box
	 * @param array $event
	 * @return string select field
	 */
	function select_formation($selectid, $htmlname = 'formation', $sort = 'intitule', $showempty = 0,  $event = array(), $filters = array()) {
		global $conf, $user, $langs;
		
		$out = '';
		
		if ($sort == 'code')
			$order = 'c.ref';
		else
			$order = 'c.intitule';
		
		$sql = "SELECT c.rowid, c.intitule, c.ref";
		$sql .= " FROM " . MAIN_DB_PREFIX . "agefodd_formation_catalogue as c";
		$sql .= " WHERE archive = 0";
		$sql .= " AND entity IN (" . getEntity('agsession') . ")";
		if (count($filters) > 0) {
			foreach ( $filters as $filter )
				$sql .= $filter;
		}
		$sql .= " ORDER BY " . $order;
		
		dol_syslog(get_class($this) . "::select_formation sql=" . $sql, LOG_DEBUG);
		$resql = $this->db->query($sql);
		if ($resql) {
			
			$out .= '<select id="' . $htmlname . '" class="flat" name="' . $htmlname . '" '.$event.' >';
			if ($showempty)
				$out .= '<option value="-1"></option>';
			$num = $this->db->num_rows($resql);
			$i = 0;
			if ($num) {
				while ( $i < $num ) {
					$obj = $this->db->fetch_object($resql);
					$label = $obj->intitule;
					
					if ($selectid > 0 && $selectid == $obj->rowid) {
						$out .= '<option value="' . $obj->rowid . '" selected="selected">' . $label . '</option>';
					} else {
						$out .= '<option value="' . $obj->rowid . '">' . $label . '</option>';
					}
					$i ++;
				}
			}
			$out .= '</select>';
		} else {
			dol_print_error($this->db);
		}
		$this->db->free($resql);
		return $out;
	}

	/**
	 * Convert a html select field into an ajax combobox.
	 * Use ajax_combobox() only for small combo list! If not, use instead ajax_autocompleter().
	 * TODO: It is used when COMPANY_USE_SEARCH_TO_SELECT and CONTACT_USE_SEARCH_TO_SELECT are set by html.formcompany.class.php. Should use ajax_autocompleter instead like done by html.form.class.php for select_produits.
	 *
	 * @param	string	$htmlname					Name of html select field ('myid' or '.myclass')
	 * @param	array	$events						More events option. Example: array(array('method'=>'getContacts', 'url'=>dol_buildpath('/core/ajax/contacts.php',1), 'htmlname'=>'contactid', 'params'=>array('add-customer-contact'=>'disabled')))
	 * @param  	int		$minLengthToAutocomplete	Minimum length of input string to start autocomplete
	 * @param	int		$forcefocus					Force focus on field
	 * @return	string								Return html string to convert a select field into a combo, or '' if feature has been disabled for some reason.
	 */
	function ajax_combobox($htmlname, $events=array(), $minLengthToAutocomplete=0, $forcefocus=0, $parwidth="")
	{
		global $conf;
		if (! empty($conf->browser->phone)) return '';	// select2 disabled for smartphones with standard browser (does not works, popup appears outside screen)
		if (! empty($conf->dol_use_jmobile)) return '';	// select2 works with jmobile but it breaks the autosize feature of jmobile.
		if (! empty($conf->global->MAIN_DISABLE_AJAX_COMBOX)) return '';
		if (empty($conf->use_javascript_ajax)) return '';
		if (empty($minLengthToAutocomplete)) $minLengthToAutocomplete=0;

		$tmpplugin='select2';
		$msg='<!-- JS CODE TO ENABLE '.$tmpplugin.' for id '.$htmlname.' -->
			  <script type="text/javascript">
				$(document).ready(function () {
					$(\''.(preg_match('/^\./',$htmlname)?$htmlname:'#'.$htmlname).'\').'.$tmpplugin.'({
						dir: \'ltr\',';
						
		if (empty($parwidth)) $msg.='    			 width: \'resolve\',';		/* off or resolve */ 
		else  $msg.='    			 width: \''.$parwidth.'\',';
		 $msg.=' 		minimumInputLength: '.$minLengthToAutocomplete.'
					})';
		if ($forcefocus) $msg.= '.select2(\'focus\')';
		$msg.= ';'."\n";

		if (count($events))
		{
			$msg.= '
				jQuery("#'.$htmlname.'").change(function () {
					var obj = '.json_encode($events).';
					$.each(obj, function(key,values) {
						if (values.method.length) {
							runJsCodeForEvent'.$htmlname.'(values);
						}
					});
				});

				function runJsCodeForEvent'.$htmlname.'(obj) {
					var id = $("#'.$htmlname.'").val();
					var method = obj.method;
					var url = obj.url;
					var htmlname = obj.htmlname;
					var showempty = obj.showempty;
					$.getJSON(url,
							{
								action: method,
								id: id,
								htmlname: htmlname,
								showempty: showempty
							},
							function(response) {
								$.each(obj.params, function(key,action) {
									if (key.length) {
										var num = response.num;
										if (num > 0) {
											$("#" + key).removeAttr(action);
										} else {
											$("#" + key).attr(action, action);
										}
									}
								});
								$("select#" + htmlname).html(response.value);
								if (response.num) {
									var selecthtml_str = response.value;
									var selecthtml_dom=$.parseHTML(selecthtml_str);
									$("#inputautocomplete"+htmlname).val(selecthtml_dom[0][0].innerHTML);
								} else {
									$("#inputautocomplete"+htmlname).val("");
								}
								$("select#" + htmlname).change();	/* Trigger event change */
							}
					);
				}';
		}

		$msg.= '});'."\n";
		$msg.= "</script>\n";

		return $msg;
	}

	function ajax_autocompleter($selected, $htmlname, $url, $urloption='', $minLength=2, $autoselect=0, $ajaxoptions=array())
	{
		global $TypeListe;		
		
    if (empty($minLength)) $minLength=1;

    // Input search_htmlname is original field
    // Input htmlname is a second input field used when using ajax autocomplete.
	$script = '<input type="hidden" name="'.$htmlname.'" id="'.$htmlname.'" value="'.$selected.'" />';

	$script.= '<!-- Javascript code for autocomplete of field '.$htmlname.' -->'."\n";
	$script.= '<script type="text/javascript">'."\n";
	$script.= '$(document).ready(function() {
					var autoselect = '.$autoselect.';
					var options = '.json_encode($ajaxoptions).';
									
					/* Interrogation pour liste des tiers */
					$("input#search_'.$htmlname.'").autocomplete({
    					source: function( request, response ) {
							$.get("'.$url.($urloption?'?'.$urloption:'').'", { '.$htmlname.': request.term }, function(data){
							if (data != null)
								{
									response($.map( data, function(item) {
										if (autoselect == 1 && data.length == 1) {
											$("#search_'.$htmlname.'").val(item.value);
											$("#'.$htmlname.'").val(item.key).trigger("change");
										}
										var label = item.label.toString();
										var update = {};
										if (options.update) {
											$.each(options.update, function(key, value) {
												update[key] = item[value];
											});
										}
										var textarea = {};
										if (options.update_textarea) {
											$.each(options.update_textarea, function(key, value) {
												textarea[key] = item[value];
											});
										}
	
										return { label: label, value: item.value, id: item.key, update: update, textarea: textarea, disabled: item.disabled }
									}));
								}
								else console.error("Error: Ajax url '.$url.($urloption?'?'.$urloption:'').' has returned an empty page. Should be an empty json array.");
							}, "json");
						},
						dataType: "json",
    					minLength: '.$minLength.',
    					select: function( event, ui ) {	
    						$("#'.$htmlname.'").val(ui.item.id).trigger("change");							
    						$("#search_'.$htmlname.'").trigger("change");	
    					}
    					,delay: 500
					}).data("ui-autocomplete")._renderItem = function( ul, item ) {
						return $("<li>")
						.data( "ui-autocomplete-item", item ) // jQuery UI > 1.10.0
						.append( \'<a><span class="tag">\' + item.label + "</span></a>" )
						.appendTo(ul);
					};


  				});';
	$script.= '</script>';

	return $script;
}
		
	/**
	 * Show EMail link à partir de dol_print_email
	 *
	 * @param	string		$email			EMail to show (only email, without 'Name of recipient' before)
	 * @param 	string		$cid 			Id du contact
	 * @param 	int			$socid 			Id of third party if known
	 * @param 	int			$corps			Contenu du mail, pour passer au moins le nom du correspondant
	 * @param 	int			$addlink		0=no link, 1=email has a html email link (+ link to create action if constant AGENDA_ADDACTIONFOREMAIL is on)
	 * @param	int			$max			Max number of characters to show
	 * @param	int			$showinvalid	Show warning if syntax email is wrong
	 * @param	int			$withpicto		Show picto
	 * @param	int			$withmail		Show mail (si 0, le lien est sur l'image)
	 * @return	string						HTML Link
	 */
	function dol_print_email_image($email,$cid='',$socid, $corps, $addlink=0,$max=64,$showinvalid=1, $withmail = 1)
	{
		global $conf,$user,$langs;

		$newemail=$email;

		if (empty($email)) return '&nbsp;';
		if (! empty($addlink))
		{	
			$newemail='<a style="text-overflow: ellipsis;" href="';
			if (! preg_match('/^mailto:/i',$email)) $newemail.='mailto:'.$email.'?body='.$corps.'">';
			$newemail.=img_picto($email, 'object_email.png');
			//if ($withmail) $newemail.=" ".dol_trunc($email,$max);
			//$newemail.='">';
			$newemail.='</a>';	
			if ($showinvalid && ! isValidEmail($email))
			{
				$langs->load("errors");
				$newemail.=img_warning($langs->trans("ErrorBadEMail",$email));
			}
			/*
			if (($cid || $socid) && ! empty($conf->agenda->enabled) && $user->rights->agenda->myactions->create)
			{
				$type='AC_EMAIL'; $link='';
				if (! empty($conf->global->AGENDA_ADDACTIONFOREMAIL)) $link='<a href="'.DOL_URL_ROOT.'/comm/action/card.php?action=create&amp;backtopage=1&amp;actioncode='.$type.'&amp;contactid='.$cid.'&amp;socid='.$socid.'">'.img_object($langs->trans("AddAction"),"calendar").'</a>';
				if ($link) $newemail='<div>'.$newemail.' '.$link.'</div>';
			}	
			*/
			
		}
		else
		{
			if ($showinvalid && ! isValidEmail($email))
			{
				$langs->load("errors");
				$newemail.=img_warning($langs->trans("ErrorBadEMail",$email));
			}
		}
		//return '<div class="nospan float" style="margin-right: 10px">'.$newemail.'</div>';
		return $newemail;
}
	/*
	* repris de DFolibarr V3.6.1 dans html.formfile.php
	*
	*  
     *      Return a string to show the box with list of available documents for object.
     *      This also set the property $this->numoffiles
     *
     *      @param      string				$modulepart         Module the files are related to ('propal', 'facture', 'facture_fourn', 'mymodule', 'mymodule_temp', ...)
     *      @param      string				$modulesubdir       Existing (so sanitized) sub-directory to scan (Example: '0/1/10', 'FA/DD/MM/YY/9999'). Use '' if file is not into subdir of module.
     *      @param      string				$filedir            Directory to scan
     *      @param      string				$urlsource          Url of origin page (for return)
     *      @param      int					$genallowed         Generation is allowed (1/0 or array list of templates)
     *      @param      int					$delallowed         Remove is allowed (1/0)
     *      @param      string				$modelselected      Model to preselect by default
     *      @param      string				$allowgenifempty	Allow generation even if list of template ($genallowed) is empty (show however a warning)
     *      @param      string				$forcenomultilang	Do not show language option (even if MAIN_MULTILANGS defined)
     *      @param      int					$iconPDF            Obsolete, see getDocumentsLink
     * 		@param		int					$maxfilenamelength	Max length for filename shown
     * 		@param		string				$noform				Do not output html form tags
     * 		@param		string				$param				More param on http links
     * 		@param		string				$title				Title to show on top of form
     * 		@param		string				$buttonlabel		Label on submit button
     * 		@param		string				$codelang			Default language code to use on lang combo box if multilang is enabled
     * 		@param		string				$morepicto			Add more HTML content into cell with picto
     * 		@return		string              					Output string with HTML array of documents (might be empty string)
	*/
	  function showdocuments($modulepart,$modulesubdir,$filedir,$urlsource,$genallowed,$delallowed=0,$modelselected='',$allowgenifempty=1,$forcenomultilang=0,$iconPDF=0,$maxfilenamelength=28,$noform=0,$param='',$title='',$buttonlabel='',$codelang='',$morepicto='')
    {
        global $langs, $conf, $user, $hookmanager;
        global $form, $bc;	

        if (! is_object($form)) $form=new Form($this->db);
        // filedir = $conf->...->dir_ouput."/".get_exdir(id)
        include_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

        // For backward compatibility
        if (! empty($iconPDF)) {
        	return $this->getDocumentsLink($modulepart, $modulesubdir, $filedir);
        }
        $printer = (!empty($user->rights->printipp->read) && !empty($conf->printipp->enabled))?true:false;
        $hookmanager->initHooks(array('formfile'));
        $forname='builddoc';
        $out='';
        $var=true;
        $headershown=0;
        $showempty=0;
        $i=0;

        $titletoshow=$langs->trans("Documents");
        if (! empty($title)) $titletoshow=$title;

        $out.= "\n".'<!-- Start show_document -->'."\n";
        //print 'filedir='.$filedir;
        // Show table
        if ($genallowed)
        {
            $modellist=array();
			// recherche liste des modèle, avec un exemple pour facture (plusieurs modèle
			// et la construction de la liste pour contrat		
			if ($modulepart == 'facture')            {
				if (is_array($genallowed)) $modellist=$genallowed;
                else
                {
                    include_once DOL_DOCUMENT_ROOT.'/core/modules/facture/modules_facture.php';
                    $modellist=ModelePDFFactures::liste_modeles($this->db);
                }
            }    

            else       {
                // For normalized standard modules
                $file=dol_buildpath('/core/modules/'.$modulepart.'/modules_'.$modulepart.'.php',0);
                if (file_exists($file))                {
                    if (!class_exists('Modele'.ucfirst($modulepart))) $res=include_once $file;
               }
                // For normalized external modules
                else              {
					$file=dol_buildpath('/'.$modulepart.'/core/modules/'.$modulepart.'/modules_'.$modulepart.'.php',0);
					if (!class_exists('Modele'.ucfirst($modulepart))) $res=include_once $file;
               }
                $class='Modele'.ucfirst($modulepart);
                if (class_exists($class))                {
                    $modellist=call_user_func($class.'::liste_modeles',$this->db);
                }
                else                {
                    dol_print_error($this->db,'Bad value for modulepart');
                    return -1;
                }
            }
            $headershown=1;

            $buttonlabeltoshow=$buttonlabel;
            if (empty($buttonlabel)) $buttonlabel=$langs->trans('Generate');

            if (empty($noform)) $out.= '<form action="'.$urlsource.(empty($conf->global->MAIN_JUMP_TAG)?'':'#builddoc').'" name="'.$forname.'" id="'.$forname.'_form" method="post">';
            $out.= '<input type="hidden" name="action" value="builddoc">';
            $out.= '<input type="hidden" name="token" value="'.newtoken().'">';

            $out.= '<div class="titre">'.$titletoshow.'</div>';
            $out.= '<table class="liste formdoc noborder" summary="listofdocumentstable" width="100%">';

            $out.= '<tr class="liste_titre">';

            // Model
            if (! empty($modellist))
            {
                $out.= '<th align="center" class="formdoc liste_titre">';
                $out.= '<span class="hideonsmartphone">'.$langs->trans('Model').' </span>';
                if (is_array($modellist) && count($modellist) == 1)    // If there is only one element
                {
                    $arraykeys=array_keys($modellist);
                    $modelselected=$arraykeys[0];
                }
                $out.= $form->selectarray('model',$modellist,$modelselected,$showempty,0,0);
                $out.= '</th>';
            }
            else
            {
                $out.= '<th align="left" class="formdoc liste_titre">';
                $out.= $langs->trans("Files");
                $out.= '</th>';
            }

            // Language code (if multilang)
            $out.= '<th align="center" class="formdoc liste_titre">';
            if (($allowgenifempty || (is_array($modellist) && count($modellist) > 0)) && $conf->global->MAIN_MULTILANGS && ! $forcenomultilang && (! empty($modellist) || $showempty))
            {
                include_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
                $formadmin=new FormAdmin($this->db);
                $defaultlang=$codelang?$codelang:$langs->getDefaultLang();
                $out.= $formadmin->select_language($defaultlang);
            }
            else
            {
                $out.= '&nbsp;';
            }
            $out.= '</th>';

            // Button
            $addcolumforpicto=($delallowed || $printer || $morepicto);
            $out.= '<th align="center" colspan="'.($addcolumforpicto?'2':'1').'" class="formdocbutton liste_titre">';
            $genbutton = '<input class="button" id="'.$forname.'_generatebutton" name="'.$forname.'_generatebutton"';
            $genbutton.= ' type="submit" value="'.$buttonlabel.'"';
            if (! $allowgenifempty && ! is_array($modellist) && empty($modellist)) $genbutton.= ' disabled="disabled"';
            $genbutton.= '>';
            if ($allowgenifempty && ! is_array($modellist) && empty($modellist) && empty($conf->dol_no_mouse_hover) && $modulepart != 'unpaid')
            {
               	$langs->load("errors");
               	$genbutton.= ' '.img_warning($langs->transnoentitiesnoconv("WarningNoDocumentModelActivated"));
            }
            if (! $allowgenifempty && ! is_array($modellist) && empty($modellist) && empty($conf->dol_no_mouse_hover) && $modulepart != 'unpaid') $genbutton='';
            if (empty($modellist) && ! $showempty && $modulepart != 'unpaid') $genbutton='';
            $out.= $genbutton;
            $out.= '</th>';

            if (!empty($hookmanager->hooks['formfile']))
            {
                foreach($hookmanager->hooks['formfile'] as $module)
                {
                    if (method_exists($module, 'formBuilddocLineOptions')) $out .= '<th></th>';
                }
            }
            $out.= '</tr>';

            // Execute hooks
            $parameters=array('socid'=>(isset($GLOBALS['socid'])?$GLOBALS['socid']:''),'id'=>(isset($GLOBALS['id'])?$GLOBALS['id']:''),'modulepart'=>$modulepart);
            if (is_object($hookmanager)) $out.= $hookmanager->executeHooks('formBuilddocOptions',$parameters,$GLOBALS['object']);
        }
        // Get list of files
        if (! empty($filedir))
        {
			$temp = $filedir.'/'.$modulesubdir;	
           // $file_list=dol_dir_list($filedir,'files',0,'','(\.meta|_preview\.png)$','date',SORT_DESC);
            $file_list=dol_dir_list($temp,'files',0,'','(\.meta|_preview\.png)$','date',SORT_DESC);
	
            // Affiche en-tete tableau si non deja affiche
            if (! empty($file_list) && ! $headershown)
            {
                $headershown=1;
                $out.= '<div class="titre">'.$titletoshow.'</div>';
                $out.= '<table class="border" summary="listofdocumentstable" width="100%">';
            }

            // Loop on each file found
			if (is_array($file_list))
			{	
				foreach($file_list as $file)
				{
					$var=!$var;

					// Define relative path for download link (depends on module)
					$relativepath=$file["name"];								// Cas general
					if ($modulesubdir) $relativepath=$modulesubdir."/".$file["name"];	// Cas propal, facture...
					// Autre cas
					if ($modulepart == 'cglinscription')           
					{ 
						$relativepath = $modulesubdir.'/'.$file["name"]; 
					}

					$out.= "<tr ".$bc[$var].">";

					$documenturl = DOL_MAIN_URL_ROOT.'/document.php';
					if (isset($conf->global->DOL_URL_ROOT_DOCUMENT_PHP)) $documenturl=$conf->global->DOL_URL_ROOT_DOCUMENT_PHP;

					// Show file name with link to download
					$out.= '<td class="nowrap">';
					$out.= '<a data-ajax="false" href="'.$documenturl.'?modulepart='.$modulepart.'&amp;file='.urlencode($relativepath).'"';
					$mime=dol_mimetype($relativepath,'',0);
					if (preg_match('/text/',$mime)) $out.= ' target="_blank"';
					$out.= ' target="_blank">';
					$out.= img_mime($file["name"],$langs->trans("File").': '.$file["name"]).' '.dol_trunc($file["name"],$maxfilenamelength);
					$out.= '</a>'."\n";
					
					$out.= '</td>';
	
					// Show file size
					//$size=(! empty($file['size'])?$file['size']:dol_filesize($filedir."/".$file["name"]));
					//$out.= '<td align="right" class="nowrap">'.dol_print_size($size).'</td>';
					$out.= '<td align="right" class="nowrap"></td>';
					// Show file date
					$date=(! empty($file['date'])?$file['date']:dol_filemtime($filedir."/".$file["name"]));
					$out.= '<td align="right" class="nowrap">'.dol_print_date($date, 'dayhour', 'tzuser').'</td>';

					if ($delallowed || $printer || $morepicto)
					{
						$out.= '<td align="right">';
						if ($delallowed)
						{
							$out.= '<a href="'.$urlsource.(strpos($urlsource,'?')?'&':'?').'action=remove_file&file='.urlencode($relativepath);
							$out.= ($param?'&'.$param:'');
							//$out.= '&modulepart='.$modulepart; // TODO obsolete ?
							//$out.= '&urlsource='.urlencode($urlsource); // TODO obsolete ?
							$out.= '">'.img_picto($langs->trans("Delete"), 'delete.png').'</a>';
							//$out.='</td>';
						}
						if ($printer)
						{
							//$out.= '<td align="right">';
    	                    $out.= '&nbsp;<a href="'.$urlsource.(strpos($urlsource,'?')?'&':'?').'action=print_file&amp;printer='.$modulepart.'&amp;file='.urlencode($relativepath);
        	                $out.= ($param?'&'.$param:'');
            	            $out.= '">'.img_picto($langs->trans("Print"),'printer.png').'</a>';
						}
						if ($morepicto)
						{
							$morepicto=preg_replace('/__FILENAMEURLENCODED__/',urlencode($relativepath),$morepicto);
                        	$out.=$morepicto;
						}
                        $out.='</td>';
                    }	

                    if (is_object($hookmanager))
                    {
            			$parameters=array('socid'=>(isset($GLOBALS['socid'])?$GLOBALS['socid']:''),'id'=>(isset($GLOBALS['id'])?$GLOBALS['id']:''),'modulepart'=>$modulepart,'relativepath'=>$relativepath);
                    	$res = $hookmanager->executeHooks('formBuilddocLineOptions',$parameters,$file);
                        if (empty($res))
                        {
                            $out .= $hookmanager->resPrint;		// Complete line
                            $out.= '</tr>';
                        }
                        else $out = $hookmanager->resPrint;		// Replace line
              		}
				}

			 	if (count($file_list) == 0 && $headershown)
	            {
    	        	$out.='<tr><td colspan="3">'.$langs->trans("None").'</td></tr>';
        	    }

                $this->numoffiles++;
            }
        }

        if ($headershown)
        {
            // Affiche pied du tableau
            $out.= "</table>\n";
            if ($genallowed)
            {
                if (empty($noform)) $out.= '</form>'."\n";
            }
        }
        $out.= '<!-- End show_document -->'."\n";
        //return ($i?$i:$headershown);
        return $out;
    } //showdocuments

	/**
	 *	Return an html string with a select combo box to choose yes or no
	 *
	 *	@param	string		$htmlname		Name of html select field
	 *	@param	string		$value			Pre-selected value
	 *	@param	int			$option			0 return yes/no, 1 return 1/0
	 *	@param	bool		$disabled		true or false
	 *  @param	int      	$useempty		1=Add empty line
	 *  @param	int			$addjscombo		1=Add js beautifier on combo box
	 *  @param	string		$morecss		More CSS
	 *  @param	string		$morecss		More events
	 *	@return	string						See option
	 */
	public function selectyesno($htmlname, $value = '', $option = 0, $disabled = false, $useempty = 0, $addjscombo = 0, $morecss = '', $events = '')
	{
		global $langs;

		$yes = "yes";
		$no = "no";
		if ($option) {
			$yes = "1";
			$no = "0";
		}

		$disabled = ($disabled ? ' disabled' : '');

		$resultyesno = '<select class="flat width75'.($morecss ? ' '.$morecss : '').'" id="'.$htmlname.'" name="'.$htmlname.'"'.$disabled.' '.$events.'>'."\n";
		if ($useempty) {
			$resultyesno .= '<option value="-1"'.(($value < 0) ? ' selected' : '').'>&nbsp;</option>'."\n";
		}
		if (("$value" == 'yes') || ($value == 1)) {
			$resultyesno .= '<option value="'.$yes.'" selected>'.$langs->trans("Yes").'</option>'."\n";
			$resultyesno .= '<option value="'.$no.'">'.$langs->trans("No").'</option>'."\n";
		} else {
			$selected = (($useempty && $value != '0' && $value != 'no') ? '' : ' selected');
			$resultyesno .= '<option value="'.$yes.'">'.$langs->trans("Yes").'</option>'."\n";
			$resultyesno .= '<option value="'.$no.'"'.$selected.'>'.$langs->trans("No").'</option>'."\n";
		}
		$resultyesno .= '</select>'."\n";

		if ($addjscombo) {
			$resultyesno .= ajax_combobox($htmlname);
		}

		return $resultyesno;
	}



	/*
	* Transformer les espaces en%20 pour transfert d'url
	*
	*	@param 	$str 	url à coder
	*	@retour			url codée
	*/		
	function urlEncode ($str) {
	 //preg_replace('/[ ]+/', '%20', $str);
	 $strret = '';
	 for ($i=0;  $i<strlen($str) ;$i++) {
		 if ($str[$i] == ' ') $strret .= '%20';
			 else $strret .= $str[$i];
	 }
	return $strret;
	}//urlEncode
	/*
	* Une fonction crée sur modèle de if ($action == 'add') de compta/bank/transfert.php
	*
	*/
	function CreerVirement($dateo, $label, $amount = 0.00, $account_from, $account_to)
	{
		global $langs, $user;
		
		$langs->load("errors");

		$amountto = $amount;

		if (!$label) {
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentities("Description")), null, 'errors');
		}
		if (!$amount) {
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentities("Amount")), null, 'errors');
		}
		if (!$account_from) {
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentities("TransferFrom")), null, 'errors');
		}
		if (!$account_to) {
			$error++;
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentities("TransferTo")), null, 'errors');
		}
		if (!$error) {
			require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
			
			//require_once DOL_DOCUMENT_ROOT."/compta/cglavt/class/cglFctCommune.class.php";

			$accountfrom = new Account($this->db);
			$accountfrom->fetch($account_from);

			$accountto = new Account($this->db);
			$accountto->fetch($account_to);

			if ($amountto < 0) {
				$error++;
				setEventMessages($langs->trans("AmountMustBePositive"), null, 'errors');
			}

			if ($accountto->id == $accountfrom->id) {
				$error++;
				setEventMessages($langs->trans("ErrorFromToAccountsMustDiffers"), null, 'errors');
			}

			if (empty($error)) {
				$this->db->begin();

				$bank_line_id_from = 0;
				$bank_line_id_to = 0;
				$result = 0;

				// By default, electronic transfert from bank to bank
				$typefrom = 'PRE';
				$typeto = 'VIR';
				if ($accountto->courant == Account::TYPE_CASH || $accountfrom->courant == Account::TYPE_CASH) {
					// This is transfer of change
					$typefrom = 'LIQ';
					$typeto = 'LIQ';
				}

				if (!$error) {
					$bank_line_id_from = $accountfrom->addline($dateo, $typefrom, $label, price2num(-1 * $amount), '', '', $user);
				}
				if (!($bank_line_id_from > 0)) {
					$error++;
				}
				if (!$error) {
					$bank_line_id_to = $accountto->addline($dateo, $typeto, $label, $amountto, '', '', $user);
				}
				if (!($bank_line_id_to > 0)) {
					$error++;
				}

				if (!$error) {
					$result = $accountfrom->add_url_line($bank_line_id_from, $bank_line_id_to, DOL_URL_ROOT.'/compta/bank/line.php?rowid=', '(banktransfert)', 'banktransfert');
				}
				if (!($result > 0)) {
					$error++;
				}
				if (!$error) {
					$result = $accountto->add_url_line($bank_line_id_to, $bank_line_id_from, DOL_URL_ROOT.'/compta/bank/line.php?rowid=', '(banktransfert)', 'banktransfert');
				}
				if (!($result > 0)) {
					$error++;
				}


				if (!$error) {
					$mesgs = $langs->trans("TransferFromToDone", '{s1}', '{s2}', $amount, $langs->transnoentitiesnoconv("Currency".$conf->currency));
					$mesgs = str_replace('{s1}', '<a href="bankentries_list.php?id='.$accountfrom->id.'&sortfield=b.datev,b.dateo,b.rowid&sortorder=desc">'.$accountfrom->label.'</a>', $mesgs);
					$mesgs = str_replace('{s2}', '<a href="bankentries_list.php?id='.$accountto->id.'">'.$accountto->label.'</a>', $mesgs);
//					setEventMessages($mesgs, null, 'mesgs');
					$this->db->commit();
				} else {
						$this->error = $accountfrom->error.' '.$accountto->error;
//					setEventMessages($accountfrom->error.' '.$accountto->error, null, 'errors');
					$this->db->rollback();
				}

			return (int)(-1) * 	$error		;
			}
		}
		

	} //CreerVirement



	/*
	* fonction reprise de compta/bank/payment_various/card - action = add - Dolibarr V15
	*
	*
	*/
	function creerFrais  ($datep, $datev, $accountid, $amount, $label, $paymenttype, $accountancy_code)
	{
		global $langs, $conf, $user, $db;

		$error = 0;

		require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/paymentvarious.class.php';
		$object = new PaymentVarious($db);

		$object->ref = ''; // TODO
		$object->accountid =$accountid;
		$object->datev = $datev;
		$object->datep = $datep;
		$object->amount = price2num($amount);
		$object->label =$label;
		//	$object->note = GETPOST("note", 'restricthtml');
		$object->type_payment = dol_getIdFromCode($db, $paymenttype, 'c_paiement', 'code', 'id', 1);
		$object->fk_user_author = $user->id;
		//	$object->category_transaction = GETPOST("category_transaction", 'alpha');

		$object->accountancy_code = $accountancy_code;

		$object->sens = 0; //  pour débit, 1 pour credit

		if (empty($datep) || empty($datev)) {
			$langs->load('errors');
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Date")), null, 'errors');
			$error++;
		}
		if (empty($object->amount)) {
			$langs->load('errors');
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Amount")), null, 'errors');
			$error++;
		}
		if (!empty($conf->banque->enabled) && !$object->accountid > 0) {
			$langs->load('errors');
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("BankAccount")), null, 'errors');
			$error++;
		}
		if (empty($object->type_payment) || $object->type_payment < 0) {
			$langs->load('errors');
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("PaymentMode")), null, 'errors');
			$error++;
		}
		if (!empty($conf->accounting->enabled) && !$object->accountancy_code) {
			$langs->load('errors');
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("AccountAccounting")), null, 'errors');
			$error++;
		}
		if ($object->sens < 0) {
			$langs->load('errors');
			setEventMessages($langs->trans("ErrorFieldRequired", $langs->transnoentitiesnoconv("Sens")), null, 'errors');
			$error++;
		}

		if (!$error) {
			$db->begin();

			$ret = $object->create($user);
			if ($ret > 0) {
				$db->commit();
				//$urltogo = ($backtopage ? $backtopage : DOL_URL_ROOT.'/compta/bank/various_payment/list.php');
				//header("Location: ".$urltogo);
				//exit;
			} else {
				$db->rollback();
				setEventMessages($object->error, $object->errors, 'errors');
				$action = "create";
			}
		}

		$action = 'create';
	} //creerFrais

	 
	/**
	 *	Show header of a report
	 *
	 *	@param	string				$reportname     Name of report
	 *	@param 	string				$notused        Not used
	 *	@param 	string				$period         Period of report
	 *	@param 	string				$periodlink     Link to switch period
	 *	@param 	string				$description    Description
	 *	@param 	integer	            $builddate      Date generation
	 *	@param 	string				$exportlink     Link for export or ''
	 *	@param	array				$moreparam		Array with list of params to add into form
	 *	@param	string				$calcmode		Calculation mode
	 *  @param  string              $varlink        Add a variable into the address of the page
	 *  @param  string              $flform         1 => L'entête est un formulaire standard Dolibarr- 
									0=> l'entête standard Do	libarr a étté enrichie et fait partie d'un formulaire plus ample
	 
	 *	@return	void
	 */ 
	function report_header($reportname, $notused, $period, $periodlink, $description, $builddate, $exportlink = '', $moreparam = array(), $calcmode = '', $varlink = '', $flform = 1, $TypeRapport)
	{
		global $langs;
		$FormRapport = new FormRapportSecteur();

			print "\n\n<!-- start banner of report -->\n";

			if (!empty($varlink)) {
				$varlink = '?'.$varlink;
			}

			$head = array();

			$h = 0;
			$head[$h][0] = $_SERVER["PHP_SELF"].$varlink;
			$head[$h][1] = $langs->trans("Report");
			$head[$h][2] = 'report';

			print '<form method="GET" action="'.$_SERVER["PHP_SELF"].$varlink.'">'."\n";
			print '<input type="hidden" name="token" value="'.newToken().'">'."\n";

			print dol_get_fiche_head($head, 'report');

			foreach ($moreparam as $key => $value) {
				 print '<input type="hidden" name="'.$key.'" value="'.$value.'">'."\n";
			}

			if (!$flform) {		
				print '<!-- début container Entete -->';
				//print '<div style="position:static;">'; 
				print '<table id=Entete><tbody><tr>'; 
				//print '<form method="get" action="#">';	
				print '<!-- début Div de gauche -->';
				print '<td width=40%>'; 
			}

			// Report_headr de Dolibarr

			print '<table class="border tableforfield centpercent" >'."\n";

			$variante = ($periodlink || $exportlink);

			// Ligne de titre
			print '<tr>';
			print '<td width="150">'.$langs->trans("ReportName").'</td>';
			print '<td>';
			print $reportname;
			print '</td>';
			if ($variante) {
				print '<td></td>';
			}
			print '</tr>'."\n";

			// Calculation mode
			if ($calcmode) {
				print '<tr>';
				print '<td width="150">'.$langs->trans("CalculationMode").'</td>';
				print '<td>';
				print $calcmode;
				if ($variante) {
					print '<td></td>';
				}
				print '</td>';
				print '</tr>'."\n";
			}

			// Ligne de la periode d'analyse du rapport
			print '<tr>';
			print '<td>'.$langs->trans("ReportPeriod").'</td>';
			print '<td >';
			if ($period) {
				print $period;
			}
			if ($variante) {
				if ($flform) print '<td class="nowraponall">'.$periodlink.'</td>';
				else print '&nbsp&nbsp&nbsp&nbsp'.$periodlink;
			}
			print '</td>';
			print '</tr>'."\n";

			// Ligne de description
			print '<tr>';
			print '<td>'.$langs->trans("ReportDescription").'</td>';
			print '<td>'.$description.'</td>';
			if ($variante) {
				print '<td></td>';
			}
			print '</tr>'."\n";

			// Ligne d'export
			print '<tr>';
			print '<td>'.$langs->trans("GeneratedOn").'</td>';
			print '<td>';
			print dol_print_date($builddate, 'dayhour');
			print '</td>';
			if ($variante) {
				print '<td>'.($exportlink ? $langs->trans("Export").': '.$exportlink : '').'</td>';
			}
			print '</tr>'."\n";
			print '</table>'."\n";

			print dol_get_fiche_end();
			if ($flform)  print '<div class="center"><input type="submit" class="button" name="submit" value="'.$langs->trans("Refresh").'"></div>';

			if ($flform) print '</form>';
			print '<br>';

			print "\n<!-- end banner of report -->\n\n";

		//	fin report_header de Dolibarr

			if (!$flform)
			{
				
				print '<!-- fin Div de gauche -->';
				print '</td>'; 

				$head = array();
				$h = 0;
				$head[$h][0] = "&nbsp";
				$head[$h][1] = "&nbsp";
				$head[$h][2] = '&nbsp';
				
				// Choix type de rapport
			print '<!-- début Div de mileu -->';
			print '<td width=20% align=center>';
			//print dol_get_fiche_head($head, 'report');
			print '<br><br><br><br><br>';
			print $FormRapport->html_selectTypeRapport($TypeRapport);
			print '<br><br><br><br><br>';
			if (!$flform)  print '<div class="center"><input type="submit" class="button" name="submit" value="'.$langs->trans("Refresh").'"></div>';

			print '<!-- fin Div de mileu -->';
				print '</td>';  
				
				
			print '<!-- début Div de droite -->';
			print '<td width=40%>';
			print '<table id=secteur><tbody><tr><tr>';
			//print dol_get_fiche_head($head, 'report');
			print '</td></tr>';
			print '<!-- début Div contenant Secteurs -->';
			print '<tr><td>';
				// Ligne de titre
			global $tabSectSels ; 
				print $FormRapport->html_ListSelectionnes($tabSectSels);

			print '</td></tr>';
			print '<tr height=3px><td>';
			print '</td></tr>';
			print '<tr><td>';
				
				
			//Choix des secteurs
			global $tabSects, $secteurs;
			print $FormRapport->html_selectSecteur($tabSects, $secteurs);
			print '<br><br>';
			print '<input type="submit" class="button"  name="BtNiv2" class="button"  value="'.$langs->trans("Selectionner").'" >';
			

				
			print '<!-- Fin Div contenant Secteurs -->';
			print '</td></tr></tbody></table>';

			print '<!-- Fin Div   de droite -->';
				print '</td></tr>'."\n";


			print '<!-- Fin Div container Entete -->';
				print '</tbody></table>'."\n";
			
			print '</form>';

				print '</div>'; 
				
			}
		} // Report_header
} // fin de classe CglFonctionDolibarr
?>