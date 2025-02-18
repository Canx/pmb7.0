<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_classements_ui.class.php,v 1.1.2.10 2021/03/26 10:08:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/classements.class.php");

class list_classements_ui extends list_ui {
	
	protected static $type;
	
	public static function set_type($type) {
		static::$type = $type;
	}
	
	public function __construct($filters=array(), $pager=array(), $applied_sort=array()) {
		if(empty($this->objects_type)) {
			$this->objects_type = str_replace('list_', '', get_class($this)).'_'.static::$type;
		}
		parent::__construct($filters, $pager, $applied_sort);
	}
	
	protected function _get_query_base() {
		$query = 'SELECT id_classement FROM classements ';
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new classement($row->id_classement);
	}
	
	protected function _get_query_order() {
	    if ($this->applied_sort[0]['by']) {
			$order = '';
			$sort_by = $this->applied_sort[0]['by'];
			switch($sort_by) {
				case 'order':
					$order .= 'classement_order, nom_classement';
					break;
				default :
					$order .= parent::_get_query_order();
					break;
			}
			if($order) {
				return $this->_get_query_order_sql_build($order);
			} else {
				return "";
			}
		}
	}
	
	/**
	 * Initialisation du tri par d�faut appliqu�
	 */
	protected function init_default_applied_sort() {
	    $this->add_applied_sort('order');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'order' => '',
						'nom_classement' => '103',
						'nom_classement_opac' => 'dsi_clas_form_nom_opac',
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column('order');
		$this->add_column('nom_classement');
		$this->add_column('nom_classement_opac');
	}
	
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'visible', false);
		$this->set_setting_display('search_form', 'export_icons', false);
	}
	
	protected function get_button_add() {
		global $msg;
	
		return "<input class='bouton' type='button' value='".$msg['dsi_clas_ajouter']."' onClick=\"document.location='".static::get_controller_url_base().'&suite=add'."';\" />";
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		if(static::$type == 'EQU') {
			$filters [] = "(type_classement='EQU')";
		} elseif(static::$type !== '') {
			$filters [] = "(type_classement='' or type_classement='".static::$type."')";
		}
		if(count($filters)) {
			$filter_query .= ' where '.implode(' and ', $filters);		
		}
		return $filter_query;
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
		return "<h3>".htmlentities($msg['dsi_clas_type_class_'.static::$type], ENT_QUOTES, $charset)." (".$this->pager['nb_results'].")</h3>";
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
	
	protected function get_cell_content($object, $property) {
		global $msg, $charset;
		
		$content = '';
		switch($property) {
			case 'order':
				$content .= "
						<img src='".get_url_icon('bottom-arrow.png')."' title='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_bottom_arrow'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&id_classement=".$object->id_classement."&suite=down'\" style='cursor:pointer;'/>
						<img src='".get_url_icon('top-arrow.png')."' title='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' alt='".htmlentities($msg['move_top_arrow'], ENT_QUOTES, $charset)."' onClick=\"document.location='".static::get_controller_url_base()."&id_classement=".$object->id_classement."&suite=up'\" style='cursor:pointer;'/>";
				break;
			case 'nom_classement':
				$content .= "<strong>".htmlentities($object->nom_classement,ENT_QUOTES, $charset)."</strong>";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}

	protected function get_display_cell($object, $property) {
		switch ($property) {
			case 'order':
				$attributes = array(
				);
				break;
			default:
				$attributes = array(
						'onclick' => "document.location=\"".static::get_controller_url_base()."&id_classement=".$object->id_classement."&suite=acces\""
				);
				break;
		}
		$content = $this->get_cell_content($object, $property);
		$display = $this->get_display_format_cell($content, $property, $attributes);
		return $display;
	}
	
	/**
	 * Affiche la recherche + la liste
	 */
	public function get_display_list() {
		$display = parent::get_display_list();
		if(static::$type == 'EQU') {
			$display .= "<br />".$this->get_button_add();
		}
		return $display;
	}
	
	public static function get_button_add_empty_lists() {
		global $msg;
		
		return "<br /><input class='bouton' type='button' value='".$msg['dsi_clas_ajouter']."' onClick=\"document.location='".static::get_controller_url_base().'&suite=add'."';\" />";
	}
}