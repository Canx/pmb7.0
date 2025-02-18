<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: indexint.inc.php,v 1.19.6.2 2020/12/08 10:25:03 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $id_pclass, $class_path, $msg, $id, $id_pclass, $deflt_pclassement;

if(!isset($id_pclass) && empty($id) && !empty($deflt_pclassement)) {
	$id_pclass = intval($deflt_pclassement);
}

require_once($class_path."/entities/entities_indexint_controller.class.php");

print '<h1>'.$msg[140].'&nbsp;: '.$msg['indexint_menu_title'].'</h1>';

$entities_indexint_controller = new entities_indexint_controller($id);
$entities_indexint_controller->set_id_pclass($id_pclass);
$entities_indexint_controller->set_url_base('autorites.php?categ=indexint');
$entities_indexint_controller->proceed();
