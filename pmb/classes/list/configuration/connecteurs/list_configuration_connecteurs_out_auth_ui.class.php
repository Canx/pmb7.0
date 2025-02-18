<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_configuration_connecteurs_out_auth_ui.class.php,v 1.1.2.4 2021/02/24 09:17:41 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/external_services_esusers.class.php");

class list_configuration_connecteurs_out_auth_ui extends list_configuration_connecteurs_ui {
	
	protected function _get_query_base() {
		return 'SELECT esgroup_id FROM es_esgroups';
	}
	
	protected function fetch_data() {
		global $msg;
		
		parent::fetch_data();
		//Ajoutons l'utilisateur anonyme
		$sql = "SELECT COUNT(1) FROM connectors_out_sources_esgroups WHERE connectors_out_source_esgroup_esgroupnum = -1";
		$anonymous_count = pmb_mysql_result(pmb_mysql_query($sql), 0, 0);
		$this->add_object((object) array(
				'esgroup_id' => 0,
				'esgroup_name' => "&lt;".$msg["admin_connecteurs_outauth_anonymgroupname"]."&gt;",
				'esgroup_fullname' => $msg["admin_connecteurs_outauth_anonymgroupfullname"],
				'connecteurs' => $anonymous_count)
		);
	}
	
	protected function get_object_instance($row) {
		if($row->esgroup_id) {
			return new es_esgroup($row->esgroup_id);
		} else {
			return parent::get_object_instance($row);
		}
	}
	
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('esgroup_name');
	}
	
	protected function _get_query_filters() {
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		$filters[] = 'esgroup_id <> -1';
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	protected function get_main_fields_from_sub() {
		return array(
				'esgroup_name' => 'es_group_name',
				'esgroup_fullname' => 'es_group_fullname',
				'connecteurs' => 'connector_out_authorization_authorizedsourcecount'
		);
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'connecteurs':
				if($object->esgroup_id) {
					//Récupérons le nombre de sources autorisées dans le groupe
					$count_query = "SELECT COUNT(1) FROM connectors_out_sources_esgroups WHERE connectors_out_source_esgroup_esgroupnum = ".$object->esgroup_id;
					$conn_count = pmb_mysql_result(pmb_mysql_query($count_query), 0, 0);
					$content .= $conn_count;
				} else {
					$content .= $object->{$property};
				}
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_edition_link($object) {
		if($object->esgroup_id) {
			return static::get_controller_url_base().'&action=edit&id='.$object->esgroup_id;
		} else {
			return static::get_controller_url_base().'&action=editanonymous';
		}
	}
	
	protected function get_button_add() {
		return "";
	}
}