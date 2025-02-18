<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mailtpl.class.php,v 1.8.6.8 2021/02/02 12:35:32 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/templates/mailtpl.tpl.php");
require_once($class_path."/files_gestion.class.php");

class mailtpl {
	public $id=0;
	public $info=array();
	public $users=array();
	public static $error="";
	
	public function __construct($id=0) {
		$this->id=intval($id);
		$this->fetch_data();
	}
	
	public function fetch_data() {
		global $PMBuserid;
		
		$this->info=array(
				'id' => $this->id,
				'name' => '',
				'objet' => '',
				'tpl' => '',
				'users' => array()
		);
		$this->users=array();
		$requete_users = "SELECT userid, username FROM users order by username ";
		$res_users = pmb_mysql_query($requete_users);
		$this->all_users=array();
		while (list($this->all_userid,$all_username)=pmb_mysql_fetch_row($res_users)) {
			$this->all_users[]=array($this->all_userid,$all_username);
		}	
		if(!$this->id){
			$this->users[]=$PMBuserid;
			return;
		} 
		$req="select * from mailtpl where id_mailtpl=". $this->id;
		
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);		
			$this->info['id']= $r->id_mailtpl;	
			$this->info['name']= $r->mailtpl_name;	
			$this->info['objet']= $r->mailtpl_objet;	
			$this->info['tpl']= $r->mailtpl_tpl;	
			$this->info['users']= $r->mailtpl_users;	
		}			
		$this->users= explode(" ",$this->info['users']);
		 				
	// printr($this->info[28]);
	}

    public function get_mailtpl(){
		global $charset;
    	$ajax_send=$this->info;
    	if($charset != 'utf-8'){ // cause: json_encode veut de l'utf8
    		$ajax_send['id'] =utf8_encode($this->info['id']);
    		$ajax_send['name']=utf8_encode($this->info['name']);
    		$ajax_send['objet'] =utf8_encode($this->info['objet']);
    		$ajax_send['tpl'] =utf8_encode($this->info['tpl']);
    		$ajax_send['users'] =utf8_encode($this->info['users']);
    	}    	
    	return($ajax_send);
    }
	
    public static function get_selvars() {
    	global $mailtpl_form_selvars, $msg, $charset, $class_path;
    	
    	$selvars = [
    	    'empr_group_empr' => [
    	        'empr_name',
    	        'empr_first_name',
    	        'empr_sexe',
    	        'empr_cb',
    	        'empr_login',
    	        'empr_mail',
    	        'empr_loans',
    	        'empr_loans_late',
    	        'empr_resas',
    	        'empr_resa_confirme',
    	        'empr_resa_not_confirme',
    	        'empr_name_and_adress',
    	        'empr_dated',
    	        'empr_datef',
    	        'empr_nb_days_before_expiration',
    	        'empr_all_information',
    	        'empr_auth_opac',
    	        'empr_auth_opac_subscribe_link',
				'empr_last_loan_date',
    	    	'empr_auth_opac_change_password_link',
    	    ],
    	    'empr_group_loc' => [
    	        'empr_loc_name',
    	        'empr_loc_adr1',
    	        'empr_loc_adr2',
    	        'empr_loc_cp',
    	        'empr_loc_town',
    	        'empr_loc_phone',
    	        'empr_loc_email',
    	        'empr_loc_website',
    	    ],
    	    'empr_group_misc' => [
    	        'empr_day_date',
    	    ]
    	];
    	
    	$options_selvars = '';
    	foreach ($selvars as $optgroup => $options) {
    	    $options_selvars .= "<optgroup label='" . htmlentities($msg["selvars_$optgroup"], ENT_QUOTES, $charset) . "'>";
    	    foreach ($options as $option) {
    	        $options_selvars .= "<option value='!!$option!!'>" . htmlentities($msg["selvars_$option"], ENT_QUOTES, $charset) . "</option>";
    	    }
    	    $options_selvars .= "</optgroup>";
    	}
    	
    	require_once($class_path.'/event/events/event_mailing.class.php');
    	$event = new event_mailing('mailing', 'get_selvars');
    	$evth = events_handler::get_instance();
    	$evth->send($event);
	    $additionnal_selvars = $event->get_selvars();
	    if (!empty($additionnal_selvars)) {
    	    foreach ($additionnal_selvars as $libelle_optgroup => $options) {
    	        $options_selvars .= "<optgroup label='" . htmlentities($libelle_optgroup, ENT_QUOTES, $charset) . "'>";
        	    foreach ($options as $option => $libelle_option) {
        	        $options_selvars .= "<option value='!!$option!!'>" . htmlentities($libelle_option, ENT_QUOTES, $charset) . "</option>";
        	    }
        	    $options_selvars .= "</optgroup>";
    	    }
    	}
    	
    	$mailtpl_form_selvars = str_replace('!!options_selvars!!', $options_selvars, $mailtpl_form_selvars);
    	
		return $mailtpl_form_selvars;   
    }  
    
    public static function get_resavars(){
    	global $mailtpl_form_resavars;
    	return $mailtpl_form_resavars;
    }
 	
    public static function get_sel_img(){
    	global $mailtpl_form_sel_img, $pmb_img_folder;
    	if(!$pmb_img_folder) return '';
    	$tpl=$mailtpl_form_sel_img;
		$img=new files_gestion('img');	
		if(!$img->get_count_file()) return '';
		
    	$select=$img->get_sel('select_file',"!!path!!!!name!!","!!name!!");
		$tpl=str_replace('!!select_file!!',$select,$tpl);  	
		return $tpl;   
    }   
       
	public function get_form() {
		global $mailtpl_content_form,$msg,$charset;
		global $pdflettreresa_resa_prolong_email;
		
		$content_form = $mailtpl_content_form;
		$content_form = str_replace('!!id!!', $this->id, $content_form);
		
		$interface_form = new interface_admin_form('mailtpl');
		if(!$this->id){
			$interface_form->set_label($msg['admin_mailtpl_form_add']);
		}else{
			$interface_form->set_label($msg['admin_mailtpl_form_edit']);
		}
		$autorisations_users="";
		$id_check_list="";
		foreach($this->all_users as $a_user) {
			$id_check="auto_".$a_user[0];
			if($a_user[0]==1){
				$checked=" checked readonly ";
			}else{
				if(in_array( $a_user[0],$this->users)){
					$checked=" checked ";
				}else $checked="";
				if($id_check_list)$id_check_list.='|';
				$id_check_list.=$id_check;
			}
			$autorisations_users.="<span class='usercheckbox'><input type='checkbox' $checked name='userautorisation[]' id='$id_check' value='".$a_user[0]."' class='checkbox'><label for='$id_check' class='normlabel'>&nbsp;".$a_user[1]."</label></span>&nbsp;&nbsp;";
		}
		$content_form=str_replace('!!name!!',htmlentities($this->info['name'], ENT_QUOTES, $charset),$content_form);
		$content_form=str_replace('!!selvars!!',mailtpl::get_selvars(),$content_form);
		
		if($pdflettreresa_resa_prolong_email){
			$content_form=str_replace('!!resavars!!',mailtpl::get_resavars(),$content_form);
		}
		
		$sel_img_tpl="";
		$sel_img=mailtpl::get_sel_img();
		if($sel_img)$sel_img_tpl="
		<div class='row'>
			<label class='etiquette'>".$msg["admin_mailtpl_form_sel_img"]."</label>
			<div class='row'>
				$sel_img
			</div>
		</div>";
		$content_form=str_replace('!!sel_img!!',$sel_img_tpl,$content_form);
		$content_form=str_replace('!!autorisations_users!!',$autorisations_users,$content_form);
		$content_form=str_replace('!!id_check_list!!',$id_check_list,$content_form);
		$content_form=str_replace('!!tpl!!',htmlentities($this->info['tpl'], ENT_QUOTES, $charset),$content_form);
		$content_form=str_replace('!!objet!!',htmlentities($this->info['objet'],ENT_QUOTES,$charset),$content_form);
		$content_form=str_replace('!!id_mailtpl!!',$this->id,$content_form);
		$interface_form->set_object_id($this->id)
		->set_duplicable(true)
		->set_confirm_delete_msg($msg['confirm_suppr_de']." ".$this->info['name']." ?")
		->set_content_form($content_form)
		->set_table_name('mailtpl')
		->set_field_focus('name');
		return $interface_form->get_display();
	}

	public function set_properties_from_form() {
		global $name, $f_objet_mail, $f_message, $userautorisation;
		
		$this->info['name']=stripslashes($name);
		$this->info['objet']=stripslashes($f_objet_mail);
		$this->info['tpl']=stripslashes($f_message);
		$this->info['users']=$userautorisation;
	}
	
	public function save() {
		$fields="
			mailtpl_name='".addslashes($this->info['name'])."',
			mailtpl_objet='".addslashes($this->info['objet'])."',
			mailtpl_tpl='".addslashes($this->info['tpl'])."',
			mailtpl_users=' ".implode(" ",$this->info['users'])." ' 
		";
		
		if(!$this->id){ // Ajout
			$req="INSERT INTO mailtpl SET $fields ";	
			pmb_mysql_query($req);
			$this->id = pmb_mysql_insert_id();
		} else {
			$req="UPDATE mailtpl SET $fields where id_mailtpl=".$this->id;	
			pmb_mysql_query($req);				
		}	
	}	
	
	public static function delete($id) {
		$id = intval($id);
		if($id) {
			$req="DELETE from mailtpl WHERE id_mailtpl=".$id;
			pmb_mysql_query($req);
		}
		return true;
	}	
	
	public static function get_attachments_form() {
	    global $mailtpl_attachments_form_tpl;
	    
	    $form = $mailtpl_attachments_form_tpl;
	    return $form;
	}
	
	public static function upload_attachments_form($path, $MAX_FILESIZE=0x500000) {
		global $msg;
		
		$statut=false;
		static::$error="";
		if(count($_FILES['pieces_jointes_mailing']['name'])) {
			for($i=0; $i<count($_FILES['pieces_jointes_mailing']['name']); $i++) {
				$name = $_FILES['pieces_jointes_mailing']['name'][$i];
				$tmp_name = $_FILES['pieces_jointes_mailing']['tmp_name'][$i];
				if (! is_uploaded_file($tmp_name)){
					static::$error=$msg["admin_files_gestion_error_not_write"].$name;
					return $statut;
				}
				
				if ($_FILES['pieces_jointes_mailing'][$i]['size'] >= $MAX_FILESIZE){
					static::$error=$msg["admin_files_gestion_error_to_big"].$name;
					return $statut;
				}
				//		"/^\.(jpg|jpeg|gif|png|doc|docx|txt|rtf|pdf|xls|xlsx|ppt|pptx){1}$/i";
				$no_valid_extension="/^\.(php|PHP){1}$/i";
				if(preg_match($no_valid_extension, strrchr($name, '.'))){
					static::$error=$msg["admin_files_gestion_error_not_valid"].$name;
					return $statut;
				}
				// tout semble ok on le d�place au bon endroit
				$statut=move_uploaded_file($tmp_name,$path.$name);
				if($statut==false) {
					static::$error=$msg["admin_files_gestion_error_not_loaded"].$name;
				}
				
				chmod($path.$name, 0777);
			}
		}
		return $statut;
	}
	    
} //mailtpl class end





class mailtpls {	
	public $info=array();
	
	public function __construct() {
		$this->fetch_data();
	}
	
	public function fetch_data() {
		global $PMBuserid;
		$this->info=array();
		$i=0;
		$req="SELECT * FROM mailtpl WHERE mailtpl_users LIKE '% $PMBuserid %' ORDER BY mailtpl_name";
		$resultat=pmb_mysql_query($req);	
		if (pmb_mysql_num_rows($resultat)) {
			while($r=pmb_mysql_fetch_object($resultat)){	
				$this->info[$i]= new mailtpl($r->id_mailtpl);
				$i++;
			}
		}
	}
		
	public function get_count_tpl() {
		return count($this->info);
	}
	
	public function get_sel($sel_name,$sel_id=0) {
		$tpl="<select name='$sel_name' id='$sel_name'>";				
		foreach($this->info as $elt){
			if($elt->info['id']==$sel_id){
				$tpl.="<option value=".$elt->info['id']." selected='selected'>".$elt->info['name']."</option>";
			} else {
				$tpl.="<option value=".$elt->info['id'].">".$elt->info['name']."</option>";
			}
		}
		$tpl.="</select>";
		return $tpl;
	}
} // mailtpls class end
