<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: sort.tpl.php,v 1.19.6.5 2021/02/09 10:58:35 qvarin Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $show_tris_form, $msg, $ligne_tableau_tris, $ligne_tableau_tris_etagere, $ligne_tableau_tris_rss_flux, $show_sel_form;
global $ligne_tableau_external_tris, $popup_ligne_tableau_tris;
global $popup, $module;

if (empty($module)) {
    $module = "catalog";
}

// les templates pour l'�cran d'affichage de la liste des tris
$show_tris_form="<body class='$module'>
    <div id='contenu-frame'>
		<script>
			function agitTri(actionTri,idTri) {				
				document.sort_form.action_tri.value = actionTri;
				document.sort_form.id_tri.value = idTri;
				document.sort_form.submit();
			}
			function suppr(idTri) {
				if (confirm('" . $msg['tri_confirm_supp'] . "')) {
					agitTri('supp',idTri);
				}
			}
		</script>
		<table style='width:100%'>
            <tr>
                <td class='align_left'>&nbsp;</td>
                <td>
                    <div class='right'>
                        <a href='#' onClick=\"parent.document.getElementById('history').style.display='none'; return false;\">
                            <img src='".get_url_icon('close.gif')."' border='0' class='center'>
                        </a>
                    </div>
                </td>
            </tr>
        </table>
		<form name='sort_form' method='post' action='sort.php'>
		   <table style='width:100%; height:100%'>
				<tr>
					<td colspan=2 style='vertical-align:top'>
					<table>
						<th colspan=3>" . $msg['tris_dispos'] . "</th>
						!!liste_tris!!
					</table>
					</td>
				</tr>
				<tr>
					<td style='width:10%'>
						&nbsp;
					</td>
					<td style='vertical-align:top'><SPAN class='right'>
						<input type='button' class='bouton' name='".$msg['tri_inactif']."' value='".$msg['tri_inactif']."' onClick=\"!!callback!!\">
						<input type='button' class='bouton' name='".$msg['definir_tri']."' value='".$msg['definir_tri']."' onClick=\"agitTri('modif','');\">
					</SPAN></td>
				</tr>
			</table>
			<input type='hidden' name='action_tri' value=''>
			<input type='hidden' name='caller' value='!!caller!!'>
			<input type='hidden' name='id_tri' value=''>
			<input type='hidden' name='type_tri' value='!!sortname!!'>
			<input type='hidden' name='module' value='$module'>
		</form>
	</div>
</body>
";
$show_popup_tris_form="<body class='$module'>
    <div id='contenu-frame'>
		<form name='sort_form' method='post' action='sort.php'>
		   <table style='width:100%; height:100%'>
				<tr>
					<td colspan=2 style='vertical-align:top'>
					<table>
						<th colspan=3>" . $msg['tris_dispos'] . "</th>
						!!liste_tris!!
					</table>
					</td>
				</tr>
				<tr>
					<td style='width:10%'>
						&nbsp;
					</td>
					<td style='vertical-align:top'><SPAN class='right'>
						<input type='button' class='bouton sort' name='".$msg['tri_inactif']."' value='".$msg['tri_inactif']."' data-sort_link='!!callback!!'>
						<input type='button' class='bouton' name='".$msg['definir_tri']."' value='".$msg['definir_tri']."' onClick=\"agitTri('modif','');\">
					</SPAN></td>
				</tr>
			</table>
			<input type='hidden' name='action_tri' value=''>
			<input type='hidden' name='caller' value='!!caller!!'>
			<input type='hidden' name='id_tri' value=''>
			<input type='hidden' name='type_tri' value='!!sortname!!'>
			<input type='hidden' name='popup' value='1'>
			<input type='hidden' name='module' value='$module'>
		</form>
	</div>
</body>
";

$popup_ligne_tableau_tris = "
<tr class='!!pair_impair!!' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='!!pair_impair!!'\" style='cursor: pointer'>
	<td class='sort' style='width:90%' alt='".$msg['appliq_tri']."' title='".$msg['appliq_tri']."' 
        data-sort_link='./recall.php?current=" . (isset($_SESSION["CURRENT"]) ? $_SESSION["CURRENT"] : '') . "&t=!!t!!&tri=!!id_tri!!&reference=!!reference!!&type_tri=!!type_tri!!&ajax=1&module=$module'>
		!!nom_tri!!
	</td>
	<td style='width:5%'>
		<a href='#' onClick='agitTri(\"modif\",!!id_tri!!);'>
		  <img src='".get_url_icon('b_edit.png')."' alt='" . $msg['modif_tri'] . "' title='" . $msg['modif_tri'] . "' border=0>
        </a>
	</td>
	<td style='width:5%'>
		<a href='#' onClick='suppr(!!id_tri!!);'>
            <img src='".get_url_icon('cross.png')."' alt='" . $msg['suppr_tri'] . "' title='" . $msg['suppr_tri'] . "'>
        </a>
	</td>
