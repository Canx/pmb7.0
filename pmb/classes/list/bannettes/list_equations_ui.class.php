<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_equations_ui.class.php,v 1.1.2.12 2021/03/26 10:58:57 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/equation.class.php");
require_once($class_path."/classements.class.php");

class list_equations_ui extends list_ui {
	
	protected function _get_query_base() {
		$query = 'SELECT id_equation FROM equations ';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new equation($row->id_equation);
	}
	
	protected function get_form_title() {
		global $msg, $charset;
		return htmlentities($msg['dsi_equ_search'], ENT_QUOTES, $charset);
	}
	
	/**
	 * Initialisation du tri par d�faut appliqu�
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('nom_equation');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'id_equation' => '66',
						'nom_equation' => '67',
						'nom_classement' => 'dsi_clas_type_class_EQU',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column('id_equation');
		$this->add_column('nom_equation');
		$this->add_column('nom_classement');
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
						'name' => 'dsi_equ_search_nom',
						'id_classement' => 'dsi_classement',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
				'id_classement' => '',
				'name' => '',
				'proprio_bannette' => '',
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('name');
		$this->add_selected_filter('id_classement');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		global $id_classement;
		
		$this->set_filter_from_form('name');
		if(isset($id_classement)) {
			$this->filters['id_classement'] = $id_classement;
		}
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_name() {
		global $msg;
		
		return "<input class='saisie-20em' id='".$this->objects_type."_name' type='text' name='".$this->objects_type."_name' value=\"".$this->filters['name']."\" title='$msg[3000]' />";
	}
	
	protected function get_search_filter_id_classement() {
		return gen_liste_classement("EQU", $this->filters['id_classement'], "this.form.submit();");
	}
	
	/**
	 * Affichage du formulaire de recherche
	 */
	public function get_search_form() {
		global $base_path, $categ, $sub;
		$this->is_displayed_add_filters_block = false;
		$search_form = parent::get_search_form();
		$search_form = str_replace('!!action!!', $base_path.'/dsi.php?categ='.$categ.'&sub='.$sub, $search_form);
		return $search_form;
	}
	
	protected function get_button_add() {
		global $msg;
	
		return "<input class='bouton' type='button' value='".$msg['ajouter']."' onClick=\"document.location='./catalog.php?categ=search&mode=6';\" />";
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if($this->filters['id_classement']) {
			$filters [] = 'num_classement = "'.$this->filters['id_classement'].'"';
		} elseif($this->filters['id_classement'] === 0) {
			$filters [] = 'num_classement = "0"';
		}
		if($this->filters['name']) {
			$filters [] = 'nom_equation like "%'.str_replace("*", "%", addslashes($this->filters['name'])).'%"';
		}
		$filters [] = 'proprio_equation = 0';
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);		
		}
		return $filter_query;
	}
		
	protected function _get_query_property_filter($property) {
		switch ($property) {
			case 'id_classement':
				return "select nom_classement from classements where id_classement = ".$this->filters[$property];
		}
		return '';
	}
	
	/**
	 * Fonction de callback
	 * @param object $a
	 * @param object $b
	 */
	protected function _compare_objects($a, $b) {
		if($this->applied_sort[0]['by']) {
			$sort_by = $this->applied_sort[0]['by'];
			switch($sort_by) {
				case 'nom_classement' :
					$a_object = new classement($a->num_classement);
					$b_object = new classement($b->num_classement);
					return strcmp($a_object->nom_classement, $b_object->nom_classement);
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
		$display = str_replace('!!categ!!', 'bannettes', $display);
		$display = str_replace('!!sub!!', $sub, $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
	
	public function get_error_message_empty_list() {
		global $msg, $charset;
		return htmlentities($msg["dsi_no_equation"], ENT_QUOTES, $charset);
	}
	
	protected function get_cell_content($object, $property) {
		global $charset;
		
		$content = '';
		switch($property) {
			case 'id_equation':
				$content .= "<strong>".htmlentities($object->id_equation,ENT_QUOTES, $charset)."</strong>";
				break;
			case 'nom_equation':
				$content .= "<strong>".htmlentities($object->nom_equation,ENT_QUOTES, $charset)."</strong><br />
					".($object->comment_equation ? "($object->comment_equation)" : "&nbsp;");
				break;
			case 'nom_classement':
				$classement = new classement($object->num_classement);
				$content .= htmlentities($classement->nom_classement,ENT_QUOTES, $charset);
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_cell($object, $property) {
		$attributes = array(
				'onclick' => "document.location=\"".static::get_controller_url_base()."&id_equation=".$object->id_equation."&suite=acces\""
		);
		$content = $this->get_cell_content($object, $property);
		$display = $this->get_display_format_cell($content, $property, $attributes);
		return $display;
	}
}