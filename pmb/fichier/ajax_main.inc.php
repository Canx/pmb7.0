<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ajax_main.inc.php,v 1.4.6.2 2020/11/05 12:32:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$prefix = "gestfic0";
switch($categ){
	
	case 'fiche':
		include('./fichier/ajax/fiche_ajax.inc.php');
		break;
	case 'dashboard' :
		include("./dashboard/ajax_main.inc.php");
		break;
	case 'plugin' :
		$plugins = plugins::get_instance();
		$file = $plugins->proceed_ajax("fichier",$plugin,$sub);
		if($file){
			include $file;
		}
		break;
	case 'extended_search' :
		require_once($class_path."/search.class.php");
		if(!isset($search_xml_file)) $search_xml_file = '';
		if(!isset($search_xml_file_full_path)) $search_xml_file_full_path = '';
	
		$sc=new search(true, $search_xml_file, $search_xml_file_full_path);
		$sc->proceed_ajax();
		break;
	case 'consult':
		switch($action) {
			case "list":
				if(isset($object_type) && $object_type) {
					$class_name = 'list_'.$object_type;
					$filters = (!empty($filters) ? encoding_normalize::json_decode(stripslashes($filters),true) : array());
					$pager = (!empty($pager) ? encoding_normalize::json_decode(stripslashes($pager),true) : array());
					$instance_class_name = new $class_name($filters, $pager, array('by' => $sort_by, 'asc_desc' => (isset($sort_asc_desc) ? $sort_asc_desc : '')));
					print encoding_normalize::utf8_normalize($instance_class_name->get_display_header_list());
					print encoding_normalize::utf8_normalize($instance_class_name->get_display_content_list());
				}
				break;
		}
		break;
	
}