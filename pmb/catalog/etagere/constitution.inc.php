<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: constitution.inc.php,v 1.12.2.1 2020/10/21 07:40:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $class_path, $idetagere, $id;

require_once($class_path."/etageres/constitution_controller.class.php");

constitution_controller::proceed((!empty($idetagere) ? $idetagere : $id));