<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: bannette_tpl.inc.php,v 1.2.6.3 2021/03/11 09:13:38 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

require_once($class_path."/bannette_tpl.class.php");
require_once($class_path."/configuration/configuration_tpl_controller.class.php");

configuration_tpl_controller::set_model_class_name('bannette_tpl');
configuration_tpl_controller::set_list_ui_class_name('list_configuration_tpl_bannette_ui');
configuration_tpl_controller::proceed($id);