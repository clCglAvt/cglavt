<?php
/* Copyright (C) 2008-2011 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * MODIF CCA 17/7/2016 - Pb de variable  $this
 * MODIF CCA 17/5/2021 - Montée Version Dolibarr V12 - suppression punctual
 * Modif CCA 25/5/21 - Montée version Dolibarr V12 - suppression add
 */
/**
 *	    \file       htdocs/google/admin/google.php
 *      \ingroup    google
 *      \brief      Setup page for google module (Calendar)
 */

define('NOCSRFCHECK',1);

$res=0;
if (! $res && file_exists("../main.inc.php")) $res=@include("../main.inc.php");
if (! $res && file_exists("../../main.inc.php")) $res=@include("../../main.inc.php");
if (! $res && file_exists("../../../main.inc.php")) $res=@include("../../../main.inc.php");
if (! $res && @file_exists("../../../../main.inc.php")) $res=@include("../../../../main.inc.php");
if (! $res && preg_match('/\/(?:custom|nltechno)([^\/]*)\//',$_SERVER["PHP_SELF"],$reg)) $res=@include("../../../../dolibarr".$reg[1]."/htdocs/main.inc.php"); // Used on dev env only
if (! $res) die("Include of main fails");
require_once(DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/date.lib.php");
require_once(DOL_DOCUMENT_ROOT."/core/lib/files.lib.php");
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php');
require_once(DOL_DOCUMENT_ROOT.'/core/class/html.formother.class.php');
require_once(DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php');
dol_include_once("/google/lib/google.lib.php");
dol_include_once('/google/lib/google_calendar.lib.php');

if (!$user->admin) accessforbidden();

$langs->load("google@google");
$langs->load("admin");
$langs->load("other");

$def = array();
$action=GETPOST("action");

$max=(empty($conf->global->GOOGLE_MAX_FOR_MASS_AGENDA_SYNC)?50:$conf->global->GOOGLE_MAX_FOR_MASS_AGENDA_SYNC);

if (empty($conf->global->GOOGLE_AGENDA_NB)) $conf->global->GOOGLE_AGENDA_NB=5;
$MAXAGENDA=empty($conf->global->GOOGLE_AGENDA_NB)?5:$conf->global->GOOGLE_AGENDA_NB;

// List of Google colors (A lot of colors are ignored by Google)
$colorlist=array('7A367A','B1365F','5229A3','7A367A','29527A','2952A3','1B887A','28754E','0D7813','528800','88880E','AB8B00',
'BE6D00','865A5A','705770','4E5D6C','5A6986','6E6E41','8D6F47','691426','5C1158','125A12','875509','754916',
'5B123B','42104A','113F47','333333','711616','FFFFFF');


/*
 * Actions
*/

if ($action == 'save')
{
	$db->begin();

	//print 'color='.$color;
	$res=dolibarr_set_const($db,'GOOGLE_LOGIN',trim($_POST["GOOGLE_LOGIN"]),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res=dolibarr_set_const($db,'GOOGLE_DUPLICATE_INTO_GCAL',trim($_POST["GOOGLE_DUPLICATE_INTO_GCAL"]),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res=dolibarr_set_const($db,'GOOGLE_API_SERVICEACCOUNT_CLIENT_ID',trim($_POST["GOOGLE_API_SERVICEACCOUNT_CLIENT_ID"]),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res=dolibarr_set_const($db,'GOOGLE_API_SERVICEACCOUNT_EMAIL',trim($_POST["GOOGLE_API_SERVICEACCOUNT_EMAIL"]),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res=dolibarr_set_const($db,'GOOGLE_DISABLE_EVENT_LABEL_INC_SOCIETE',trim($_POST["GOOGLE_DISABLE_EVENT_LABEL_INC_SOCIETE"]),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res=dolibarr_set_const($db,'GOOGLE_DISABLE_EVENT_LABEL_INC_CONTACT',trim($_POST["GOOGLE_DISABLE_EVENT_LABEL_INC_CONTACT"]),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;
	$res=dolibarr_set_const($db,'GOOGLE_CAL_TZ_FIX',trim($_POST["GOOGLE_CAL_TZ_FIX"]),'chaine',0,'',$conf->entity);
	if (! $res > 0) $error++;

	if (! empty($_FILES['GOOGLE_API_SERVICEACCOUNT_P12KEY_file']['tmp_name']))
	{
		$dir     = $conf->google->multidir_output[$conf->entity]."/";
		$file_OK = is_uploaded_file($_FILES['GOOGLE_API_SERVICEACCOUNT_P12KEY_file']['tmp_name']);
		if ($file_OK)
		{
			dol_mkdir($dir);

			if (@is_dir($dir))
			{
				$newfile=$dir.'/'.dol_sanitizeFileName($_FILES['GOOGLE_API_SERVICEACCOUNT_P12KEY_file']['name']);
				$result = dol_move_uploaded_file($_FILES['GOOGLE_API_SERVICEACCOUNT_P12KEY_file']['tmp_name'], $newfile, 1);
				if (! ($result > 0))
				{
					$errors[] = "ErrorFailedToSaveFile";
					$error++;
				}
				else
				{
					$res=dolibarr_set_const($db,'GOOGLE_API_SERVICEACCOUNT_P12KEY',trim($_FILES['GOOGLE_API_SERVICEACCOUNT_P12KEY_file']['name']),'chaine',0,'',$conf->entity);
				}
			}
			else
			{
				$errors[] = "ErrorFailedToCreateDir";
				$error++;
			}
		}
		else
		{
			$error++;
			switch($_FILES['GOOGLE_API_SERVICEACCOUNT_P12KEY_file']['error'])
			{
				case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
				case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
					$errors[] = "ErrorFileSizeTooLarge";
					break;
				case 3: //uploaded file was only partially uploaded
					$errors[] = "ErrorFilePartiallyUploaded";
					break;
			}
		}
	}

	if (! $error)
	{
		$db->commit();
		setEventMessage($langs->trans("SetupSaved"));
	}
	else
	{
		$db->rollback();
		setEventMessage($errors,'errors');
	}
}

// This is a test action to allow to test creation of event once synchro with Calendar has been enabled.
if (preg_match('/^test/',$action))
{
	include_once(DOL_DOCUMENT_ROOT.'/comm/action/class/actioncomm.class.php');
	include_once(DOL_DOCUMENT_ROOT.'/contact/class/contact.class.php');
	include_once(DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php');

	$object=new ActionComm($db);
	$result=$object->initAsSpecimen();

	$tmpcontact=new Contact($db);
	$tmpcontact->initAsSpecimen();
	$object->contact=$tmpcontact;

	if ($tmpcontact->socid > 0)
	{
		$tmpsoc=new Societe($db);
		$tmpsoc->fetch($tmpcontact->socid);	// Overwrite with value of an existing record
		$object->societe=$tmpsoc;
		$object->thirdparty=$tmpsoc;
	}
//Modif CCA 25/5/21 - Montée version Dolibarr V12 - suppression add
	//$result=$object->add($user);
	$result=$object->create($user);
//Fin Modif CCA 25/5/21 - Montée version Dolibarr V12 - suppression add
	if ($result < 0) $error++;

	if (! $error)
	{
		$object->label='New label';
		$object->location='New location';
		$object->note='New note';
		$object->datep+=3600;
		$object->datef+=3600;

		$result=$object->update($user);
		if ($result < 0) $error++;
	}

	if ($action == 'testall' && ! $error)
	{
		$result=$object->delete();
		if ($result < 0) $error++;
	}
	if (! $error)
	{
		setEventMessage($langs->trans("TestSuccessfull"));
	}
	else
	{
		if ($object->errors) setEventMessage($object->errors,'errors');
		else setEventMessage($object->error,'errors');
	}
}


if (GETPOST('cleanup'))
{
	$nbdeleted=0;

	$userlogin = empty($conf->global->GOOGLE_LOGIN)?'':$conf->global->GOOGLE_LOGIN;

	// Create client/token object
	$key_file_location = $conf->google->multidir_output[$conf->entity]."/".$conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY;
	$force_do_not_use_session=(in_array(GETPOST('action'), array('testall','testcreate'))?true:false);	// false by default
	$servicearray=getTokenFromServiceAccount($conf->global->GOOGLE_API_SERVICEACCOUNT_CLIENT_ID, $conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL, $key_file_location, $force_do_not_use_session);

	if (! is_array($servicearray))
	{
// MODIF CCA 
//		$this->errors[]=$servicearray;
		$errors[]=$servicearray;
		$error++;
	}

	if ($servicearray == null)
	{
		$this->error="Failed to login to Google with credentials provided into setup page ".$conf->global->GOOGLE_API_SERVICEACCOUNT_CLIENT_ID.", ".$conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL.", ".$key_file_location;
		dol_syslog($this->error, LOG_ERR);
// MODIF CCA 
//		$this->errors[]=$this->error;
		$errors[]=$error;
		$error++;
	}
	else
	{
		try {
			$service = new Google_Service_Calendar($servicearray['client']);
			$events = $service->events->listEvents($userlogin);
			while(true)
			{
				foreach ($events->getItems() as $event)
				{
					$dolibarr_id='';
					$extendedProperties=$event->getExtendedProperties();
					if (is_object($extendedProperties))
					{
						$shared=$extendedProperties->getShared();
						$priv=$extendedProperties->getPrivate();
						$dolibarr_id=($priv['dolibarr_id']?$priv['dolibarr_id']:$shared['dol_id']);
					}
					if ($dolibarr_id)
					{
						//echo 'This is a dolibarr event '.$dolibarr_id.' - '.$event->getSummary().'<br>'."\n";
						deleteEventById($servicearray['client'], $event->getId(), $userlogin, $service);
						$nbdeleted++;
					}
				}
				$pageToken = $events->getNextPageToken();
				if ($pageToken)
				{
					$optParams = array('pageToken' => $pageToken);
					$events = $service->events->listEvents($userlogin, $optParams);
				}
				else
				{
					break;
				}
			}
		}
		catch(Exception $e)
		{
// MODIF CCA 
//			$this->errors[] = 'ERROR '.$e->getMessage();
			$errors[] = 'ERROR '.$e->getMessage();
			$error++;
		}
	}

	if ($error)
	{
// MODIF CCA 
//		setEventMessage($this->errors, 'errors');
		setEventMessage($errors, 'errors');
	}
	else
	{
		setEventMessage($langs->trans("XRecordDeleted",$nbdeleted), 'mesgs');
	}
}

if ($action == 'pushallevents')
{
	$nbinserted=0;

	$userlogin = empty($conf->global->GOOGLE_LOGIN)?'':$conf->global->GOOGLE_LOGIN;

	// Create client/token object
	$key_file_location = $conf->google->multidir_output[$conf->entity]."/".$conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY;
	$force_do_not_use_session=(in_array(GETPOST('action'), array('testall','testcreate'))?true:false);	// false by default
	$servicearray=getTokenFromServiceAccount($conf->global->GOOGLE_API_SERVICEACCOUNT_CLIENT_ID, $conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL, $key_file_location, $force_do_not_use_session);
//MODIF CCA
	$errors=array();
	if (! is_array($servicearray)) 
	{
//MODIF CCA
		//$this->errors[]=$servicearray;
		$errors[]=$servicearray;
// Fin MODIF
		$error++;
	}

	if ($servicearray == null)
	{
		$this->error="Failed to login to Google with credentials provided into setup page ".$conf->global->GOOGLE_API_SERVICEACCOUNT_CLIENT_ID.", ".$conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL.", ".$key_file_location;
		dol_syslog($this->error, LOG_ERR);
// MODIF CCA 
//		$this->errors[]=$this->error;			
		$errors[]=$error;
// MODIF CCA 
		$error++;
	}
	elseif ( is_array($servicearray))
	{
		try {
			$service = new Google_Service_Calendar($servicearray['client']);

			// Search all events
// MODIF CCA 17/5/2021 - Montée Version Dolibarr V12 - suppression punctual
			//$sql = 'SELECT id, datep, datep2 as datef, code, label, transparency, priority, fulldayevent, punctual, percent, location, fk_soc, fk_contact, note';
			$sql = 'SELECT id, datep, datep2 as datef, code, label, transparency, priority, fulldayevent, percent, location, fk_soc, fk_contact, note';
//Fin  MODIF CCA 17/5/2021 -
			$sql.= ' FROM '.MAIN_DB_PREFIX.'actioncomm';
			$sql.=$db->order('datep','DESC');
			$sql.=$db->plimit($max);
			$resql = $db->query($sql);
			if (! $resql)
			{
				dol_print_error($db);
				exit;
			}
			$synclimit = 0;	// 0 = all
			$i=0;
			while (($obj = $db->fetch_object($resql)) && ($i < $synclimit || empty($synclimit)))
			{
				$object = new ActionComm($db);
				$object->id=$obj->id;
				$object->datep=$db->jdate($obj->datep);
				$object->datef=$db->jdate($obj->datef);
				$object->code=$obj->code;
				$object->label=$obj->label;
				$object->transparency=$obj->transparency;
				$object->priority=$obj->priority;
				$object->fulldayevent=$obj->fulldayevent;
				$object->punctual=$obj->punctual;
				$object->percent=$obj->percent;
				$object->location=$obj->location;
				$object->socid=$obj->fk_soc;
				$object->contactid=$obj->fk_contact;
				$object->note=$obj->note;

				// Event label can now include company and / or contact info, see configuration
				google_complete_label_and_note($object, $langs);

				$ret = createEvent($servicearray, $object, $userlogin);
				if (! preg_match('/ERROR/',$ret))
				{
					if (! preg_match('/google\.com/',$ret)) $ret='google:'.$ret;
					$object->update_ref_ext($ret);	// This is to store ref_ext to allow updates
					$nbinserted++;
				}
				else
				{
// MODIF CCA 
//					$this->errors[]=$ret;
					$errors[]=$ret;
					$error++;
				}

				$i++;
			}
		}
		catch(Exception $e)
		{
// MODIF CCA 
//			$this->errors[] = 'ERROR '.$e->getMessage();
			$errors[] = 'ERROR '.$e->getMessage();
			$error++;
		}
	}

	setEventMessage($langs->trans("PushToGoogleSucess",$nbinserted), 'mesgs');
	if ($error)
	{
// MODIF CCA 
		//setEventMessage($this->errors, 'errors');
		setEventMessage($errors, 'errors');
	}

}



/*
 * View
 */

$form=new Form($db);
$formadmin=new FormAdmin($db);
$formother=new FormOther($db);

$help_url='EN:Module_Google_EN|FR:Module_Google|ES:Modulo_Google';
//$arrayofjs=array('/includes/jquery/plugins/colorpicker/jquery.colorpicker.js');
//$arrayofcss=array('/includes/jquery/plugins/colorpicker/jquery.colorpicker.css');
$arrayofjs=array();
$arrayofcss=array();
llxHeader('',$langs->trans("GoogleSetup"),$help_url,'',0,0,$arrayofjs,$arrayofcss);

$linkback='<a href="'.DOL_URL_ROOT.'/admin/modules.php">'.$langs->trans("BackToModuleList").'</a>';
print_fiche_titre($langs->trans("GoogleSetup"),$linkback,'setup');
print '<br>';


if (! function_exists("openssl_open")) print '<div class="warning">Warning: PHP Module \'openssl\' is not installed</div><br>';
if (! class_exists('DOMDocument')) print '<div class="warning">Warning: PHP Module \'xml\' is not installed</div><br>';


print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="POST" autocomplete="off" enctype="multipart/form-data">';
print '<input type="hidden" name="action" value="save">';

$head=googleadmin_prepare_head();

dol_fiche_head($head, 'tabagendasync', $langs->trans("GoogleTools"));

if ($conf->use_javascript_ajax)
{
	print "\n".'<script type="text/javascript" language="javascript">';
	print 'jQuery(document).ready(function () {
		function initfields()
		{
			if (jQuery("#GOOGLE_DUPLICATE_INTO_GCAL").val() > 0) jQuery(".synccal").show();
			else jQuery(".synccal").hide();
		}
		initfields();
		jQuery("#GOOGLE_DUPLICATE_INTO_GCAL").change(function() {
			initfields();
		});
	})';
	print '</script>'."\n";
}

print $langs->trans("GoogleEnableSyncToCalendar").' '.$form->selectyesno("GOOGLE_DUPLICATE_INTO_GCAL",isset($_POST["GOOGLE_DUPLICATE_INTO_GCAL"])?$_POST["GOOGLE_DUPLICATE_INTO_GCAL"]:$conf->global->GOOGLE_DUPLICATE_INTO_GCAL,1).'<br>';

$var=false;

print '<div class="synccal">';
print '<br>';

print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td>'.$langs->trans("Parameter")."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "</tr>";
// Google login
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("GOOGLE_FIX_TZ")."</td>";
print "<td>";
print '<input class="flat" type="text" size="4" name="GOOGLE_CAL_TZ_FIX" value="'.$conf->global->GOOGLE_CAL_TZ_FIX.'">';
print ' &nbsp; '.$langs->trans("FillThisOnlyIfRequired");
print "</td>";
print "</tr>";

print '</table>';


print '<br>';

$var=true;

print "<table class=\"noborder\" width=\"100%\">";

print "<tr class=\"liste_titre\">";
print '<td class="nowrap">'.$langs->trans("Parameter").' ('.$langs->trans("ParametersForGoogleAPIv3Usage","Calendar").')'."</td>";
print "<td>".$langs->trans("Value")."</td>";
print "<td>".$langs->trans("Note")."</td>";
print "</tr>";

// Google login
$var=!$var;
print "<tr ".$bc[$var].">";
print '<td>'.$langs->trans("GoogleIDAgenda")."</td>";
print "<td>";
print '<input class="flat" type="text" size="24" name="GOOGLE_LOGIN" autocomplete="off" value="'.$conf->global->GOOGLE_LOGIN.'">';
print "</td>";
print '<td>';
print $langs->trans("Example").": yourlogin@gmail.com, email@mydomain.com, 'primary'<br>";
print $langs->trans("GoogleSetupHelp").'<br>';
print $langs->trans("KeepEmptyYoUseLoginPassOfEventUser");
print '</td>';
print "</tr>";

/*
$var=!$var;
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_API_SERVICEACCOUNT_CLIENT_ID")."</td>";
print '<td>';
print '<input class="flat" type="text" size="90" name="GOOGLE_API_SERVICEACCOUNT_CLIENT_ID" value="'.$conf->global->GOOGLE_API_SERVICEACCOUNT_CLIENT_ID.'">';
print '</td>';
print '<td>';
print $langs->trans("AllowGoogleToLoginWithServiceAccount","https://code.google.com/apis/console/","https://code.google.com/apis/console/").'<br>';
print '</td>';
print '</tr>';
*/

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_API_SERVICEACCOUNT_EMAIL")."</td>";
print '<td>';
print '<input class="flat" type="text" size="90" name="GOOGLE_API_SERVICEACCOUNT_EMAIL" value="'.$conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL.'">';
print '</td>';
print '<td>';
print $langs->trans("AllowGoogleToLoginWithServiceAccount","https://code.google.com/apis/console/","https://code.google.com/apis/console/").'<br>';
print '</td>';
print '</tr>';

$var=!$var;
print "<tr ".$bc[$var].">";
print '<td class="fieldrequired">'.$langs->trans("GOOGLE_API_SERVICEACCOUNT_P12KEY")."</td>";
print '<td>';
if (! empty($conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY)) print $conf->global->GOOGLE_API_SERVICEACCOUNT_P12KEY.'<br>';
print '<input type="file" name="GOOGLE_API_SERVICEACCOUNT_P12KEY_file">';
print '</td>';
print '<td>';
print $langs->trans("AllowGoogleToLoginWithServiceAccountP12","https://code.google.com/apis/console/","https://code.google.com/apis/console/").'<br>';
print '</td>';
print '</tr>';

print "</table>";

print info_admin($langs->trans("EnableAPI","https://code.google.com/apis/console/","https://code.google.com/apis/console/","Calendar API"));

print info_admin($langs->trans("ShareCalendarWithServiceAccount",$conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL,$langs->transnoentitiesnoconv("GoogleIDAgenda")));

print '</div>';

dol_fiche_end();

print '<div align="center">';
print "<input type=\"submit\" name=\"save\" class=\"button\" value=\"".$langs->trans("Save")."\">";
print "</div>";

print "</form>\n";

print '<br>';


// Test area

print '<div class="tabsActions">';

print '<div class="synccal">';
if (empty($conf->global->GOOGLE_API_SERVICEACCOUNT_EMAIL) || empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL) || empty($conf->global->GOOGLE_LOGIN))
{
	print '<a class="butActionRefused" href="#">'.$langs->trans("TestCreateUpdateDelete")."</a>";

	print '<a class="butActionRefused" href="#">'.$langs->trans("TestCreate")."</a>";
}
else
{
	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=testall">'.$langs->trans("TestCreateUpdateDelete")."</a>";

	print '<a class="butAction" href="'.$_SERVER['PHP_SELF'].'?action=testcreate">'.$langs->trans("TestCreateUpdate")."</a>";
}
print '</div>';

print '</div>';

print '<br>';


print '<div class="synccal">';

if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL))
{
	print '<br>';
	print '<br>';

	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="action" value="pushallevents">';
	print $langs->trans("ExportEventsToGoogle",$max,$conf->global->GOOGLE_LOGIN)." ";
	print '<input type="submit" name="pushall" class="button" value="'.$langs->trans("Run").'"';
	if (empty($conf->global->GOOGLE_LOGIN)) print ' disabled="disabled"';
	print '>';
	print "</form>\n";
}

if (! empty($conf->global->GOOGLE_DUPLICATE_INTO_GCAL))
{
	print '<form name="googleconfig" action="'.$_SERVER["PHP_SELF"].'" method="post">';
	print '<input type="hidden" name="action" value="deleteallevents">';
	print $langs->trans("DeleteAllGoogleEvents",$conf->global->GOOGLE_LOGIN)." ";
	print '<input type="submit" name="cleanup" class="button" value="'.$langs->trans("Run").'"';
	if (empty($conf->global->GOOGLE_LOGIN)) print ' disabled="disabled"';
	print '>';
	print "</form>\n";
}

print '</div>';

llxFooter();

if (is_object($db)) $db->close();
?>
