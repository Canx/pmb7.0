<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_entity_concepts_datasource_authors.class.php,v 1.3 2017/06/02 09:52:43 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class frbr_entity_concepts_datasource_authors extends frbr_entity_common_datasource {
	
	public function __construct($id=0){
		$this->entity_type = 'authors';
		parent::__construct($id);
	}
	
	/*
	 * R�cup�ration des donn�es de la source...
	 */
	public function get_datas($datas=array()){
		$query = "select distinct index_concept.num_object as id, index_concept.num_concept as parent FROM index_concept
			WHERE index_concept.type_object = 2 AND index_concept.num_concept IN (".implode(',', $datas).")";
		$datas = $this->get_datas_from_query($query);
		$datas = parent::get_datas($datas);
		return $datas;
	}
}