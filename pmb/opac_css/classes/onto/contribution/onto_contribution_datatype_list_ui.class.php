<?php
// +-------------------------------------------------+
// � 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_list_ui.class.php,v 1.1 2018/10/12 15:12:43 apetithomme Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($class_path.'/onto/common/onto_common_datatype_list_ui.class.php');

class onto_contribution_datatype_list_ui extends onto_common_datatype_list_ui {
	
	/**
	 * A d�river pour filtrer la liste des valeurs � afficher dans le s�lecteur
	 * @return array
	 */
	public static function get_list_values_to_display($property) {
		return ($property->pmb_extended['list_values'] ? explode(',', $property->pmb_extended['list_values']) : array());
	}
}