<?php
// +-------------------------------------------------+
// � 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_watcheslist_datasource_watches.class.php,v 1.5.10.1 2019/11/08 08:01:16 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_watcheslist_datasource_watches extends cms_module_common_datasource_list{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->sortable = true;
		$this->limitable = true;
	}
	
	/*
	 * On d�fini les s�lecteurs utilisable pour cette source de donn�e
	*/
	public function get_available_selectors(){
		return array(
				"cms_module_watcheslist_selector_watches_generic"
		);
	}

	/*
	 * On d�fini les crit�res de tri utilisable pour cette source de donn�e
	*/
	protected function get_sort_criterias() {
		return array (
				"watch_last_date",
				"id_watch",
				"watch_title"
		);
	}
	
	/*
	 * R�cup�ration des donn�es de la source...
	 */
	public function get_datas(){
		global $dbh;
		//on commence par r�cup�rer l'identifiant retourn� par le s�lecteur...
		$selector = $this->get_selected_selector();
		if($selector){
			$return = array();
			if (count($selector->get_value()) > 0) {
				foreach ($selector->get_value() as $value) {
					$return[] = $value*1;
				}
			}
			
			if(count($return)){
				$watcheslist = array();
				$query = "select id_watch from docwatch_watches where id_watch in ('".implode("','",$return)."')";
				if ($this->parameters["sort_by"] != "") {
					$query .= " order by ".addslashes($this->parameters["sort_by"]);
					if ($this->parameters["sort_order"] != "") $query .= " ".addslashes($this->parameters["sort_order"]);
				}
				$result = pmb_mysql_query($query,$dbh);
				if ($result) {
					if (pmb_mysql_num_rows($result)) {
						while($row=pmb_mysql_fetch_object($result)){
							$docwatch_watch = new docwatch_watch($row->id_watch);
							$docwatch_watch->fetch_items();
							$watcheslist[] = $docwatch_watch->get_normalized_watch();
						}
					}
				}
				if ($this->parameters["nb_max_elements"] > 0) $watcheslist = array_slice($watcheslist, 0, $this->parameters["nb_max_elements"]);
				return array('watches' => $watcheslist);
			}
		}
		return false;
	}
	
	public function get_format_data_structure(){

		$datasource_watch = new cms_module_watch_datasource_watch();
		return array(
				array(
					'var' => "watches",
					'desc' => $this->msg['cms_module_watcheslist_view_watches_desc'],
					'children' => $this->prefix_var_tree($datasource_watch->get_format_data_structure(),"watches[i]")
				)
		);
	}
}