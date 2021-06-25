<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: main.inc.php,v 1.3.8.1 2021/02/08 11:00:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

switch($sub) {
	case 'theme':
		include("./admin/demandes/theme.inc.php");		
		break;
	case 'type':
		include("./admin/demandes/type.inc.php");		
		break;
	case 'perso':
		include("./admin/demandes/perso.inc.php");
		break;
	default:
		break;
}
?>