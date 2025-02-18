<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: lists_controller.class.php,v 1.9.6.10 2021/03/03 10:15:35 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class lists_controller {
	
	/**
	 * Nom de la classe mod�le � d�river
	 * @var string
	 */
	protected static $model_class_name = '';
	
	/**
	 * Nom de la classe list_ui � d�river
	 * @var string
	 */
	protected static $list_ui_class_name = '';
	
	/**
	 * URL base du controleur
	 * @var string
	 */
	protected static $url_base = '';
	
	protected static function get_model_instance($id) {
		return new static::$model_class_name($id);
	}
	
	protected static function get_list_ui_instance($filters=array(), $pager=array(), $applied_sort=array()) {
		return new static::$list_ui_class_name($filters, $pager, $applied_sort);
	}
	
	public static function proceed($id=0) {
		global $msg;
		global $action;
		global $dest;
		
		$id = intval($id);
		switch ($action) {
			case 'edit':
				$model_instance = static::get_model_instance($id);
				print $model_instance->get_form();
				break;
			case 'save':
				$model_instance = static::get_model_instance($id);
				$model_instance->set_properties_from_form();
				$model_instance->save();
				
				//TODO : Appliquer une redirection mais attendant
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'delete':
				$model_class_name = static::$model_class_name;
				$model_class_name::delete($id);
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'list_delete':
				$list_ui_class_name = static::$list_ui_class_name;
				$list_ui_class_name::delete();
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_display_list();
				break;
			case 'dataset_edit':
				$list_ui_class_name = static::$list_ui_class_name;
				$list_ui_instance = static::get_list_ui_instance();
				print $list_ui_instance->get_dataset_form($id);
				break;
			case 'dataset_save':
				$list_ui_class_name = static::$list_ui_class_name;
				$list_ui_instance = static::get_list_ui_instance();
				$list_model = new list_model($id);
				$list_model->set_objects_type($list_ui_instance->get_objects_type());
				$list_model->set_list_ui($list_ui_instance);
				$list_model->set_properties_from_form();
				$list_model->save();
				if(!$id) { //Cr�ation
					$list_ui_instance->add_dataset($list_model->get_id());
				}
				$list_ui_instance->apply_dataset($list_model->get_id());
				print $list_ui_instance->get_display_list();
				break;
			case 'dataset_apply':
				$list_ui_class_name = static::$list_ui_class_name;
				$list_ui_instance = static::get_list_ui_instance();
				$list_ui_instance->apply_dataset($id);
				print $list_ui_instance->get_display_list();
				break;
			case 'dataset_delete':
				list_model::delete($id);
				break;
			default:
				$list_ui_instance = static::get_list_ui_instance();
				switch($dest) {
					case "TABLEAU":
						$list_ui_instance->get_display_spreadsheet_list();
						break;
					case "TABLEAUHTML":
						print $list_ui_instance->get_display_html_list();
						break;
					default:
						print $list_ui_instance->get_display_list();
						break;
				}
		}
	}
	
	public static function redirect_display_list() {
		if(headers_sent()) {
			print "
				<script type='text/javascript'>
					window.location.href='".static::get_url_base()."';
				</script>";
		} else {
			header('Location: '.static::get_url_base());
		}
	}
	
	public static function proceed_ajax($object_type, $directory='') {
		global $class_path;
		global $filters, $pager, $sort_by, $sort_asc_desc, $ancre;
		
		if(isset($object_type) && $object_type) {
			$class_name = 'list_'.$object_type;
			if($directory) {
				static::load_class('/list/'.$directory.'/'.$class_name.'.class.php');
			} else {
				static::load_class('/list/'.$class_name.'.class.php');
			}
			$filters = (!empty($filters) ? encoding_normalize::json_decode(stripslashes($filters), true) : array());
			$pager = (!empty($pager) ? encoding_normalize::json_decode(stripslashes($pager), true) : array());
			$instance_class_name = new $class_name($filters, $pager, array('by' => $sort_by, 'asc_desc' => (!empty($sort_asc_desc) ? $sort_asc_desc : '')));
			$instance_class_name->set_ancre($ancre);
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_header_list());
			print encoding_normalize::utf8_normalize($instance_class_name->get_display_content_list());
		}
	}
	
	public static function proceed_manage_ajax($id=0, $objects_type, $directory='') {
		global $sub, $action;
		global $class_path;
		global $filters, $pager, $sort_by, $sort_asc_desc;
		global $filter_property, $filter_label;
		
		$id = intval($id);
		if(isset($objects_type) && $objects_type) {
			switch($sub) {
				case 'options':
					switch ($action) {
						case 'get_applied_group_selector':
							$class_name = 'list_'.$objects_type;
							if($directory) {
								static::load_class('/list/'.$directory.'/'.$class_name.'.class.php');
							} else {
								static::load_class('/list/'.$class_name.'.class.php');
							}
							$instance_class_name = new $class_name();
							print encoding_normalize::utf8_normalize($instance_class_name->get_display_add_applied_group($id));
							break;
						case 'get_search_filter_selector':
							$class_name = 'list_'.$objects_type;
							if($directory) {
								static::load_class('/list/'.$directory.'/'.$class_name.'.class.php');
							} else {
								static::load_class('/list/'.$class_name.'.class.php');
							}
							$instance_class_name = new $class_name();
							$instance_class_name->add_selected_filter($filter_property, $filter_label);
							if($instance_class_name->is_custom_field_filter($filter_property)) {
								$filter_form = $instance_class_name->get_search_filter_custom_field_form($filter_property, $filter_label, true);
							} else {
								$filter_form = $instance_class_name->get_search_filter_form($filter_property, $filter_label, true);
							}
							print encoding_normalize::utf8_normalize($filter_form);
							break;
						case 'get_search_order_selector':
						    $class_name = 'list_'.$objects_type;
						    if($directory) {
						        static::load_class('/list/'.$directory.'/'.$class_name.'.class.php');
						    } else {
						        static::load_class('/list/'.$class_name.'.class.php');
						    }
						    $instance_class_name = new $class_name();
						    print encoding_normalize::utf8_normalize($instance_class_name->get_search_order_add_applied_sort($id));
						    break;
						case 'filter_delete':
							list_ui::unset_property_values_in_session($objects_type, 'filter', $filter_property);
							break;
					}
					break;
				default:
					switch($action) {
						case 'save':
							$list_model = new list_model($id);
							$list_model->set_objects_type($objects_type);
							$list_model->set_properties_from_form();
							$list_model->save();
							break;
						case 'edit':
								
							break;
						case 'delete':
							list_model::delete($id);
							break;
					}
					break;
			}
		}
	}
	
	protected static function load_class($file){
		global $base_path;
		global $class_path;
		global $include_path;
		global $javascript_path;
		global $styles_path;
		global $msg,$charset;
		global $current_module;
		 
		if(file_exists($class_path.$file)){
			require_once($class_path.$file);
		}else{
			return false;
		}
		return true;
	}
	
	public static function set_list_ui_class_name($list_ui_class_name) {
		static::$list_ui_class_name = $list_ui_class_name;
	}
	
	public static function set_model_class_name($model_class_name) {
		static::$model_class_name = $model_class_name;
	}
	
	public static function get_url_base() {
		return static::$url_base;
	}
	
	public static function set_url_base($url_base) {
		static::$url_base = $url_base;
	}
}