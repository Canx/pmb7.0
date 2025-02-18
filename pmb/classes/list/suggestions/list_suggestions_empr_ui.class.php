<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_suggestions_empr_ui.class.php,v 1.1.2.2 2021/03/25 08:55:00 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_suggestions_empr_ui extends list_ui {
		
	protected $suggestions_map;
	
	public function get_title() {
		global $msg, $charset;
		
		return "<h1>".htmlentities($msg['acquisition_sug_ges'], ENT_QUOTES, $charset)."</h1>";
	}
	
	public function get_form_title() {
		global $msg, $charset;
		
		return htmlentities($msg['acquisition_sugg_list_lecteur'], ENT_QUOTES, $charset);
	}
	
	protected function _get_query_base() {
		$query = "select count(id_suggestion) as nb, concat(empr_nom,' ',empr_prenom) as name, id_empr as id, empr_location from suggestions 
				JOIN suggestions_origine ON id_suggestion=num_suggestion
				JOIN empr ON origine=id_empr";
		return $query;
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'state' => 'acquisition_sugg_filtre_by_etat',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'state' => -1,
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('state');
	}
	
	protected function get_search_filter_state() {
		return $this->get_suggestions_map()->getStateSelector($this->filters['state']);
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($this->filters['state'] && $this->filters['state'] != '-1') {
			$filters[] = "statut='".$this->filters['state']."'";
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);
		}
		return $filter_query;
	}
			
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'sugg', $display);
		$display = str_replace('!!sub!!', '', $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
		
	/**
	 * Objet de la liste
	 */
	protected function get_display_content_object_list($object, $indice) {
		if(!isset($this->is_editable_object_list)) {
			$this->is_editable_object_list = true;
		}
		return parent::get_display_content_object_list($object, $indice);
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg['acquisition_sugg_no_state_lecteur'], ENT_QUOTES, $charset);
	}
	
	protected function _get_query_human_state() {
		if($this->filters['state'] && $this->filters['state'] != '-1') {
			$states = $this->get_suggestions_map()->getStateList();
			return $states[$this->filters['state']];
		}
		return '';
	}
	
	protected function _get_query_human() {
		$humans = $this->_get_query_human_main_fields();
		return $this->get_display_query_human($humans);
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'name' => 'acquisition_sugg_lecteur',
						'nb' => 'acquisition_sugg_nb',
				)
		);
	}
	
	protected function init_default_columns() {
		$this->add_column('name');
		$this->add_column('nb');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_display('search_form', 'unfolded_filters', true);
	}
	
	/**
	 * Initialisation du tri par d�faut appliqu�
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('name');
	}
	
	/**
	 * Tri SQL
	 */
	protected function _get_query_order() {
		
		if($this->applied_sort[0]['by']) {
			$order = '';
			$sort_by = $this->applied_sort[0]['by'];
			switch($sort_by) {
				default :
					$order .= $sort_by;
					break;
			}
			if($order) {
				$this->applied_sort_type = 'SQL';
				return " group by name order by ".$order." ".$this->applied_sort[0]['asc_desc'];
			} else {
				return " group by name";
			}
		}
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$state = 'statut';
		global ${$state};
		if(isset(${$state})) {
			$this->filters['state'] = ${$state};
		}
		parent::set_filters_from_form();
	}
	
	protected function get_edition_link($object) {
		global $base_path;
		
		return $base_path.'/acquisition.php?categ=sug&action=list&user_id[]='.$object->id.'&user_statut[]=1&sugg_location_id='.$object->empr_location;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
	
		return $base_path.'/acquisition.php?categ=sug&sub=empr_sug';
	}
	
	public function get_suggestions_map() {
		if(!isset($this->suggestions_map)) {
			$this->suggestions_map = new suggestions_map();
		}
		return $this->suggestions_map;
	}
}