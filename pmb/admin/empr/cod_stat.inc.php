<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cod_stat.inc.php,v 1.13.6.5 2021/01/21 08:56:06 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $id;

require_once($class_path."/empr_codestat.class.php");
require_once($class_path."/configuration/configuration_controller.class.php");

configuration_controller::set_model_class_name('empr_codestat');
configuration_controller::set_list_ui_class_name('list_configuration_empr_codstat_ui');
configuration_controller::proceed($id);
