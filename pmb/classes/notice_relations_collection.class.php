<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: notice_relations_collection.class.php,v 1.2.8.1 2020/11/04 10:46:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/notice_relations.class.php");

class notice_relations_collection {
	
	static private $notice_relations = array();
	
	/**
	 * 
	 * @param number $notice_id
	 * @return notice_relations:
	 */
	static public function get_object_instance($notice_id=0) {
		$notice_id += 0;
		if (!isset(self::$notice_relations[$notice_id])) {
			self::$notice_relations[$notice_id] = new notice_relations($notice_id);
		}
		return self::$notice_relations[$notice_id];
	}
}