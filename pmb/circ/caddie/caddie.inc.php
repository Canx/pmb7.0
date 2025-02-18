<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: caddie.inc.php,v 1.7.8.1 2021/02/09 07:30:30 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

if(!isset($idcaddie)) $idcaddie = 0;
if(!isset($idemprcaddie)) $idemprcaddie = 0;

// functions particuli�res � ce module
require_once("$include_path/templates/cart.tpl.php");
require_once("$include_path/templates/empr_cart.tpl.php");
require_once("$class_path/empr_caddie.class.php");
require_once("$class_path/parameters.class.php") ;
require_once("$class_path/emprunteur.class.php") ;
require_once("$include_path/empr_cart.inc.php");
require_once("$include_path/cart.inc.php");
require_once("$base_path/circ/empr/empr_func.inc.php");

require_once($class_path."/caddie/empr_caddie_controller.class.php");

$idcaddie = empr_caddie::check_rights($idcaddie) ;

print module_circ::get_instance()->get_display_subtabs();
switch($sub) {
	case "action" :
		include('./circ/caddie/action/main.inc.php');
		break;
	case "gestion" :
	default:
		include('./circ/caddie/gestion/main.inc.php');
		break;
	}