</tr>";

$ligne_tableau_tris = "
<tr class='!!pair_impair!!' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='!!pair_impair!!'\" style='cursor: pointer'>
	<td style='width:90%' alt='".$msg['appliq_tri']."' title='".$msg['appliq_tri']."' onClick='parent.document.getElementById(\"history\").style.display=\"none\";parent.location.href=\"./recall.php?current=" . (isset($_SESSION["CURRENT"]) ? $_SESSION["CURRENT"] : '') . "&t=!!t!!&tri=!!id_tri!!&reference=!!reference!!&type_tri=!!type_tri!!&module=$module\"; return false;'>
			!!nom_tri!!
	</td>
	<td style='width:5%'>
			<a href='#' onClick='agitTri(\"modif\",!!id_tri!!);'>
			<img src='".get_url_icon('b_edit.png')."' alt='" . $msg['modif_tri'] . "' title='" . $msg['modif_tri'] . "' border=0></a>
	</td>
	<td style='width:5%'>
			<a href='#' onClick='suppr(!!id_tri!!);'><img src='".get_url_icon('cross.png')."' alt='" . $msg['suppr_tri'] . "' title='" . $msg['suppr_tri'] . "'></a>
	</td>
</tr>
";
$ligne_tableau_external_tris = "
<tr class='!!pair_impair!!' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='!!pair_impair!!'\" style='cursor: pointer'>
	<td style='width:90%' alt='".$msg['appliq_tri']."' title='".$msg['appliq_tri']."' onClick='parent.document.getElementById(\"history\").style.display=\"none\";parent.location.href=\"./recall.php?current=" . (isset($_SESSION["CURRENT"]) ? $_SESSION["CURRENT"] : '') . "&t=!!t!!&tri=!!id_tri!!&external=1&reference=!!reference!!&type_tri=!!type_tri!!&module=$module\"; return false;'>
			!!nom_tri!!
	</td>
	<td style='width:5%'>
			<a href='#' onClick='agitTri(\"modif\",!!id_tri!!);'>
			<img src='".get_url_icon('b_edit.png')."' alt='" . $msg['modif_tri'] . "' title='" . $msg['modif_tri'] . "' border=0></a>
	</td>
	<td style='width:5%'>
			<a href='#' onClick='suppr(!!id_tri!!);'><img src='".get_url_icon('cross.png')."' alt='" . $msg['suppr_tri'] . "' title='" . $msg['suppr_tri'] . "'></a>
	</td>
</tr>
";
$ligne_tableau_tris_etagere = $ligne_tableau_tris_rss_flux = "
	<tr class='!!pair_impair!!' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='!!pair_impair!!'\" style='cursor: pointer'>
		<td style='width:90%' alt='".$msg['appliq_tri']."' title='".$msg['appliq_tri']."' onClick=\"parent.document.getElementById('history').style.display='none';parent.window.getSort('!!id_tri!!','!!descname_tri!!'); return false;\">
				!!nom_tri!!
		</td>
		<td style='width:5%'>
				<a href='#' onClick='agitTri(\"modif\",!!id_tri!!);'>
				<img src='".get_url_icon('b_edit.png')."' alt='" . $msg['modif_tri'] . "' title='" . $msg['modif_tri'] . "' border=0></a>
		</td>
		<td style='width:5%'>
				<a href='#' onClick='suppr(!!id_tri!!);'><img src='".get_url_icon('cross.png')."' alt='" . $msg['suppr_tri'] . "' title='" . $msg['suppr_tri'] . "'></a>
		</td>
	</tr>
";




