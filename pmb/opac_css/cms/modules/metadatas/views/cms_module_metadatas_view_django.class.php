<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_metadatas_view_django.class.php,v 1.5.6.2 2021/02/12 11:00:54 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_metadatas_view_django extends cms_module_common_view_django{
	
		
	public function get_form(){
		return "";
	}
	
	
	public function get_headers($datas=array()){

		$headers=array(
			'add' => array(),
			'replace' => array()
		);
		if (is_array($datas) && count($datas)) {
			foreach ($datas as $group) {
				if (isset($group["metadatas"]) && is_array($group["metadatas"])) {
					if (isset($group["group_template"])) {
						foreach ($group["metadatas"] as $key=>$value) {
							if ($value != "") {
								$html = str_replace("{{key_metadata}}", $key, $group["group_template"]);
								$html = str_replace("{{value_metadata}}", strip_tags($value), $html);
								if($group['replace']){
									$headers['replace'][] = $html;
								}else{
									$headers['add'][] = $html;
								}
							}
						}
					}
				}
			}
		}
		return $headers;
	}
	
	
	public function render($datas){
		return "";
	}

	
	public function get_manage_form(){
		return "";
	}
}
