<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: check_session_time.inc.php,v 1.13.6.1 2021/03/17 14:56:55 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

$time_expired=0;
if (!$opac_duration_session_auth) {
	$opac_duration_session_auth=600;
}

//Si on n'est pas en construction du portail
if (!$_SESSION["cms_build_activate"]) {
	//Lecteur enregistr� ?
	if ($_SESSION["user_code"]) {
		if (((time()-$_SESSION["connect_time"])>$opac_duration_session_auth)&&($opac_duration_session_auth!=-1)) {
			unset($_SESSION["user_code"]);
			session_destroy();
			logout();
			$time_expired=1;
		} else {
			$_SESSION["connect_time"]=time();
		}
	} else {
		//Session anonyme
		if (
			(isset($_SESSION["connect_time"]) && $_SESSION["connect_time"]) && 
			(isset($_SERVER['HTTP_REFERER']) && substr($_SERVER['HTTP_REFERER'],0,strlen($opac_url_base))==$opac_url_base) && //on v�rifie que le referer contient l'opac
			((substr($opac_url_base,0,strlen($pmb_url_base))!=$pmb_url_base)?(substr($_SERVER['HTTP_REFERER'],0,strlen($pmb_url_base))!=$pmb_url_base):1) //Si l'opac contient la gestion, on v�ridie que le referer ne contient pas la gestion
			) {
			if (((time()-$_SESSION["connect_time"])>$opac_duration_session_auth)&&($opac_duration_session_auth!=-1)) {
			    session_destroy();
			    logout();
				$time_expired=2;
			} else {
				$_SESSION["connect_time"]=time();
			}
		} else {
			$_SESSION["connect_time"]=time();
		}
	}
}