//le template pour la modification d'un tri
$close_btn = "<td class='align_right'><a href='#' onClick=\"parent.document.getElementById('history').src='./sort.php?action=0';parent.document.getElementById('history').style.display='none'; return false;\"><img src='".get_url_icon('close.gif')."' border='0' class='center'></a></td>"; 
$show_sel_form=" <body class='$module' onLoad='document.forms[\"sort_form\"].elements[\"nom_tri\"].focus();'>
    <div id='contenu-frame'>
		<script>
			function left_to_right() {
		 		var order;
				var temp;
				left=document.sort_form.liste_critere;
				right=document.sort_form.elements['liste_sel[]'];
				for (i=0; i<left.length; i++) {
					if (left.options[i].selected) {
						temp=left.options[i].value;
						temp=temp.substr(2,temp.lastIndexOf('_')-2);
								
						switch (temp) {
							case 'text': 
								order='A-Z ';
							break;
							case 'num':
								order='0-9 ';
							break;
						}
						new_option=new Option(order+left.options[i].text,left.options[i].value);
						right.options[right.length]=new_option;
						left.options[i]=null;
						i=i-1;
					}
				}
			}
			function right_to_left() {
				left=document.sort_form.liste_critere;
				right=document.sort_form.elements['liste_sel[]'];
				for (i=0; i<right.length; i++) {
					if (right.options[i].selected) {
						new_option=new Option(right.options[i].text.substr(4),right.options[i].value);
						left.options[left.length]=new_option;
						right.options[i]=null;
						i=i-1;
					}
				}
			}
			function swap_type_tri(type) {
				var valeur;
				valeur=document.sort_form.ordre.getAttribute('num_text');
				if (valeur=='A - Z') {
					valeur='0 - 9';
				} else {
					valeur='A - Z';
				}
				document.sort_form.ordre.setAttribute('num_text',valeur);
				right=document.sort_form.elements['liste_sel[]'];
				for (i=0; i<right.length; i++) {
					if (right.options[i].selected) {
						temp=right.options[i].value;
						switch (document.sort_form.ordre.getAttribute('num_text'))
						{
							case 'A - Z':
							if (temp.substr(0,1)=='c') {
								order='A-Z';
							} else {
								order='Z-A';
							}
							type='text';
							break;
							case '0 - 9':
							if (temp.substr(0,1)=='c') {
								order='0-9';
							} else {
								order='9-0';
							}
							type='num';
							break;
						}
						right.options[i].value=temp.substr(0,1)+'_'+type+'_'+temp.substring(temp.lastIndexOf('_')+1);
						right.options[i].text=order.toUpperCase()+' '+right.options[i].text.substr(4);		
					}
				}
				
			}
			function change_order(order_value) {
		 		var order;
				var temp;
				var type;		
				right=document.sort_form.elements['liste_sel[]'];
				for (i=0; i<right.length; i++) {
					if (right.options[i].selected) {
						temp=right.options[i].value;
						if (temp.substr(0,1)!=order_value) {
							switch (temp.substr(2,temp.lastIndexOf('_')-2)) {
							case 'num':
								document.sort_form.ordre.setAttribute('num_text','0 - 9');
							break;
							case 'text':
								document.sort_form.ordre.setAttribute('num_text','A - Z');
							break;
							}
							switch (document.sort_form.ordre.getAttribute('num_text'))
							{
							case 'A - Z':
								if (order_value=='c') {
									order='A-Z';
								} else {
									order='Z-A';
								}
								type='text';
							break;
							case '0 - 9':
								if (order_value=='c') {
									order='0-9';
								} else {
									order='9-0';
								}
								type='num';
							break;
						}
						right.options[i].value=order_value+'_'+type+'_'+temp.substring(temp.lastIndexOf('_')+1);
						right.options[i].text=order.toUpperCase()+' '+right.options[i].text.substr(4);
						}		
					}
				}
			}
			function move_up() {
				right=document.sort_form.elements['liste_sel[]'];
				for (i=0; i<right.length; i++) {
					if (right.options[i].selected) {
						if (i>0) {
							swap_i=new Option(right.options[i].text,right.options[i].value);
							swap_i_1=new Option(right.options[i-1].text,right.options[i-1].value);
							right.options[i]=swap_i_1;
							right.options[i-1]=swap_i;
							right.options[i-1].selected=true;
						}
					}
				}
			}
			function move_down() {
				right=document.sort_form.elements['liste_sel[]'];
				for (i=right.length-1; i>=0; i--) {
					if (right.options[i].selected) {
						if (i<right.length-1) {
							swap_i=new Option(right.options[i].text,right.options[i].value);
							swap_i_1=new Option(right.options[i+1].text,right.options[i+1].value);
							right.options[i]=swap_i_1;
							right.options[i+1]=swap_i;
							right.options[i+1].selected=true;
						}
					}
				}
			}
			function sauvegarder() {
				if (document.forms[\"sort_form\"].elements[\"nom_tri\"].value!=\"\") {    
					right=document.sort_form.elements['liste_sel[]'];
					for (i=right.length-1; i>=0; i--) {
						right.options[i].selected=true;
					}
					document.sort_form.action_tri.value = \"enreg\";
                    ".(!empty($popup) && $popup ? "" : "document.forms[\"sort_form\"].submit();")."
				} else {
					alert (\"".$msg['erreur_nom_tri']."\");
					document.forms[\"sort_form\"].elements[\"nom_tri\"].focus();
				}
			}
	</script>
	<table style='width:100%'><tr><td class='align_left'><h3>".$msg['definir_tri']."</h3></td>".(!empty($popup) && $popup ? "" : $close_btn)."</tr></table>
		<form name='sort_form' method='post' action='sort.php'>
		   <table style='width:100%; height:100%'>
				<tr>
					<td colspan=3>".$msg['nom_tri']."&nbsp;&nbsp;<input type='text' name='nom_tri' value='!!nom_tri!!'><input type=hidden name='id_tri' value='!!id_tri!!'>
					</td>
				</tr>
				<tr>
					<td style='width:40%'>
					".$msg['criteres_tri_dispos']."
					</td>
					<td style='width:20%'>
					&nbsp;
					</td>
					<td style='width:40%'>
					".$msg['criteres_tri_retenus']."
					</td>
				</tr>
				<tr>
					<td style='vertical-align:top; width:40%; height:100%'>
						<select name='liste_critere' multiple='yes' style='width:100%;height:400px' onDblClick='left_to_right()'>
							!!liste_criteres!!
						</seclect>
					</td>
					<td style='text-align:center; width:20%'>
					<table style='height:100%'><tr><td style='text-align:center'><input type='button' value='&gt;&gt;' onClick='left_to_right()'></td></tr><tr><td style='text-align:center'><input type='button' value='&lt;&lt;' onClick='right_to_left()''></td></tr></table>.
					</td>
					<td style='vertical-align:top' style='text-align:center; width:40%; height:100%'>
						<select name='liste_sel[]' multiple='yes' style='width:100%;height:400px' onDblClick='right_to_left()'>
							!!liste_selectionnes!!
						</select>
					</td>		
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan=2>
                        <span class='right'>
					       <input type='image' src='".get_url_icon('arrow_up.png')."' alt='".$msg['monter']."' title='".$msg['monter']."' value='".$msg['monter']."' onClick='move_up(); return false;'>
                            &nbsp;<input type='image' src='".get_url_icon('arrow_down.png')."' alt='".$msg['descendre']."' title='".$msg['descendre']."' value='".$msg['descendre']."' onClick='move_down(); return false;'>
                            &nbsp;&nbsp;
                            <input type='image' alt='".$msg['tri_croissant']."' title='".$msg['tri_croissant']."' value='".$msg['tri_croissant']."' src='".get_url_icon('fleche_diago_haut.png')."' onClick=\"change_order('c'); return false;\">
                            &nbsp;<input type='image' alt='".$msg['tri_decroissant']."' title='".$msg['tri_decroissant']."' value='".$msg['tri_decroissant']."' src='images/fleche_diago_bas.png' onClick=\"change_order('d'); return false;\">
                            &nbsp;<input name='ordre' type='button' class='bouton' num_text='0 - 9' onClick=\"swap_type_tri(this.getAttribute('num_text'));\" value='A - Z/0 - 9'>
                        </span>
                    </td>
				</tr>
				<tr><td colspan=3>&nbsp;</td></tr>
				<tr>
					<td colspan=3>
                        <SPAN class='right'>
                            <input type='button' class='bouton cancel' name='".$msg['76']."' value='".$msg['76']."' ".(!empty($popup) && $popup ? "" : "onClick='document.forms[\"sort_form\"].submit();'").">
                            &nbsp;&nbsp;
                            <input type='button' class='bouton save' name='".$msg['sauvegarder_tri']."' value='".$msg['sauvegarder_tri']."' onClick='sauvegarder()'>
                        </span>
					</td>
				</tr>
			</table>
            ".(!empty($popup) && $popup ? "<input type='hidden' name='popup' value='1'>" : "")."
			<input type='hidden' name='action_tri' value='affliste'>
			<input type='hidden' name='caller' value='!!caller!!'>
			<input type='hidden' name='id_tri' value='!!id_tri!!'>
			<input type='hidden' name='type_tri' value='!!sortname!!'>
			<input type='hidden' name='module' value='$module'>
		</form>
	</div>
</body>
";