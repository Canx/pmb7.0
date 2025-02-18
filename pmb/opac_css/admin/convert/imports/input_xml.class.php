<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: input_xml.class.php,v 1.1 2018/07/25 06:19:18 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($base_path."/admin/convert/convert_input.class.php");

class input_xml extends convert_input {

	public function _get_n_notices_($fi,$file_in,$input_params,$origine) {
		//pmb_mysql_query("delete from import_marc");
		$index=array();
		$i=false;
		$encoding="";
		$n=1;
		$fcontents="";
		while ($i===false) {
			$i=strpos($fcontents,"<".$input_params['NOTICEELEMENT'].">");
			if ($i===false) $i=strpos($fcontents,"<".$input_params['NOTICEELEMENT']." ");
			if ($i!==false) {
				//on pense � r�cup le charset du xml
				if($encoding === ""){
					$s=strpos($fcontents,"<?xml");
					$e=strpos($fcontents,"?>");
					if(isset($s) && isset($e)){
						$entete = substr($fcontents,$s,$e+2);
						$rx = "/<?xml.*encoding=[\'\"](.*?)[\'\"].*?>/m";
						if (preg_match($rx,$entete, $m)) $encoding = strtoupper($m[1]);
					}
				} 
				$i1=strpos($fcontents,"</".$input_params['NOTICEELEMENT'].">");
				while ((!feof($fi))&&($i1===false)) {
					$fcontents.=fread($fi,4096);
					$i1=strpos($fcontents,"</".$input_params['NOTICEELEMENT'].">");
				}
				if ($i1!==false) {
					$notice=substr($fcontents,$i,$i1+strlen("</".$input_params['NOTICEELEMENT'].">")-$i);
					$requete="insert into import_marc (no_notice, notice, origine, encoding) values($n,'".addslashes($notice)."','$origine','$encoding')";
					pmb_mysql_query($requete);
					$n++;
					$index[]=$n;
					$fcontents=substr($fcontents,$i1+strlen("</".$input_params['NOTICEELEMENT'].">"));
					$i=false;
				}
			} else {
				if (!feof($fi))
					$fcontents.=fread($fi,4096);
				else break;
			}
		}
	
		return $index;
	}
}

?>