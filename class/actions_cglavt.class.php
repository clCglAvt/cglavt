<?php
/* Copyright (C) 2013 Jean-François FERRY  <jfefe@aternatik.fr>
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
 *       \file       cglavt/class/actions_cglavt.class.php
 *       \brief      Place module actions - HOOK pour Couleur
 */

/**
 * Actions class file for resources
 *
 * TODO Remove this class and replace a method into commonobject
 */
class ActionsCglAvt
{

	var $db;
	var $error;
	var $errors=array();

	/**
	 *	Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	*/
	function __construct($db)
	{
		$this->db = $db;
	}

	/**
	 * doActions for resource module
	 *
	 * @param 	array 	$parameters 	parameters
	 * @param 	Object 	$object 		object
	 * @param 	string 	$action 		action
	 * @return	void
	 */
	function editDictionaryFieldlist($parameters, &$obj, &$action)
	{
	}//editDictionaryFieldlist

	/**
	 * doActions for resource module
	 *
	 * @param 	array 	$parameters 	parameters
	 * @param 	Object 	$object 		object
	 * @param 	string 	$action 		action
	 * @return	void
	 */
	function createDictionaryFieldlist($parameters, &$obj, &$action)
	{

	}//createDictionaryFieldlist

	/**
	 *	Show field
	 *
	 * 	@param		array	$fieldlist		Array of fields
	 * 	@param		Object	$obj			If we show a particular record, obj is filled with record fields
	 *  @param		string	$tabname		Name of SQL table
	 *	@return		void
	 */
	function fieldList($fieldlist,$obj='',$tabname='')
	{
	/*	global $conf,$langs,$db;
		global $form;
		global $region_id;
	//	global $sourceList,$elementList;
		global $localtax_typeList;

		$formadmin = new FormAdmin($db);
		$formcompany = new FormCompany($db);

		foreach ($fieldlist as $field => $value)
		{
			if ($fieldlist[$field] == 'nomservice') {
				print '<td>';
				print $form->select_produits_list((! empty($obj->country_code)?$obj->refservice:(! empty($obj->nomservice)?$obj->nomservice:'')),'nomservice');
				print '</td>';
			}
			elseif ($fieldlist[$field] == 'recuperableonly' || $fieldlist[$field] == 'fdm' || $fieldlist[$field] == 'deductible') {
				print '<td>';
				print $form->selectyesno($fieldlist[$field],(! empty($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:''),1);
				print '</td>';
			}
			elseif ($fieldlist[$field] == 'code' && isset($obj->$fieldlist[$field])) {
				print '<td><input type="text" class="flat" value="'.(! empty($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:'').'" size="10" name="'.$fieldlist[$field].'"></td>';
			}
			elseif ($fieldlist[$field] == 'ordre' && isset($obj->$fieldlist[$field]))
			{
				print '<td><input type="text" class="flat" value="'.(! empty($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:'').'" size="10" name="'.$fieldlist[$field].'"></td>';
			}
			
			elseif ($fieldlist[$field] == 'couleur' )
			{
				print '<td nowrap="nowrap">';
				print select_color(! empty($obj->couleur)?$obj->couleur:'', 'couleur',$tabname);
				print '</td>';
			}
	 
	 
			else
			{
				print '<td>';
				$size='10';
				print '<input type="text" '.$size.' class="flat" value="'.(isset($obj->$fieldlist[$field])?$obj->$fieldlist[$field]:'').'" name="'.$fieldlist[$field].'">';
				print '</td>';
			}
		}
		*/
	}
	/*
	 * Fusion de Tiers pour CglAvt
	 *
	 * @param	array	$parameters		Parameters
	 * @param	Object	$object			Object
	 * @param	string	$action			Action
	*/
	function replaceThirdparty($parameters, &$object, &$action) 
	{		
		$tables = array();
		$tables = array(
			'cglinscription_bull',
			'cglavt_dossier',
			'cglavt_dossierdet'
		);
		$ret =  CommonObject::commonReplaceThirdparty($this->db, $parameters['soc_origin'], $parameters['soc_dest'], $tables);
		if ($ret) $action = null;
		if ($ret ) return 1;
		else return -1;
		//return $ret;		
	} //replaceThirdparty
	
	function emailElementlist($parameters, &$object, &$action) 
	{
		global $langs;
		$langs->load('cglavt@CglAvt');
		$this->results ['cgllocation'] = $langs->trans('TiSendLocation');
		$this->results ['cglbulletin'] = $langs->trans('TiSendInscription');
		$this->results ['cglresa'] = $langs->trans('TiSendReservation');
		$this->results ['cglStripe'] = $langs->trans('TiSendStripe');		
		return 0;
	}
		
	function smsElementlist($parameters, &$object, &$action) 
	{
		global $langs;
		$langs->load('cglavt@CglAvt');
		
		$this->results ['cgllocationSMS'] = $langs->trans('TiSendSMSLocation');
		$this->results ['cglbulletinSMS'] = $langs->trans('TiSendSMSInscription');
		$this->results ['cglresaSMS'] = $langs->trans('TiSendSMSReservation');		
		$this->results ['cglStripeSMS'] = $langs->trans('TiSendSMSStripe');	
		
		return 0;
	}
	function emailtemplates($parameters, &$object, &$action) 
	{
		global $langs;
		$langs->load('cglavt@CglAvt');
	}

	}// ActionsCglAvt
?>