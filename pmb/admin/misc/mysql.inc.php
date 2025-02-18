<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: mysql.inc.php,v 1.19.4.1 2021/03/24 08:36:59 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".inc.php")) die("no access");

global $info, $database_window_title, $msg, $action, $tabindexref, $pmb_set_time_limit;

switch ($info) {
	case 'phpinfo':
		echo window_title($database_window_title."Php - Info");
		echo phpinfo();
		break;
	case 'table_index':
		echo window_title($database_window_title."Tables - indexes");
		require_once ("./tables/dataref.inc.php");
		$pb="";
		foreach ($tabindexref as $table=>$key_names) {
			$rqti="show index from $table";
			$resi=pmb_mysql_query($rqti) or die(pmb_mysql_error()."<br />".$rqti);
			$cles_reelles = array();
			for ($i=0;$i<pmb_mysql_num_rows($resi);$i++) {
				$key_name=pmb_mysql_result($resi,$i,'Key_name');
				$col_name=pmb_mysql_result($resi,$i,'Column_name');
				$cles_reelles[$key_name][]=$col_name;
			}
			foreach ($key_names as $key_name=>$col_names) {
				if ($cles_reelles[$key_name]) {
					for($j=0;$j<count($col_names);$j++) {
						if (array_search($col_names[$j],$cles_reelles[$key_name])===false) {
							$pb .= "<br />--------- $table $key_name ".$col_names[$j]." missing";
						}
					}
				} else {
					$pb .= "<br />-- $table $key_name missing";
				} 
			}	
		}
		
		if ($pb) echo "<b>".$msg['admin_info_table_index_pb']."</b><br />".$pb;
		else echo $msg['admin_info_table_index_ok'];
		break;
	/*case 'verif_base':
		echo window_title($database_window_title.$msg[verification_verif_base]);
		require_once("$base_path/admin/misc/verifications/verif_base.inc.php");
		break;*/
	case 'mysqlinfo':
		echo window_title($database_window_title."MySQL - Info");
		
		echo "<div class='row'><div class='row'><label class='etiquette'>".$msg['sql_info_notices']."</label></div>
			  <div class='row'>".pmb_sql_value("select count(*) as nb from notices")."</div>" ;

		echo "<div class='row'><label class='etiquette'>".$msg['sql_info_exemplaires']."</label></div>
			  <div class='row'>".pmb_sql_value("select count(*) as nb from exemplaires")."</div>" ;

		echo "<div class='row'><label class='etiquette'>".$msg['sql_info_bulletins']."</label></div>
			  <div class='row'>".pmb_sql_value("select count(*) as nb from bulletins")."</div>" ;

		echo "<div class='row'><label class='etiquette'>".$msg['sql_info_authors']."</label></div>
			  <div class='row'>".pmb_sql_value("select count(*) as nb from authors")."</div>" ;

		echo "<div class='row'><label class='etiquette'>".$msg['sql_info_publishers']."</label></div>
			  <div class='row'>".pmb_sql_value("select count(*) as nb from publishers")."</div>" ;

		echo "<div class='row'><label class='etiquette'>".$msg['sql_info_empr']."</label></div>
			  <div class='row'>".pmb_sql_value("select count(*) as nb from empr")."</div>" ;

		echo "<div class='row'><label class='etiquette'>".$msg['sql_info_pret']."</label></div>
			  <div class='row'>".pmb_sql_value("select count(*) as nb from pret")."</div>" ;

		echo "<div class='row'><label class='etiquette'>".$msg['sql_info_pret_archive']."</label></div>
			  <div class='row'>".pmb_sql_value("select count(*) as nb from pret_archive")."</div>" ;

		echo "<hr />" ;

		echo "<div class='row'>
				<label class='etiquette' >MySQL Database name, host and user</label>
				</div>
			  <div class='row'>
					".DATA_BASE." on ".SQL_SERVER.", user=".USER_NAME."
					</div>
			  <div class='row'>
				<label class='etiquette' >MySQL Server Information</label>
				</div>
			  <div class='row'>
					".pmb_mysql_get_server_info()."
					</div><hr />" ;

		echo "<div class='row'>
				<label class='etiquette' >MySQL Client Information</label>
				</div>
			  <div class='row'>
					".pmb_mysql_get_client_info()."
					</div><hr />" ;

		echo "<div class='row'>
				<label class='etiquette' >MySQL Host Information</label>
				</div>
			  <div class='row'>
					".pmb_mysql_get_host_info()."
					</div><hr />" ;

		echo "<div class='row'>
				<label class='etiquette' >MySQL Protocol Information</label>
				</div>
			  <div class='row'>
					".pmb_mysql_get_proto_info()."
					</div><hr />" ;

		echo "<div class='row'>
				<label class='etiquette' >MySQL Stat. Information</label>
				</div>
			  <div class='row'>
					".str_replace('  ','<br />',pmb_mysql_stat())."</div><hr />";
		
		echo "<div class='row'>
				<label class='etiquette' >MySQL Variables</label>
				</div>
			  <div class='row'><table>" ;
		$result = pmb_mysql_query('SHOW VARIABLES');
		$parity=0 ;
		while ($row = pmb_mysql_fetch_assoc($result)) {
			if ($parity % 2) $pair_impair = "even";
				else $pair_impair = "odd";
			$parity+=1;
			echo "<tr class='$pair_impair'><td>".$row['Variable_name']."</td><td>".$row['Value']."</td></tr>";
			}
		echo "</table></div>" ;
		break;
	case '':
	default:
		print "<div class='row'>
			<a href='./admin.php?categ=misc&sub=mysql&action=CHECK'>$msg[719]</a>
				</div>
			<div class='row'>
				<a href='./admin.php?categ=misc&sub=mysql&action=ANALYZE'>$msg[720]</a>
				</div>
			<div class='row'>
				<a href='./admin.php?categ=misc&sub=mysql&action=REPAIR'>$msg[721]</a>
				</div>
			<div class='row'>
				<a href='./admin.php?categ=misc&sub=mysql&action=OPTIMIZE'>$msg[722]</a>
				</div>
			<div class='row'>
				<a href='./admin.php?categ=misc&sub=mysql&info=mysqlinfo'>$msg[admin_info_mysql]</a>
				</div>
			<div class='row'>
				<a href='./admin.php?categ=misc&sub=mysql&info=phpinfo'>$msg[admin_info_php]</a>
				</div>
			<div class='row'>
				<a href='./admin.php?categ=misc&sub=mysql&info=table_index'>$msg[admin_info_table_index]</a>
				</div>";
			/*<div class='row'>
				<a href='./admin.php?categ=misc&sub=mysql&info=verif_base'>$msg[admin_info_verif_base]</a>
				</div>*/
			break;
	}

if($action) {

	@set_time_limit($pmb_set_time_limit);
	$db = DATA_BASE;
	$tables = pmb_mysql_list_tables($db);
	$num_tables = @pmb_mysql_num_rows($tables);

	$i = 0;
	while($i < $num_tables) {
		$table[$i] = pmb_mysql_tablename($tables, $i);
		$i++;
	}

	echo "<table >";
	foreach ($table as $cle => $valeur) {
		$requete = $action." TABLE ".$valeur." ";
		$res = pmb_mysql_query($requete);
		$nbr_lignes = @pmb_mysql_num_rows($res);
		$nbr_champs = @pmb_mysql_num_fields($res);

		if($nbr_lignes) {
			if(!$cle) {
				for($i=0; $i < $nbr_champs; $i++) {
					printf("<th>%s</th>", pmb_mysql_field_name($res, $i));
				}
			}

			for($i=0; $i < $nbr_lignes; $i++) {
				$row = pmb_mysql_fetch_row($res);
				echo "<tr>";
				foreach($row as $col) {
					if(!$col) $col="&nbsp;";
						print "<td>$col</td>";
					}
					echo "</tr>";
			}
		}

	}
	echo "</table>";

}
