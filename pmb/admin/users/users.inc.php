<?php

// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: users.inc.php,v 1.7.2.2 2021/02/08 11:00:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

require_once('./admin/users/users_func.inc.php');
require_once($class_path.'/user.class.php');
require_once ($class_path . "/event/events/event_user.class.php");

print $admin_user_javascript;

switch($action) {
	case 'pwd':
        
        //Evenement publi�
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "before_pwd");
        $event->set_user_id($id);
        $evt_handler->send($event);
        
		include("./admin/users/user_pwd.inc.php");
        
        //Evenement publi�
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "after_pwd");
        $event->set_user_id($id);
        $evt_handler->send($event);
		break;
	case 'modif':
        
        //Evenement publi�
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "before_modif");
        $event->set_user_id($id);
        $evt_handler->send($event);
        
		include("./admin/users/user_modif.inc.php");
		break;
	case 'update':
        
        //Evenement publi�
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "before_update");
        $event->set_user_id($id);
        $evt_handler->send($event);
        
		include("./admin/users/user_update.inc.php");
        
        //Evenement publi�
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "after_update");
        $event->set_user_id($id);
        $evt_handler->send($event);
        
		break;
	case 'add':
        
        //Evenement publi�
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "before_add");
        $event->set_user_id($id);
        if( isset($synchro_step) ) {
            $event->set_synchro_step($synchro_step);
        }
        $evt_handler->send($event);
        
		echo window_title($database_window_title.$msg[347].$msg[1003].$msg[1001]);
		$user = new user();
		print $user->get_user_form();
		echo form_focus('userform', 'form_login');
		break;
	case 'del':
        
        //Evenement publi�
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "before_del");
        $event->set_user_id($id);
        $evt_handler->send($event);
        
        if($id && $id !=1) {
        	user::delete($id, $_COOKIE["PhpMyBibli-LOGIN"]);
        }
        show_users();
        
        //Evenement publi�
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "after_del");
        $event->set_user_id($id);
        $evt_handler->send($event);
        
		break;
	case 'duplicate':
        
        //Evenement publi�
        $evt_handler = events_handler::get_instance();
        $event = new event_user("user", "before_duplicate");
        $event->set_user_id($id);
        $event->set_user_id($id);
        $evt_handler->send($event);
        
		include("./admin/users/user_duplicate.inc.php");
		break;
	default:
		show_users();
		break;
}
