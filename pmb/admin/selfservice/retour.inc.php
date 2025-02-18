<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: retour.inc.php,v 1.5.6.1 2021/03/05 07:38:22 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $action, $form_actif;

// gestion du param�trage de la borne de pr�t

function show_param_retour($action='') {
	global $msg;
	
	if($action=="save") {
		print "<div class='erreur'>".$msg["selfservice_admin_update"]."</div>";
	}
	print list_configuration_selfservice_retour_ui::get_instance()->get_display_list();
	
}

switch($action) {
	case 'save':
		if($form_actif) {
			list_configuration_selfservice_retour_ui::get_instance()->save();
		}
		show_param_retour($action);
	break;
	default:
		show_param_retour();
		break;
}
