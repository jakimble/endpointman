<?php 
/*
Endpoint Manager V2
Copyright (C) 2009-2010  Ed Macri, John Mullinix and Andrew Nagy 

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

// Check for safe mode
if( ini_get('safe_mode') ){
	die(_('Turn Off Safe Mode'));
}

if(file_exists('/tftpboot')) {
	if(!is_writeable('/tftpboot')) {
		die(_('/tftpboot is not writable'));
	}
} else {
	die(_("Please create /tftpboot, even if you won't use it"));
}

include 'includes/functions.inc';

global $endpoint, $debug;

$debug = NULL;

$endpoint = new endpointmanager();

global $global_cfg, $debug;

if(!is_writeable(LOCAL_PATH)) {
	chmod(LOCAL_PATH, 0764);
}

if(!is_writeable(PHONE_MODULES_PATH)) {
	chmod(PHONE_MODULES_PATH, 0764);
}

if(!file_exists(LOCAL_PATH.".htaccess")) {
$htaccess = "allow from all
AuthName FreePBX-Admin-only
Require valid-user
AuthType Basic
AuthMySQLEnable\tOn
AuthMySQLHost\tlocalhost
AuthMySQLDB\tasterisk
AuthMySQLUserTable\tampusers
AuthMySQLUser\t".$amp_conf['AMPDBUSER']."
AuthMySQLPassword\t".$amp_conf['AMPDBPASS']."
AuthMySQLNameField\tusername
AuthMySQLPasswordField\tpassword
AuthMySQLAuthoritative\tOn
AuthMySQLPwEncryption\tnone
AuthMySQLUserCondition\t\"username = 'admin'\"

<Files .*>
  deny from all
</Files>
";

	$outfile = LOCAL_PATH.".htaccess";
	$wfh=fopen($outfile,'w');
	fwrite($wfh,$htaccess);
	fclose($wfh);
}

if($amp_conf['AMPENGINE'] != 'asterisk') {
	die(_("Sorry, Only Asterisk is supported currently"));
}

if (isset($_REQUEST['page'])) {
	$page = $_REQUEST['page'];
} else {
	$page = "";
}

if($global_cfg['debug']) {
	$debug .= "Request Variables: \n".print_r($_REQUEST, TRUE);
}

switch ($page) {
	case 'advanced':
		include LOCAL_PATH.'includes/advanced.inc';	
		break;
	
	case 'template_manager':
		include LOCAL_PATH.'includes/template_manager.inc';	
		break;

	case 'devices_manager';
		include LOCAL_PATH.'includes/devices_manager.inc';
		break;

	case 'brand_model_manager':
		include LOCAL_PATH.'includes/brand_model_manager.inc';
		break;
		
	case 'installer':
	  include LOCAL_PATH.'install.php';
	  break;

	default:
		include LOCAL_PATH.'includes/devices_manager.inc';
}
?>