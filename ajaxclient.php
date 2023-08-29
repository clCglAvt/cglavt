<?php
/* Copyright (C) 2012 Regis Houssin  <regis.houssin@capnetworks.com>
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
 *       \file       htdocs/core/ajax/contacts.php
 *       \brief      File to load contacts combobox
 */

if (! defined('NOTOKENRENEWAL')) define('NOTOKENRENEWAL','1'); // Disables token renewal
if (! defined('NOREQUIREMENU'))  define('NOREQUIREMENU','1');
//if (! defined('NOREQUIREHTML'))  define('NOREQUIREHTML','1');
if (! defined('NOREQUIREAJAX'))  define('NOREQUIREAJAX','1');
//if (! defined('NOREQUIRESOC'))   define('NOREQUIRESOC','1');
//if (! defined('NOREQUIRETRAN'))  define('NOREQUIRETRAN','1');

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/CahierSuivi/class/html.suivi_client.class.php';

$id			= GETPOST('id','int');

/*
 * View
 */
//dol_syslog('ajaxclient - id:'.$id);	
top_httphead();

//print '<!-- Ajax page called with url '.$_SERVER["PHP_SELF"].'?'.$_SERVER["QUERY_STRING"].' -->'."\n";

// Load original field value
if (! empty($id) )
{	
	$return=array();

    $langs->load('companies');

        $out='';
		
		$sql = "SELECT st.rowid,  st.phone as TiersTel, st.email as TiersMail, stex.s_tel2 as TiersSupTel,  stex.s_email2 as TiersSupMail, p.code as country_code ";
        $sql.= " FROM ".MAIN_DB_PREFIX ."societe as st";
        $sql.= " LEFT JOIN ".MAIN_DB_PREFIX ."societe_extrafields as stex on fk_object = st.rowid ";
        $sql .= ' LEFT JOIN '.MAIN_DB_PREFIX.'c_country as p ON st.fk_pays = p.rowid';
        $sql.= " WHERE st.rowid ='".$id."'";
		$w= new FormCglSuivi($db);
//dol_syslog('ajaxclient - sql:'.$sql);		
        $resql=$db->query($sql);
        if ($resql)
        {
            $num = $db->num_rows($resql);
            $i = 0;
            if ($num)            {
			   $objc = $db->fetch_object($resql);
               $return['telmail'] = $w->ChercheTelMailTiersContact($objc->TiersTel, $objc->TiersSupTel, $objc->TiersMail, $objc->TiersSupMail, $objc->rowid,  $objc->country_code);	
            }			
        }
        else
        {
            //dol_print_error($db);
            $error =  $db->error;
			$num = -1;
        }	
	$return['tel']	= $objc->TiersTel;
	$return['telfmt']	= $objc->TiersTel;
	$return['mail']	= $objc->TiersMail;
	$return['suptel']	= $objc->TiersSupTel;
	$return['suptelfmt']	= $objc->TiersSupTel;
	$return['supmail']	= $objc->TiersSupMail;
	$return['num']		= $num;
	$return['error']	= $error;
//dol_syslog('ajaxclient - retour encode:'. json_encode($return));	
	echo json_encode($return);
}
else
	echo json_encode(array('id inconnu'));
