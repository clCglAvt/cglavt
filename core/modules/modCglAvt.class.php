<?php
/* Copyright (C) 2003      Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2012 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * CCA 2014 <claude@cigaleaventure.com>
 *
 * Version CAV - 2.7 - juin 2022
 *					 - Migration Dolibarr V15
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
 *  \defgroup   cglinscription Module Inscription pour boutique d'activités sportives
 *  \brief      Descripteur de moduledescriptor.
 *  \file       htdocs/cglinscription/core/modules/modcglinscription.class.php
 *  \ingroup    cglinscription
 *  \brief      Fichier de Description and d'activation file pour le module d'inscriptions activités sprotives en boutique
 *              version 1.0 mars 2014
 *              version 1.1 dec 2014 - adaptation version Dolibarr 3.6 
 *              version 2 dec 2018 - adaptation version Dolibarr 8.0.3 
 *				version 2.6 avril 2021 - migration vers Dolibarr 12.0.5
 *				version 2.6.1 mars 2022 - migration vers Dolibarr 12.0.5
 *				version 2.9 - Migration V17
 
 */
include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';


/**
 *  Description and activation class for module MyModule
 */
class ModCglAvt extends DolibarrModules
{
	/**
	 *   Constructor. Define names, constants, directories, boxes, permissions
	 *
	 *   @param      DoliDB		$db      Database handler
	 */
	function __construct($db)
	{
        global $langs,$conf;
		
        $this->db = $db;

		// Id for module (must be unique).
		// Use here a free id (See in Home -> System information -> Dolibarr for list of used modules id).
	$this->numero = 875130;
		// Key text used to identify module (for permissions, menus, etc...)
	$this->rights_class = 'CglAvt';

		// Family can be 'crm','financial','hr','projects','products','ecm','technic','other'
		// It is used to group modules in module setup page
	$this->family = "crm";
		// Module label (no space allowed), used if translation string 'ModuleXXXName' not found (where XXX is value of numeric property 'numero' of module)
	$this->name = preg_replace('/^mod/i','',get_class($this));
		// Module description, used if translation string 'ModuleXXXDesc' not found (where XXX is value of numeric property 'numero' of module)
	$this->description = "Personnalisation de Dolibarr pour CigaleAventure";
		// Possible values for version are: 'development', 'experimental', 'dolibarr' or version
	$this->version = '2.9';
		// Key used in llx_const table to save module status enabled/disabled (where MYMODULE is value of property name of module in uppercase)
	$this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
		// Where to store the module in setup page (0=common,1=interface,2=others,3=very specific)
	$this->special = 2;
		// Name of image file used for this module.
		// If file is in theme/yourtheme/img directory under name object_pictovalue.png, use this->picto='pictovalue'
		// If file is in module/img directory under name object_pictovalue.png, use this->picto='pictovalue@module'

		// Defined all module parts (triggers, login, substitutions, menus, css, etc...)
		// for default path (eg: /mymodule/core/xxxxx) (0=disable, 1=enable)
		// for specific path of parts (eg: /mymodule/core/modules/barcode)
		// for specific css file (eg: /mymodule/css/mymodule.css.php)
		//$this->module_parts = array(
		//                        	'triggers' => 0,                           // Set this to 1 if module has its own trigger directory (core/triggers)
		//					'login' => 0,                              // Set this to 1 if module has its own login method directory (core/login)
		//					'substitutions' => 0,                      // Set this to 1 if module has its own substitution function file (core/substitutions)
		//					'menus' => 0,                              // Set this to 1 if module has its own menus handler directory (core/menus)
		//					'theme' => 0,                              // Set this to 1 if module has its own theme directory (core/theme)
		//                        	'tpl' => 0,                                // Set this to 1 if module overwrite template dir (core/tpl)
		//					'barcode' => 0,                            // Set this to 1 if module has its own barcode directory (core/modules/barcode)
		//					'models' => 0,                             // Set this to 1 if module has its own models directory (core/modules/xxx)
		//					'css' => array('/mymodule/css/mymodule.css.php'),	// Set this to relative path of css file if module has its own css file
	 	//					'js' => array('/mymodule/js/mymodule.js'),   // Set this to relative path of js file if module must load a js on all pages
		//					'hooks' => array('hookcontext1','hookcontext2')  // Set here all hooks context managed by module
		//					'dir' => array('output' => 'othermodulename'),   // To force the default directories names
		//					'workflow' => array('WORKFLOW_MODULE1_YOURACTIONTYPE_MODULE2'=>array('enabled'=>'! empty($conf->module1->enabled) && ! empty($conf->module2->enabled)', 'picto'=>'yourpicto@mymodule')) // Set here all workflow context managed by module
		//                        );
	$this->module_parts = array('models' => 1,
						'hooks' => array('thirdpartycard','globalcard', 'emailtemplates', 'main') 
						);

		// Data directories to create when module is enabled.
		// Example: this->dirs = array("/mymodule/temp");
	$this->dirs = array('/cglavt');

		// Config pages. Put here list of php page, stored into mymodule/admin directory, to use to setup module.
		$this->config_page_url = array();
		// Dependencies
	$this->depends = array('modSociete','modCommande','modFacture', 'modBanque', 'modFournisseur', 'modService','modAgenda', 'modAgefodd');		// List of modules id that must be enabled if this module is enabled
	//$this->requiredby = array('modCashDesk');		// List of modules id to disable if this one is disabled
	$this->phpmin = array(5,0);		// Minimum version of PHP required by module
	$this->need_dolibarr_version = array(3,4);	// Minimum version of Dolibarr required by module
	$this->langfiles = array("cglavt@CglAvt");

		// Constantes
		// List of particular constants to add when module is enabled (key, 'chaine', value, desc, visible, 'current' or 'allentities', deleteonunactive)
		// Example: $this->const=array(0=>array('MYMODULE_MYNEWCONST1','chaine','myvalue','This is a constant to add',1),
		//                             1=>array('MYMODULE_MYNEWCONST2','chaine','myvalue','This is another constant to add',0, 'current', 1)
		// );
		$this->const = array();

		// Constants
		$r = 0;
		
		// Array de nouvelles pages dans des onglets existants
		// Example: $this->tabs = array('objecttype:+tabname1:Title1:mylangfile@mymodule:$user->rights->mymodule->read:/mymodule/mynewtab1.php?id=__ID__',  	// To add a new tab identified by code tabname1
        //                              'objecttype:+tabname2:Title2:mylangfile@mymodule:$user->rights->othermodule->read:/mymodule/mynewtab2.php?id=__ID__',  	// To add another new tab identified by code tabname2
        //                              'objecttype:-tabname':NU:conditiontoremove);                                                     						// To remove an existing tab identified by code tabname
		// where objecttype can be
		// 'thirdparty'       to add a tab in third party view
		// 'intervention'     to add a tab in intervention view
		// 'order_supplier'   to add a tab in supplier order view
		// 'invoice_supplier' to add a tab in supplier invoice view
		// 'invoice'          to add a tab in customer invoice view
		// 'order'            to add a tab in customer order view
		// 'product'          to add a tab in product view
		// 'stock'            to add a tab in stock view
		// 'propal'           to add a tab in propal view
		// 'member'           to add a tab in fundation member view
		// 'contract'         to add a tab in contract view
		// 'user'             to add a tab in user view
		// 'group'            to add a tab in group view
		// 'contact'          to add a tab in contact view
		// 'categories_x'	  to add a tab in category view (replace 'x' by type of category (0=product, 1=supplier, 2=customer, 3=member)
 		$this->tabs = array ();
        // Dictionaries
		
	    if (! isset($conf->cglavt->enabled))
        {
        	$conf->cglavt=new stdClass();
        	$conf->cglavt->enabled=0;
        }
		$this->dictionaries=array();
 		$this->dictionaries=array(
            'langs' => 'cglinscription@cglinscription',
            'tabname'=>array(
					MAIN_DB_PREFIX."c_paiementCAV",
					),
		// List of tables we want to see into dictonnary editor
            'tablib'=>array(
					"DictionaryPaymentModes",
					),				// Label of tables
            'tabsql'=>array(
					'SELECT c.id    as rowid, c.code, c.libelle, c.type, c.active, c.accountancy_code, c.fl_regroup , c.fl_regroupneg, c.fk_cpt_bq  FROM '.MAIN_DB_PREFIX.'c_paiement AS c'
					),	// Request to select fields
            'tabsqlsort'=>array(
					"code ASC",
					),			// Sort order
            'tabfield'=>array( 
					"code,libelle,type,accountancy_code,fl_regroup,fl_regroupneg,fk_cpt_bq"					
					),		// List of fields (result of select to show dictionnary)
            'tabfieldvalue'=>array(
					"code,libelle,type,accountancy_code,fl_regroup,fl_regroupneg,fk_cpt_bq"	
					),	// List of fields (list of fields to edit a record)
            'tabfieldinsert'=>array(
					"code,libelle,type,accountancy_code,fl_regroup,fl_regroupneg,fk_cpt_bq"	
					), // List of fields (list of fields for insert)
            'tabrowid'=>array(
					"id"
					),	// Name of columns with primary key (try to always name it 'rowid')
            'tabcond'=>array(
					(!empty($conf->commande->enabled) || !empty($conf->propal->enabled) || isModEnabled('facture') || (!empty($conf->fournisseur->enabled) && empty($conf->global->MAIN_USE_NEW_SUPPLIERMOD)) || !empty($conf->supplier_invoice->enabled) || !empty($conf->supplier_order->enabled))
					),	// Condition to show each dictionnary
			'tabhelp' => array (
					array('code'=>$langs->trans("EnterAnyCode"),
					'fl_regroup'=>$langs->trans("FlRegroup"),
					'fl_regroupneg'=>$langs->trans("FlRegroupNeg"),
					'fk_cpt_bq'=>$langs->trans("Fk_cpt_bq")
					))
        );

        // Boxes
		// Add here list of php file(s) stored in core/boxes that contains class to show a box.
        $this->boxes = array();			// List of boxes
		// Example:
		//$this->boxes=array(array(0=>array('file'=>'myboxa.php','note'=>'','enabledbydefaulton'=>'Home'),1=>array('file'=>'myboxb.php','note'=>''),2=>array('file'=>'myboxc.php','note'=>'')););

		// Permissions
		$this->rights = array();		// Permission array used by this module
		$r=0;

		// Add here list of permission defined by an id, a label, a boolean and two constant strings.

		// Main menu entries
		$this->menu = array();			// List of menus to add
		$r=0;		
	} // construct

	/**
	 *		Function called when module is enabled.
	 *		The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
	 *		It also creates data directories
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function init($options='')
	{
		$sql = array();

		//$result=$this->load_tables();

		return $this->_init($sql, $options);
		
			
	}

	/**
	 *		Function called when module is disabled.
	 *      Remove from database constants, boxes and permissions from Dolibarr database.
	 *		Data directories are not deleted
	 *
     *      @param      string	$options    Options when enabling module ('', 'noboxes')
	 *      @return     int             	1 if OK, 0 if KO
	 */
	function remove($options='')
	{
		$sql = array();

		return $this->_remove($sql, $options);
	}


	/**
	 *		Create tables, keys and data required by module
	 * 		Files llx_table1.sql, llx_table1.key.sql llx_data.sql with create table, create keys
	 * 		and create data commands must be stored in directory /mymodule/sql/
	 *		This function is called by this->init
	 *
	 * 		@return		int		<=0 if KO, >0 if OK
	 */
	function load_tables()
	{
		//return $this->_load_tables('/cglavt/sql/');
	}
}

?>