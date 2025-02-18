<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_serialcirc_ask_ui.class.php,v 1.1.2.6 2021/02/12 22:31:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/serialcirc.class.php");

class list_serialcirc_ask_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'select * from serialcirc_ask ';
		return $query;
	}
		
	protected function get_object_instance($row) {
		return new serialcirc_ask($row->id_serialcirc_ask);
	}
	
	/**
	 * Initialisation du tri par d�faut appliqu�
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('date', 'desc');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'date' => 'serialcirc_asklist_date',
						'empr' => 'serialcirc_asklist_empr',
						'type' => 'serialcirc_asklist_type',
						'serial' => 'serialcirc_asklist_perio',
						'status' => 'serialcirc_asklist_statut',
						'comment' => 'serialcirc_asklist_comment',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function get_title() {
		global $msg, $charset;
		
		$title = "<h1>".htmlentities($msg["serialcirc_asklist_title"],ENT_QUOTES,$charset)."</h1>";
		return $title;
	}
	
	protected function get_form_title() {
		global $msg, $charset;
		return htmlentities($msg['serialcirc_asklist_title_form'], ENT_QUOTES, $charset);
	}
	
	protected function get_link_action($action) {
		return array(
				'href' => static::get_controller_url_base()."&action=".$action
		);
	}
	
	protected function get_selection_actions() {
		global $msg;
	
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array(
					$this->get_selection_action('accept', $msg['serialcirc_asklist_accept_bt'], '', $this->get_link_action('accept')),
					$this->get_selection_action('refus', $msg['serialcirc_asklist_refus_bt'], '', $this->get_link_action('refus')),
					$this->get_selection_action('delete', $msg['serialcirc_asklist_delete_bt'], '', $this->get_link_action('delete'))
				);
		}
		return $this->selection_actions;
	}
	
	protected function get_selection_mode() {
		return 'button';
	}
	
	protected function get_name_selected_objects() {
		return "asklist_id";
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('date');
		$this->add_column('empr');
		$this->add_column('type');
		$this->add_column('serial');
		$this->add_column('status');
		$this->add_column('comment');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'location' => 'serialcirc_asklist_location_title',
						'type' => 'serialcirc_asklist_type_title',
						'status' => 'serialcirc_asklist_statut_title',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'location' => '',
				'type' => '-1',
				'status' => '-1',
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('location');
		$this->add_selected_filter('type');
		$this->add_selected_filter('status');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$location = $this->objects_type.'_location';
		global ${$location};
		if(isset(${$location}) && ${$location} != '') {
			$this->filters['location'] = ${$location};
		}
		$this->set_filter_from_form('type');
		$this->set_filter_from_form('status');
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_location() {
		global $msg;
		
		return gen_liste("select distinct idlocation, location_libelle from docs_location, docsloc_section where num_location=idlocation order by 2 ", "idlocation", "location_libelle", $this->objects_type.'_location', "calcule_section(this);", $this->filters['location'], "", "",0,$msg["serialcirc_asklist_location_all"],0);
	}
	
	protected function get_search_filter_type() {
		global $msg;
		
		return $this->gen_selector($this->objects_type.'_type',
				array(
						-1=>$msg['serialcirc_asklist_type_all'],
						0=>$msg['serialcirc_asklist_type_0'],
						1=>$msg['serialcirc_asklist_type_1']
				),	$this->filters['type']);
	}
	
	protected function get_search_filter_status() {
		global $msg;
		
		return $this->gen_selector($this->objects_type.'_status',
				array(
						-1=>$msg['serialcirc_asklist_statut_all'],
						0=>$msg['serialcirc_asklist_statut_0'],
						1=>$msg['serialcirc_asklist_statut_1'],
						2=>$msg['serialcirc_asklist_statut_2'],
						3=>$msg['serialcirc_asklist_statut_3']
				),$this->filters['status']);
	}
	
	protected function gen_selector($name,$field_list,$value=0){
		global $charset;
		$selector="<select name='$name' id='$name'>";
		foreach($field_list as $val =>$field) {
			$selector.= "<option value='".$val."'";
			$val == $value ? $selector .= ' selected=\'selected\'>' : $selector .= '>';
			$selector.= htmlentities($field,ENT_QUOTES, $charset).'</option>';
		}
		return $selector.'</select>';
	}
	
	/**
	 * Jointure externes SQL pour les besoins des filtres
	 */
	protected function _get_query_join_filters() {
		$filter_join_query = '';
		if($this->filters['location']) {
			$filter_join_query .= " JOIN empr ON num_serialcirc_ask_empr=id_empr";
		}
		return $filter_join_query;
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($this->filters['location']) {
			$filters [] = 'empr_location = "'.$this->filters['location'].'"';
		}
		if($this->filters['type'] !== '-1') {
			$filters [] = 'serialcirc_ask_type = "'.$this->filters['type'].'"';
		}
		if($this->filters['status'] !== '-1') {
			$filters [] = 'serialcirc_ask_statut = "'.$this->filters['status'].'"';
		}
		if(count($filters)) {
			$filter_query .= $this->_get_query_join_filters();
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
	
	/**
	 * Fonction de callback
	 * @param object $a
	 * @param object $b
	 */
	protected function _compare_objects($a, $b) {
		global $msg;
		if($this->applied_sort[0]['by']) {
			$sort_by = $this->applied_sort[0]['by'];
			switch($sort_by) {
				case 'date':
				case 'comment':
					return strcmp($a->ask_info[$sort_by], $b->ask_info[$sort_by]);
					break;
				case 'empr':
					return strcmp($a->ask_info['empr']["empr_libelle"], $b->ask_info['empr']["empr_libelle"]);
					break;
				case 'type':
					return strcmp($msg['serialcirc_asklist_type_'.$a->ask_info[$sort_by]], $msg['serialcirc_asklist_type_'.$b->ask_info[$sort_by]]);
					break;
				case 'serial':
					
					break;
				case 'status':
					return strcmp($msg['serialcirc_asklist_statut_'.$a->ask_info['statut']], $msg['serialcirc_asklist_statut_'.$b->ask_info['statut']]);
					break;
				default :
					return parent::_compare_objects($a, $b);
					break;
			}
		}
	}
	
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		global $sub;
		
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'serials', $display);
		$display = str_replace('!!sub!!', $sub, $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
			
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["serialcirc_asklist_no"], ENT_QUOTES, $charset);
	}
	
	protected function get_cell_content($object, $property) {
	    global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'date':
			case 'comment':
				$content .= $object->ask_info[$property];
				break;
			case 'empr':
				$content .= "<a href='".$object->ask_info['empr']['view_link']."'>".htmlentities($object->ask_info['empr']["empr_libelle"],ENT_QUOTES,$charset)."</a>";
				break;
			case 'type':
				$content .= $msg['serialcirc_asklist_type_'.$object->ask_info[$property]];
				break;
			case 'serial':
				$abt_list="";
				if($object->ask_info['type']==0){
					foreach($object->ask_info['abts'] as $abt){
						$abt_list.="<br /><a href='". $abt['link_diff'] ."' >".$abt['name']." </a>";
					}
				}
				$content .= "<a href='".$object->ask_info['perio']['view_link']."'>".$object->ask_info['perio']['header'].$abt_list;
				break;
			case 'status':
				$content .= $msg['serialcirc_asklist_statut_'.$object->ask_info['statut']];
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function _get_query_human_location() {
		if($this->filters['location']) {
			$docs_location = new docs_location($this->filters['location']);
			return $docs_location->libelle;
		}
		return '';
	}
	
	protected function _get_query_human_type() {
		global $msg;
		if($this->filters['type'] !== -1) {
			return $msg['serialcirc_asklist_type_'.$this->filters['type']];
		}
		return '';
	}
	
	protected function _get_query_human_status() {
		global $msg;
		if($this->filters['status'] !== -1) {
			return $msg['serialcirc_asklist_statut_'.$this->filters['status']];
		}
		return '';
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/catalog.php?categ=serials&sub=circ_ask';
	}
}