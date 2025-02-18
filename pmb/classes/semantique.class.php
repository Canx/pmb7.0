<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: semantique.class.php,v 1.14.4.1 2020/03/24 08:00:20 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class semantique {

    public function __construct() {
    }
    
    //fonction qui r�cup�re les synonymes d'un mot
    public static function list_synonyms($mot) {
    	$t=array();
    	$rqt="select id_mot from mots where mot='".addslashes($mot)."'";
    	$execute_query=pmb_mysql_query($rqt);
    	if (pmb_mysql_num_rows($execute_query)) {
    		$r=pmb_mysql_fetch_object($execute_query);
    		$query = "SELECT distinct mots.id_mot, mots.mot, linked_mots_mutual.num_linked_mot as has_word_mutual
				FROM mots
				JOIN linked_mots ON mots.id_mot=linked_mots.num_linked_mot AND linked_mots.type_lien=1
				LEFT JOIN linked_mots AS linked_mots_mutual ON linked_mots_mutual.num_mot=linked_mots.num_linked_mot AND linked_mots_mutual.num_linked_mot=linked_mots.num_mot AND linked_mots_mutual.type_lien=1
				WHERE linked_mots.num_mot=".$r->id_mot;
    		$result = pmb_mysql_query($query);
    		
    		while ($row = pmb_mysql_fetch_object($result)) {
    			$t1=array();
    			$t1["code"]=$row->id_mot;
    			$t1["mot"]=$row->mot;
    			$t1["has_word_mutual"]=($row->has_word_mutual ? true : false);
				$t[]=$t1;	
    					
    		}
    	}
    	return $t;
    }
        
    //fonction qui r�cup�re le code php en base des mots vides du dernier calcul
    public static function add_empty_words() {
    	//ajout des mots vides calcul�s
		$rqt="select php_empty_words from empty_words_calculs where archive_calcul=1";
		$execute_query=pmb_mysql_query($rqt);
		if ($execute_query&&pmb_mysql_num_rows($execute_query)) {
			$r=pmb_mysql_fetch_object($execute_query);
			return $r->php_empty_words;
		}
    }
    
	//fonction qui calcule les mots vides par rapport � l'index globale des mots
	public static function calculate_empty_words($nb_notices_calcul=0) {
				
		//si mot pas de lien suppression du mot
		@pmb_mysql_query("delete from mots where id_mot in (select num_mot from linked_mots where linked_mots.type_lien=2 group by num_mot) and id_mot not in (select num_linked_mot from linked_mots group by num_linked_mot)");
		//vidage des mots vides calcul�s de la table linked_mots
		@pmb_mysql_query("delete from linked_mots where type_lien=2");
					
		//si le param�tre de nombre de notices pour lequel le mot est consid�r� vide est vide, on consid�re que ce nombre est la moiti� des notices 
		//si le nombre de notices pour lequel le mot est pr�sent est sup�rieur au param�tre 		
		$nb_notices_calcul += 0;
		
		if (!$nb_notices_calcul) $nb_noti="*2>nb";
			else  $nb_noti=">".$nb_notices_calcul;
		
		$rqt1 = "select word, count(id_notice) as cm,(select count(*) from notices) as nb from notices_mots_global_index join words on num_word = id_word group by num_word having cm$nb_noti";
		$execute_query1 = pmb_mysql_query($rqt1);
		//parcours de tous les mots trouv�s afin de g�n�rer le code php
		while ($r1 = pmb_mysql_fetch_object($execute_query1)) {			
			//v�rification de l'existence du mot dans la table mots
			$rqt_select = "select id_mot from mots where mot='".$r1->word."'";
			$query_select = pmb_mysql_query($rqt_select);
			if (pmb_mysql_num_rows($query_select)) {
			    $r_mot = pmb_mysql_fetch_object($query_select);
			    $id_mot = $r_mot->id_mot;
				// Verifier de l'existance en mot vide (type_lien � 3)
			    $rqt_words_created = "select mot from mots,linked_mots where mots.id_mot=linked_mots.num_mot and linked_mots.type_lien=3 and mots.id_mot=" . $id_mot;
			    $result = pmb_mysql_query($rqt_words_created);
			    if (!pmb_mysql_num_rows($result)) {
					// Le mot est aussi un synonyme, donc insertion information mot vide calcul�
					pmb_mysql_query("insert into linked_mots (num_mot,num_linked_mot, type_lien) values (".$id_mot.",0,2)");
				}	
			} else {			
				//ajout du mot
				@pmb_mysql_query("insert into mots (mot) values ('".$r1->word."')");
				$id_mot=pmb_mysql_insert_id();
				@pmb_mysql_query("insert into linked_mots (num_mot,num_linked_mot, type_lien) values (".$id_mot.",0,2)");					
			}	
		}
		
	}
	
	public static function gen_table_empty_word() {
		//ajout des mots pour remplir  $empty_word[]
		global $empty_word;
		pmb_mysql_query("delete from empty_words_calculs");	
		$temp="";
		$rqt_words_created="select mot from mots,linked_mots where id_mot=num_mot and (type_lien=3 or type_lien=2)";
		$query_words_created=pmb_mysql_query($rqt_words_created);
		while ($result_words_created=pmb_mysql_fetch_object($query_words_created)) {
						
			$mot = convert_diacrit($result_words_created->mot);
			$words = pmb_alphabetic('^a-z0-9\s\*', ' ',pmb_strtolower($mot));
			$words=explode(" ",$words);
			
			//Variable de stockage des mots restants apr�s supression des mots vides
			//Pour chaque mot
			for ($i=0; $i<count($words); $i++) {
				$words[$i]=trim($words[$i]);
				if ($words[$i]) $temp.="\$empty_word[] = \"".$words[$i]."\";";
			}
		}

		if ($temp) {
			//insertion dans la base de la date du jour, du code php et du nombre actuel de notices dans la base 
			@pmb_mysql_query("insert into empty_words_calculs (date_calcul,php_empty_words,archive_calcul) values ('".today()."','".$temp."',1)");
		}
	}
}
?>