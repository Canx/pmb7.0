<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.1.20.2 2021/02/08 11:00:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'profil':
		include("./admin/harvest/build.inc.php");		
		break;
	case 'profil_import':
		include("./admin/harvest/profil.inc.php");		
		break;
	default:
		include("$include_path/messages/help/$lang/admin_harvest.txt");
		break;
}
?>