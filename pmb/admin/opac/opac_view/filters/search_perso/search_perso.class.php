<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: search_perso.class.php,v 1.8.2.1 2021/04/02 13:44:52 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $base_path;
require_once($base_path."/admin/opac/opac_view/filters/opac_view_filters.class.php");

class search_perso extends opac_view_filters {
	
	protected function _init_path() {
		$this->path="search_perso";
	}
    
    public function fetch_data() {
		parent::fetch_data();
						
		$myQuery = pmb_mysql_query("SELECT * FROM search_persopac order by search_name ");
		$this->liste_item=array();
		$i=0;
		if(pmb_mysql_num_rows($myQuery)){
			while(($r=pmb_mysql_fetch_object($myQuery))) {
				$this->liste_item[$i]=new stdClass();
				$this->liste_item[$i]->limitsearch=$r->search_limitsearch;
				$this->liste_item[$i]->id=$r->search_id;
				$this->liste_item[$i]->name=$r->search_name;
				$this->liste_item[$i]->shortname=$r->search_shortname;
				$this->liste_item[$i]->query=$r->search_query;
				$this->liste_item[$i]->human=$r->search_human;
				$this->liste_item[$i]->directlink=$r->search_directlink;	
				$this->liste_item[$i]->limitsearch=$r->search_limitsearch;	
				if(in_array($r->search_id,$this->selected_list))	$this->liste_item[$i]->selected=1;
				else $this->liste_item[$i]->selected=0;				
				$i++;			
			}	
		}
		return true;
 	}		
	
	public function get_form(){
		global $msg;
		global $tpl_liste_item_tableau,$tpl_liste_item_tableau_ligne;
		
		global $class_path,$base_path,$include_path;

		require_once($base_path."/admin/opac/opac_view/filters/search_perso/search_perso.tpl.php");
		
		// liste des lien de recherche directe
		$liste="";
		// pour toute les recherche de l'utilisateur
		$liste_id = array();
		
		for($i=0;$i<count($this->liste_item);$i++) {
			$liste_id[] = 'search_perso_selected_'.$this->liste_item[$i]->id;
			if ($i % 2) $pair_impair = "even"; else $pair_impair = "odd";			
	        $td_javascript=" ";
	        $tr_surbrillance = "onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='".$pair_impair."'\" ";
	
	        $line = str_replace('!!td_javascript!!',$td_javascript , $tpl_liste_item_tableau_ligne);
	        $line = str_replace('!!tr_surbrillance!!',$tr_surbrillance , $line);
	        $line = str_replace('!!pair_impair!!',$pair_impair , $line);
	
			$line =str_replace('!!id!!', $this->liste_item[$i]->id, $line);
			if($this->liste_item[$i]->selected) $checked="checked";else $checked="";			
			$line =str_replace('!!selected!!', $checked, $line);
			$line = str_replace('!!name!!', $this->liste_item[$i]->name, $line);
			$line = str_replace('!!human!!', $this->liste_item[$i]->human, $line);		
			$line = str_replace('!!shortname!!', $this->liste_item[$i]->shortname, $line);
			if($this->liste_item[$i]->directlink)
				$directlink="<img src='".get_url_icon('tick.gif')."' border='0'  hspace='0' class='align_middle'  class='bouton-nav' value='=' />";
			else $directlink="";
			$line = str_replace('!!directlink!!', $directlink, $line);
			
			$liste.=$line;
		}
		$tpl_liste_item_tableau = str_replace('!!lignes_tableau!!',$liste , $tpl_liste_item_tableau);
		
		if (count($liste_id)) {
			$tpl_liste_item_tableau .= "<input type='button' class='bouton_small align_middle' value='".$msg['tout_cocher_checkbox']."' onclick='check_checkbox(\"".implode("|",$liste_id)."\",1);'>";
			$tpl_liste_item_tableau .= "<input type='button' class='bouton_small align_middle' value='".$msg['tout_decocher_checkbox']."' onclick='check_checkbox(\"".implode("|",$liste_id)."\",0);'>";
		}
		
		return $tpl_liste_item_tableau;
	}	
	
	public function save_form(){
		$req="delete FROM opac_filters where opac_filter_view_num=".$this->id_vue." and  opac_filter_path='".$this->path."' ";
		pmb_mysql_query($req);
		
		$param=array();
		$selected_list=array();
		for($i=0;$i<count($this->liste_item);$i++) {
			eval("global \$search_perso_selected_".$this->liste_item[$i]->id.";
			\$selected= \$search_perso_selected_".$this->liste_item[$i]->id.";");
			if($selected){
				$selected_list[]=$this->liste_item[$i]->id;
			}
		}
		$param["selected"]=$selected_list;
		$param=addslashes(serialize($param));		
		$req="insert into opac_filters set opac_filter_view_num=".$this->id_vue." ,  opac_filter_path='".$this->path."', opac_filter_param='$param' ";
		pmb_mysql_query($req);
		
		//Sauvegarde dans les recherches pr�d�finies
		$req = "select search_id, search_opac_views_num from search_persopac";
		$res = pmb_mysql_query($req);
		if ($res) {
		    while($row = pmb_mysql_fetch_object($res)) {
		        $views_num = array();
		        //la recherche pr�d�finie est s�lectionn�e..
		        if (in_array($row->search_id,$selected_list)) {
		            if ($row->search_opac_views_num != "") {
		                $views_num = explode(",", $row->search_opac_views_num);
		                if (count($views_num)) {
		                    if (!in_array($this->id_vue, $views_num)) {
		                        $views_num[] = $this->id_vue;
		                        $requete = "update search_persopac set search_opac_views_num='".implode(",", $views_num)."' where search_id=".$row->search_id;
		                        pmb_mysql_query($requete);
		                    }
		                }
		            }
		        } else {
		            if ($row->search_opac_views_num != "") {
		                $views_num = explode(",", $row->search_opac_views_num);
		                if (count($views_num)) {
		                    $key_exists = array_search($this->id_vue, $views_num);
		                    if ($key_exists !== false) {
		                        //la recherche pr�d�finie ne doit plus �tre affich�e dans la vue
		                        array_splice($views_num,$key_exists,1);
		                        $requete = "update search_persopac set search_opac_views_num='".implode(",", $views_num)."' where search_id=".$row->search_id;
		                        pmb_mysql_query($requete);
		                    }
		                }
		            } else {
		                //la recherche pr�d�finie doit �tre affich�e dans les autres vues sauf celle-ci..
		                $requete = "select opac_view_id from opac_views where opac_view_id <> ".$this->id_vue;
		                $resultat = pmb_mysql_query($requete);
		                $views_num[] = 0; // OPAC classique
		                while ($view = pmb_mysql_fetch_object($resultat)) {
		                    $views_num[] = $view->opac_view_id;
		                }
		                $requete = "update search_persopac set search_opac_views_num='".implode(",", $views_num)."' where search_id=".$row->search_id;
		                pmb_mysql_query($requete);
		            }
		        }
		    }
		}
	}	
	
}