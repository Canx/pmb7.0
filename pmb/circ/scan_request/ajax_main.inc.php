<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.5.2.1 2020/11/05 09:50:54 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

//Point d'entr�e d'upload, de suppression et d'ajout de documents num�riques sur une demande
/**
 * Id de demande n�cessaire pour tout les traitements
 * Id de notice ? (a voir si la valorisation ne se fait pas plut�t au post du formulaire)
 */
require_once($class_path.'/scan_request/scan_request.class.php');

if(isset($num_bulletin)) $num_bulletin += 0;
else $num_bulletin = 0;
if(isset($num_record)) $num_record += 0;
else $num_record = 0;
if(isset($num_request)) $num_request += 0;
else $num_request = 0;

switch($action) {
    case "list":
        lists_controller::proceed_ajax($object_type, 'scan_requests');
        break;
}

if($num_request == 0 || ($num_bulletin && $num_record)) return;
if($num_request>0){
	$scan_request = new scan_request($num_request);	
	switch($sub){	
		case 'upload':			
			$scan_request->add_explnum();
			break;
		case 'edit':    
			print  $scan_request->get_ajax_form();
			break;
		case 'save':		
			print $scan_request->save_ajax_form();
			break;
		default:
			
			break;
	}
}
