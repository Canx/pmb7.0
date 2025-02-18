<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_etageres_ui.class.php,v 1.1.2.10 2020/12/08 13:39:36 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;

require_once($class_path."/etagere.class.php");

class list_etageres_ui extends list_ui {
	
	public function get_form_title() {
		return '';
	}
	
	protected function get_html_title() {
		return '';
	}
	
	protected function _get_query_base() {
		return "SELECT * FROM etagere";
	}
	
	protected function get_object_instance($row) {
		return new etagere($row->idetagere);
	}
	
	protected function add_object($row) {
		global $PMBuserid;
		
		$rqt_autorisation=explode(" ",$row->autorisations);
		if (array_search ($PMBuserid, $rqt_autorisation)!==FALSE || $PMBuserid==1) {
			$this->objects[] = $this->get_object_instance($row);
		}
	}
		
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$this->filters = array(
		);
		parent::init_filters($filters);
	}
	
	public function init_applied_group($applied_group=array()) {
		$this->applied_group = array(0 => 'classement_label');
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns = array(
			'main_fields' => array(
					'name' => 'etagere_name',
					'comment_gestion' => 'etagere_comment_gestion',
					'nb_paniers' => 'etagere_cart_count',
					'validity' => 'etagere_visible_date',
					'home_visibility' => 'etagere_visible_accueil',
					'classement_label' => '',
					'classement_selector' => '',
			),
		);
	}
	
	protected function init_default_columns() {
		global $sub;
		
		$this->add_column('name');
		$this->add_column('comment_gestion');
		$this->add_column('nb_paniers');
		$this->add_column('validity');
		$this->add_column('home_visibility');
		if($sub != 'constitution') {
			$this->add_column('classement_selector');
		}
	}
	
	/**
	 * Initialisation des settings par d�faut
	 */
	protected function init_default_settings() {
		global $deflt_catalog_expanded_caddies;
		
		parent::init_default_settings();
		$this->set_setting_display('search_form', 'export_icons', false);
		$this->set_setting_column('default', 'align', 'left');
		$this->settings['objects']['default']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['display_mode'] = 'expandable_table';
		$this->settings['grouped_objects']['level_1']['expanded_display'] = $deflt_catalog_expanded_caddies;
	}
	
	protected function init_no_sortable_columns() {
		$this->no_sortable_columns = array(
				'name', 'comment_gestion', 'nb_paniers', 'validity', 'home_visibility',
				'classement_selector'
		);
	}
	
	/**
	 * Initialisation de la pagination par d�faut
	 */
	protected function init_default_pager() {
		parent::init_default_pager();
		$this->pager['nb_per_page'] = pmb_mysql_result(pmb_mysql_query("SELECT count(*) FROM etagere"), 0, 0); //Illimit�;
		$this->set_pager_in_session();
	}
	
	/**
	 * Initialisation du tri par d�faut appliqu�
	 */
	protected function init_default_applied_sort() {
		$this->add_applied_sort('name');
	}
		
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {

		parent::set_filters_from_form();
	}
			
	/**
	 * Construction dynamique de la fonction JS de tri
	 */
	protected function get_js_sort_script_sort() {
		$display = parent::get_js_sort_script_sort();
		$display = str_replace('!!categ!!', 'shelves', $display);
		$display = str_replace('!!sub!!', '', $display);
		$display = str_replace('!!action!!', 'list', $display);
		return $display;
	}
		
	protected function get_cell_content($object, $property) {
		global $msg, $opac_url_base;
		
		$content = '';
		switch($property) {
			case 'name':
				$content .= "<strong>".$object->name."</strong>".($object->comment?" (".$object->comment.")":"");
				break;
			case 'nb_paniers':
				$sql = "SELECT COUNT(*) FROM etagere_caddie WHERE etagere_id = ".$object->idetagere;
				$res = pmb_mysql_query($sql);
				$content .= pmb_mysql_result($res, 0, 0);
				break;
			case 'validity':
				if($object->validite) {
					$content .= $msg['etagere_visible_date_all'];
				} else {
					$content .= $msg['etagere_visible_date_du']." ".$object->validite_date_deb_f." ".$msg['etagere_visible_date_fin']." ".$object->validite_date_fin_f;
				}
				break;
			case 'home_visibility':
				if($object->visible_accueil) {
					$content .= "X";
				}
				$content .= "<br /><a href='".$opac_url_base."index.php?lvl=etagere_see&id=".$object->idetagere."' target=_blank>".$opac_url_base."index.php?lvl=etagere_see&id=".$object->idetagere."</a>";
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function get_display_cell($object, $property) {
		global $sub;
		
		$onclick="";
		switch($property) {
			case 'name':
			case 'comment_gestion':
			case 'nb_paniers':
			case 'validity':
				$onclick = "document.location=\"".static::get_controller_url_base()."&sub=".($sub ? $sub : "edit_etagere")."&action=edit_etagere&idetagere=".$object->idetagere."\"";
				break;
		}
		$attributes = array(
				'onclick' => $onclick,
		);
		$content = $this->get_cell_content($object, $property);
		$display = $this->get_display_format_cell($content, $property, $attributes);
		return $display;
	}
	
	public function get_display_list() {
		global $sub;
		
		//R�cup�ration du script JS de tris
		$display = $this->get_js_sort_script_sort();
		$display .= "<script src='./javascript/classementGen.js' type='text/javascript'></script>";
		if($sub != 'constitution') {
			$display .= "
			<div class='row'>
				".$this->get_button_add()."
			</div><br>";
		}
		
		//Affichage de la liste des objets
		$display .= $this->get_display_objects_list();
		if(count($this->get_selection_actions())) {
			$display .= $this->get_display_selection_actions();
		}
		if($sub != 'constitution') {
			$display .= "
			<div class='row'>
				".$this->get_button_add()."
			</div>";
		}
		return $display;
	}
	
	public function get_error_message_empty_list() {
		global $msg;
		return $msg['etagere_no_etagere'];
	}
	
	protected function get_button_add() {
		global $msg;
		
		return "<input class='bouton' type='button' value=' ".$msg["etagere_new_etagere"]." ' onClick=\"document.location='".static::get_controller_url_base()."&sub=gestion&action=new_etagere'\" />";
	}
	
	protected function get_selection_actions() {
		global $msg;
	
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
// 			$this->selection_actions[] = $this->get_selection_action('delete', $msg['delete'], '', $this->get_link_action('', 'href'));
		}
		return $this->selection_actions;
	}
	
	public static function get_controller_url_base() {
		global $base_path;
		
		return $base_path.'/catalog.php?categ=etagere';
	}
}