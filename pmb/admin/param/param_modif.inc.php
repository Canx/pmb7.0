<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: param_modif.inc.php,v 1.10.8.1 2020/10/31 13:24:23 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $id_param;
$id_param = intval($id_param);
$requete = "SELECT * FROM parametres WHERE id_param='$id_param' and gestion=0 ";
$res = pmb_mysql_query($requete);
$nbr = pmb_mysql_num_rows($res);

if($nbr) {
	$params=pmb_mysql_fetch_object($res);
	param_form(	$params->id_param,
				$params->type_param,
				$params->sstype_param,
				$params->valeur_param,
				$params->comment_param	);
}
