<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: section.inc.php,v 1.25.4.8 2021/01/21 08:56:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

require_once($class_path."/docs_section.class.php");
require_once($class_path."/configuration/configuration_controller.class.php");

configuration_controller::set_model_class_name('docs_section');
configuration_controller::set_list_ui_class_name('list_configuration_docs_section_ui');
configuration_controller::proceed($id);