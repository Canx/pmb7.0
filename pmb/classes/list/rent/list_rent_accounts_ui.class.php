<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_rent_accounts_ui.class.php,v 1.1.2.5 2021/04/07 14:00:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class list_rent_accounts_ui extends list_rent_ui {
		
	protected function _get_query_base() {
		$query = "SELECT id_account FROM rent_accounts";
		return $query;
	}
	
	protected function get_object_instance($row) {
		return new rent_account($row->id_account);
	}
	
	/**
	 * Initialisation des filtres disponibles
	 */
	protected function init_available_filters() {
		$this->available_filters =
		array('main_fields' =>
				array(
						'entity' => 'acquisition_coord_lib',
						'exercice' => 'acquisition_budg_exer',
						'request_type' => 'acquisition_account_request_type_name',
						'type' => 'acquisition_account_type_name',
						'num_publisher' => 'acquisition_account_num_publisher',
						'num_supplier' => 'acquisition_account_num_supplier',
						'num_author' => 'acquisition_account_num_author',
						'invoiced' => 'acquisition_account_invoiced_filter',
						'request_status' => 'acquisition_account_request_status',
						'num_pricing_system' => 'acquisition_account_num_pricing_system',
						'event_date' => 'acquisition_account_event_date',
						'date' => 'acquisition_account_date',
				)
		);
		$this->available_filters['custom_fields'] = array();
	}
	
	/**
	 * Initialisation des filtres de recherche
	 */
	public function init_filters($filters=array()) {
		$id_entity = entites::getSessionBibliId();
		$query = exercices::listByEntite($id_entity);
		$result = pmb_mysql_query($query);
		$id_exercice = 0;
		if($result && pmb_mysql_num_rows($result)) {
			$id_exercice = pmb_mysql_result($result, 0, 'id_exercice');
		}
		$this->filters = array(
				'entity' => $id_entity,
				'exercice' => $id_exercice,
				'request_type' => '',
				'type' => '',
				'num_publisher' => '',
				'num_supplier' => '',
				'num_author' => '',
				'num_pricing_system' => '',
				'web' => '',
				'date_start' => '',
				'date_end' => '',
				'event_date_start' => '',
				'event_date_end' => '',
				'invoiced' => '',
				'request_status' => 0
		);
		parent::init_filters($filters);
	}
	
	protected function init_default_selected_filters() {
		$this->add_selected_filter('entity');
		$this->add_selected_filter('exercice');
		$this->add_selected_filter('type');
		$this->add_selected_filter('num_publisher');
		$this->add_selected_filter('num_supplier');
		$this->add_selected_filter('num_author');
		$this->add_selected_filter('invoiced');
		$this->add_selected_filter('request_status');
		$this->add_selected_filter('num_pricing_system');
		$this->add_selected_filter('event_date');
	}
	
	protected function get_button_add() {
	    global $msg;
	    
	    return "<input class='bouton' type='button' value='".$msg['acquisition_new_account']."' onClick=\"document.location='".static::get_controller_url_base()."&action=edit&id=0';\" />";
	}
	
	/**
	 * Initialisation des colonnes disponibles
	 */
	protected function init_available_columns() {
		$this->available_columns =
		array('main_fields' =>
				array(
						'id' => 'acquisition_account_id',
						'num_user' => 'acquisition_account_num_user',
						'request_type_name' => 'acquisition_account_request_type_name',
						'type_name' => 'acquisition_account_type_name',
						'date' => 'acquisition_account_date',
						'title' => 'acquisition_account_title',
						'num_publisher' => 'acquisition_account_num_publisher',
						'num_supplier' => 'acquisition_account_num_supplier',
						'num_author' => 'acquisition_account_num_author',
						'event_date' => 'acquisition_account_event_date',
						'request_status' => 'acquisition_account_request_status',
						'state_icon' => 'acquisition_account_state_icon',
						'receipt_limit_date' => 'acquisition_account_receipt_limit_date',
						'receipt_effective_date' => 'acquisition_account_receipt_effective_date',
						'return_date' => 'acquisition_account_return_date'
				)
		);
		$this->available_columns['custom_fields'] = array();
	}
	
	protected function init_default_columns() {
		$this->add_column_selection();
		$this->add_column('id');
		$this->add_column('num_user');
		$this->add_column('request_type_name');
		$this->add_column('type_name');
		$this->add_column('date');
		$this->add_column('title');
		$this->add_column('num_publisher');
		$this->add_column('num_supplier');
		$this->add_column('num_author');
		$this->add_column('event_date');
		$this->add_column('request_status');
		$this->add_column('state_icon');
	}
	
	/**
	 * Initialisation des settings par d�faut
	 */
	protected function init_default_settings() {
		parent::init_default_settings();
		$this->set_setting_column('event_date', 'align', 'center');
		$this->set_setting_column('receipt_limit_date', 'align', 'center');
		$this->set_setting_column('receipt_effective_date', 'align', 'center');
		$this->set_setting_column('return_date', 'align', 'center');
	}
	
	/**
	 * Filtres provenant du formulaire
	 */
	public function set_filters_from_form() {
		$this->set_filter_from_form('request_type');
		$this->set_filter_from_form('num_pricing_system', 'integer');
		$this->set_filter_from_form('web');
		$this->set_filter_from_form('event_date_start');
		$this->set_filter_from_form('event_date_end');
		$this->set_filter_from_form('invoiced', 'integer');
		$this->set_filter_from_form('request_status', 'integer');
		parent::set_filters_from_form();
	}
	
	protected function get_search_filter_event_date() {
		return $this->get_search_filter_interval_date('event_date');
	}
	
	protected function get_search_filter_invoiced() {
		global $msg;
		
		return '<select name="'.$this->objects_type.'_invoiced">
			<option value="0" '.($this->filters['invoiced'] == 0 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_type_select_all'].'</option>
			<option value="1" '.($this->filters['invoiced'] == 1 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_not_invoiced'].'</option>
			<option value="2" '.($this->filters['invoiced'] == 2 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_invoiced'].'</option>
		</select>';
	}
	
	protected function get_search_filter_request_status() {
		global $msg;
		
		return '<select name="'.$this->objects_type.'_request_status">
			<option value="0" '.($this->filters['request_status'] == 0 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_type_select_all'].'</option>
			<option value="1" '.($this->filters['request_status'] == 1 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_request_status_not_ordered'].'</option>
			<option value="2" '.($this->filters['request_status'] == 2 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_request_status_ordered'].'</option>
			<option value="3" '.($this->filters['request_status'] == 3 ?  "selected='selected'" : "").'>'.$msg['acquisition_account_request_status_account'].'</option>
		</select>';
	}
	
	/**
	 * Filtre SQL
	 */
	protected function _get_query_filters() {
		
		$filter_query = '';
		
		$this->set_filters_from_form();
		
		$filters = array();
		$filters[] = 'account_num_exercice = "'.$this->filters['exercice'].'"';
		
		if($this->filters['request_type']) {
			$filters [] = 'account_request_type = "'.addslashes($this->filters['request_type']).'"';
		}
		if($this->filters['type']) {
			$filters [] = 'account_type = "'.addslashes($this->filters['type']).'"';
		}
		if($this->filters['num_publisher']) {
			$filters [] = 'account_num_publisher = "'.$this->filters['num_publisher'].'"';
		}
		if($this->filters['num_supplier']) {
			$filters [] = 'account_num_supplier = "'.$this->filters['num_supplier'].'"';
		}
		if($this->filters['num_author']) {
			$filters [] = 'account_num_author = "'.$this->filters['num_author'].'"';
		}
		if($this->filters['num_pricing_system']) {
			$filters [] = 'account_num_pricing_system = "'.$this->filters['num_pricing_system'].'"';
		}
		if($this->filters['web']) {
			$filters [] = 'account_web = "'.addslashes($this->filters['web']).'"';
		}
		if($this->filters['date_start']) {
			$filters [] = 'account_date >= "'.$this->filters['date_start'].'"';
		}
		if($this->filters['date_end']) {
			$filters [] = 'account_date <= "'.$this->filters['date_end'].' 23:59:59"';
		}
		if($this->filters['event_date_start']) {
			$filters [] = 'account_event_date >= "'.$this->filters['event_date_start'].'"';
		}
		if($this->filters['event_date_end']) {
			$filters [] = 'account_event_date <= "'.$this->filters['event_date_end'].' 23:59:59"';
		}
		if($this->filters['invoiced']==1) {
			$filters [] = 'id_account not in(select account_invoice_num_account from rent_accounts_invoices)';
		}elseif($this->filters['invoiced']==2) {
			$filters [] = 'id_account in(select account_invoice_num_account from rent_accounts_invoices)';
		}
		if($this->filters['request_status']) {
			$filters [] = 'account_request_status = "'.$this->filters['request_status'].'"';
		}
		if(count($filters)) {
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
		if($this->applied_sort[0]['by']) {
			$sort_by = $this->applied_sort[0]['by'];
			switch($sort_by) {
				case 'num_user' :
					return strcmp($a->get_user()->prenom.' '.$a->get_user()->nom, $b->get_user()->prenom.' '.$b->get_user()->nom);
					break;
				case 'num_publisher' :
					return strcmp($a->get_publisher()->display, $b->get_publisher()->display);
					break;
				case 'num_supplier' :
					return strcmp($a->get_supplier()->raison_sociale, $b->get_supplier()->raison_sociale);
					break;
				case 'num_author' :
					return strcmp($a->get_author()->display, $b->get_author()->display);
					break;
				case 'id' :
					return $this->intcmp($a->get_id(), $b->get_id());
					break;
				default :
					return strcmp($a->{'get_'.$sort_by}(), $b->{'get_'.$sort_by}());
					break;
			}
		}
		
	}
	
	protected function get_cell_content($object, $property) {
		$content = '';
		switch($property) {
			case 'num_publisher':
				if(isset($object->get_publisher()->display)) {
					$content .= $object->get_publisher()->display;
				}
				break;
			case 'num_supplier':
				if(isset($object->get_supplier()->raison_sociale)) {
					$content .= $object->get_supplier()->raison_sociale;
				}
				break;
			case 'num_author':
				if(isset($object->get_author()->display)) {
					$content .= $object->get_author()->display;
				}
				break;
			case 'event_date':
				$content .= $object->get_formatted_event_date();
				break;
			case 'request_status':
				$content .= $object->get_request_status_label();
				break;
			case 'state_icon':
				$content .= $object->get_state_invoice();
				break;
			default :
				$content .= parent::get_cell_content($object, $property);
				break;
		}
		return $content;
	}
	
	protected function _get_query_human_event_date() {
		return $this->_get_query_human_interval_date('event_date');
	}
	
	protected function _get_query_human_invoiced() {
		global $msg;
		if($this->filters['invoiced']==2) {
			return $msg['acquisition_account_invoiced'];
		}elseif($this->filters['invoiced']==1) {
			return $msg['acquisition_account_not_invoiced'];
		}
		return '';
	}
	
	protected function _get_query_human_request_status() {
		global $msg;
		if($this->filters['request_status']) {
			switch ($this->filters['request_status']) {
				case 1 :
					return $msg['acquisition_account_request_status_not_ordered'];
				case 2 :
					return $msg['acquisition_account_request_status_ordered'];
				case 3 :
					return $msg['acquisition_account_request_status_account'];
			}
			return '';
		}
	}
	
	protected function _get_query_human() {
		global $msg, $charset;
		
		$humans = $this->_get_query_human_main_fields();
		
		if($this->filters['request_type']) {
			$account_request_types = new marc_list('rent_request_type');
			$humans[] = "<b>".htmlentities($msg['acquisition_account_request_type_name'], ENT_QUOTES, $charset)."</b> ".$account_request_types->table[$this->filters['request_type']];
		}
		if($this->filters['web']) {
			$humans[] = "<b>".htmlentities($msg['acquisition_account_web'], ENT_QUOTES, $charset)."</b> ".$msg['acquisition_account_web_yes'];
		}
		return $this->get_display_query_human($humans);
	}
	
	protected function get_display_cell($object, $property) {
		global $id_bibli;
		
		$attributes = array();
		if($object->is_editable()) {
			$attributes['onclick'] = "window.location=\"".static::get_controller_url_base()."&action=edit&id_bibli=".$id_bibli."&id=".$object->get_id()."\"";
		}
		$content = $this->get_cell_content($object, $property);
		$display = $this->get_display_format_cell($content, $property, $attributes);
		return $display;
	}
	
	protected function get_selection_actions() {
		global $msg;
		
		if(!isset($this->selection_actions)) {
			$this->selection_actions = array();
			
			$invoices_link = array(
					'href' => static::get_controller_url_base()."&action=create_from_accounts"
			);
			$this->selection_actions[] = $this->get_selection_action('gen_invoices', $msg['acquisition_account_gen_invoices'], '', $invoices_link);
		}
		return $this->selection_actions;
	}
	
	protected function get_selection_mode() {
		return 'button';
	}
	
	protected function get_name_selected_objects() {
	    return "accounts";
	}
}