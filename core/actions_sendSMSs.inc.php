<?php
/* Copyright (C) 2013 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
* Modif CCA 12/1/2020 - Création
*
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
* or see http://www.gnu.org/
*/

/**
 *	\file			htdocs/core/actions_sendSMSs.inc.php
 *  \brief			Code for actions on sending SMSs from object page
 */

// $mysoc must be defined
// $id must be defined
// $paramname may be defined
// $autocopy may be defined (used to know the automatic BCC to add)
// $trigger_name must be set (can be '')
// $actiontypecode can be set
// $object and $uobject may be defined

/*
 * Add file in SMS form
 */
if (GETPOST('addfile','alpha'))
{
	$trackid = GETPOST('trackid','aZ09');

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

	// Set tmp user directory
	$vardir=$conf->user->dir_output."/".$user->id;
	$upload_dir_tmp = $vardir.'/temp';             // TODO Add $keytoavoidconflict in upload_dir path

	dol_add_file_process($upload_dir_tmp, 0, 0, 'addedfile', '', null, $trackid, 0);
	$action='presend';
}

/*
 * Remove file in SMS form
 */
if (! empty($_POST['removedfile']) && empty($_POST['removAll']))
{

	$trackid = GETPOST('trackid','aZ09');

	require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';

	// Set tmp user directory
	$vardir=$conf->user->dir_output."/".$user->id;
	$upload_dir_tmp = $vardir.'/temp';             // TODO Add $keytoavoidconflict in upload_dir path

	// TODO Delete only files that was uploaded from SMS form. This can be addressed by adding the trackid into the temp path then changing donotdeletefile to 2 instead of 1 to say "delete only if into temp dir"
	// GETPOST('removedfile','alpha') is position of file into $_SESSION["listofpaths"...] array.
	dol_remove_file_process(GETPOST('removedfile','alpha'), 0, 1, $trackid);   // We do not delete because if file is the official PDF of doc, we don't want to remove it physically
	$action='presend';
}

/*
 * Remove all files in SMS form
 */
if (GETPOST('removAll','alpha'))
{
	$trackid = GETPOST('trackid','aZ09');

	$listofpaths=array();
	$listofnames=array();
	$listofmimes=array();
	$keytoavoidconflict = empty($trackid)?'':'-'.$trackid;
	if (! empty($_SESSION["listofpaths".$keytoavoidconflict])) $listofpaths=explode(';',$_SESSION["listofpaths".$keytoavoidconflict]);
	if (! empty($_SESSION["listofnames".$keytoavoidconflict])) $listofnames=explode(';',$_SESSION["listofnames".$keytoavoidconflict]);
	if (! empty($_SESSION["listofmimes".$keytoavoidconflict])) $listofmimes=explode(';',$_SESSION["listofmimes".$keytoavoidconflict]);

	include_once DOL_DOCUMENT_ROOT.'/core/class/html.formSMS.class.php';
	$formSMS = new FormSMS($db);
	$formSMS->trackid = $trackid;

	foreach($listofpaths as $key => $value)
	{
		$pathtodelete = $value;
		$filetodelete = $listofnames[$key];
		$result = dol_delete_file($pathtodelete,1); // Delete uploded Files

		$langs->load("other");
		setEventMessages($langs->trans("FileWasRemoved",$filetodelete), null, 'mesgs');

		$formSMS->remove_attached_files($key); // Update Session
	}
}

/*
 * Send SMS 
 */
if (($action == 'send' || $action == 'relance') && ! $_POST['SMSaddfile'] && ! $_POST['removAll'] && ! $_POST['removedfile'] && ! $_POST['cancel'] && !$_POST['modelselected'])
{
	$error=0;
print '<br>CCA ==================================$_POST["fromsms"]:'.$_POST["fromsms"];
print '<br>CCA ==================================$smsfrom:'.$smsfrom;
print '<br>CCA ==================================$sendto:'.GETPOST("sendto");
print '<br>CCA ==================================$receiver:'.GETPOST("receiver");
print '<br>CCA ==================================$deliveryreceipt:'.GETPOST("deliveryreceipt");
print '<br>CCA ==================================$deferred:'.GETPOST("deferred");
print '<br>CCA ==================================$priority:'.GETPOST("priority");
print '<br>CCA ==================================$class:'.GETPOST("class");
print '<br>CCA ==================================$errorstosms:'.GETPOST("errorstosms");
print '<br>CCA ==================================$socid:'.GETPOST("socid");
   $smsfrom='';
    if (! empty($_POST["fromsms"])) $smsfrom=GETPOST("fromsms");
    if (empty($smsfrom)) $smsfrom=GETPOST("fromname");
    $sendto     = GETPOST("sendto");
    $receiver   = GETPOST('receiver');
    $body       = GETPOST('message');
    $deliveryreceipt= GETPOST("deliveryreceipt");
    $deferred   = GETPOST('deferred');
    $priority   = GETPOST('priority');
    $class      = GETPOST('class');
    $errors_to  = GETPOST("errorstosms");

print '<br>CCA ==================================POu un envoi SMS quelconque';
    $thirdparty=new Societe($db);
    $thirdparty->fetch($socid);

    if ($receiver == 'thirdparty') $sendto=$thirdparty->phone;
    if ((empty($sendto) || ! str_replace('+','',$sendto)) && (! empty($receiver) && $receiver != '-1'))
    {
        $sendto=$thirdparty->contact_get_property($receiver,'mobile');
    }

    // Test param
    if (empty($body))
    {
        setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentities("Message")),'','errors');
        $action='test';
        $error++;
    }
    if (empty($smsfrom) || ! str_replace('+','',$smsfrom))
    {
        setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentities("SmsFrom")),'','errors');
        $action='test';
        $error++;
    }
    if ((empty($sendto) || ! str_replace('+','',$sendto)) && (empty($receiver) || $receiver == '-1'))
    {
        setEventMessages($langs->trans("ErrorFieldRequired",$langs->transnoentities("SmsTo")),'','errors');
        $action='test';
        $error++;
    }

    if (! $error)
    {
        // Make substitutions into message
        $substitutionarrayfortest=array();
        complete_substitutions_array($substitutionarrayfortest,$langs);
        $body=make_substitutions($body,$substitutionarrayfortest);

print '<br>CCA ==================================$message:'.GETPOST("body");
        require_once(DOL_DOCUMENT_ROOT."/core/class/CSMSFile.class.php");

        $smsfile = new CSMSFile($sendto, $smsfrom, $body, $deliveryreceipt, $deferred, $priority, $class);  // This define OvhSms->login, pass, session and account

        $smsfile->nostop=GETPOST('disablestop');

        $result=$smsfile->sendfile(); // This send SMS

        if ($result > 0)
        {
            setEventMessages($langs->trans("SmsSuccessfulySent",$smsfrom,$sendto), null);
        }
        else
        {
            setEventMessages($langs->trans("ResultKo").' (sms from'.$smsfrom.' to '.$sendto.')<br>'.$smsfile->error, null, 'errors');
        }

        $action='';
    }

}