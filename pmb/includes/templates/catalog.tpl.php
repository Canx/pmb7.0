<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: catalog.tpl.php,v 1.56.2.6 2021/02/09 07:30:28 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $categ, $mode, $sub, $quoi, $catalog_menu, $msg;
global $class_path, $evt_handler, $event, $catalog_layout, $current_module;
global $catalog_layout_end, $biblio_query, $saisie_cb_form, $search_bar, $type, $external_type, $action;

// Valeurs pour l'affichage de la page par defaut 
// (selection de l'onglet)
// note : l'autre solution serait de faire un menu g�n�ral (voir en admin) 
//plut�t que d'afficher un sous menu par d�faut.
if(!$categ){
	$categ="search";
	$mode=0;
} elseif($categ=="caddie" and !$sub){
	//Paniers > Gestion : selection de "Gestion des paniers par defaut"
	$sub="gestion";
	$quoi="panier";	
}

// ---------------------------------------------------------------------------
//		$catalog_menu : Menu vertical du catalogage
// ---------------------------------------------------------------------------
// ancien d�but de catalog_menu <h3>$msg[129]</h3>
require_once($class_path."/modules/module_catalog.class.php");
require_once($class_path."/list/tabs/list_tabs_ui.class.php");
require_once($class_path."/list/tabs/list_tabs_catalog_ui.class.php");
$module_catalog = module_catalog::get_instance();
$catalog_menu = $module_catalog->get_left_menu();
	
// $catalog_layout : layout page catalogage
$catalog_layout = "
<div id='conteneur' class='$current_module'>
$catalog_menu
<div id='contenu'>
<!--!!menu_contextuel!! -->
";

// $catalog_layout_end : layout page catalogue (fin)
$catalog_layout_end = '
</div></div>
';

// $biblio_query : form de recherche : semble ne plus �tre utilis�.....
$biblio_query = "
<script type='text/javascript'>
	function test_form(form)
	{
		if((form.ex_query.value.replace(/^\s+|\s+$/g, '').length == 0) && (form.ISBN_query.value.replace(/^\s+|\s+$/g, '').length == 0) && (form.title_query.value.replace(/^\s+|\s+$/g, '').length == 0) && (form.author_query.value.replace(/^\s+|\s+$/g, '').length == 0))
		{
			alert(\"${msg[348]}\");
			return false;
		}

		return true;
	}
</script>
<h1>".$msg["235"]."</h1>
<form class='form-$current_module' id='biblio_query' name='biblio_query' method='post' action='./catalog.php?categ=search' onSubmit='return test_form(this)'>
<div class='form-contenu'>
<div class='row'>
	<label class='etiquette' for='ex_query'>".$msg["232"]."</label>
	</div>
<div class='row'>
	<input type='text' class='saisie-50em' name='ex_query' id='ex_query' />
	</div>
<div class='row'>
	<label class='etiquette' for='ISBN_query'>".$msg["231"]."</label>
	</div>
<div class='row'>
	<input type='text' class='saisie-50em' name='ISBN_query' id='ISBN_query' />
	</div>
<div class='row'>
	<label class='etiquette' for='title_query'>".$msg["233"]."</label>
	</div>
<div class='row'>
	<input type='text' class='saisie-50em' value='' size='36' name='title_query' id='title_query' />
	</div>
<div class='row'>
	<label class='etiquette' for='author_query'>".$msg["234"]."</label>
	</div>
<div class='row'>
	<input type='text' class='saisie-50em' value='' name='author_query' id='author_query' />
	</div>
<div class='row'>
	<span>".$msg["155"]." <a href='./help.php?whatis=regex' onclick='aide_regex();return false;'></a></span>
	</div>
</div>
<div class='row'>
	<input type='submit' class='bouton' value='".$msg["142"]."' />
	</div>
</form>
<script type='text/javascript'>	document.forms['biblio_query'].elements['ex_query'].focus();
</script>
";

//  $saisie_cb_form: form de saisie code barre
$saisie_cb_form = "
<h1>".$msg["270"]."</h1>
<form class='form-$current_module' id='saisie_cb' name='saisie_cb' method='post' action='./catalog.php?categ=create_form&id=0'>
<div class='form-contenu'>
<div class='row'>
	<label class='etiquette' for='saisieISBN'>".$msg["255"]."</label>
	</div>
<div class='row'>
	<input class='saisie-20em' type='text' id='saisieISBN' name='saisieISBN' value='' />
	</div>
</div>
<div class='row'>
	<input class='bouton' type='submit' value=' ".$msg["502"]." ' />
	</div>
</form>
<script type='text/javascript'>document.forms['saisie_cb'].elements['saisieISBN'].focus();</script>
";

//  $search_bar: code qui fait la barre de ranking en r�sultat recherche
// si vous changez la taille, il faut mettre � jour $lengtha et $lengthb
// dans classes/notice_display.class.php
$search_bar = "
<table border=\"0\" class=\"result-bar\" cellspacing=\"0\" width=\"25\">
  <tr>
    <td class=\"bar-left\"><img src=\"".get_url_icon('bar_spacer.gif')."\" width=\"!!la!!\" height=\"3\" alt=\"rank: !!indice!!%\"></td>
    <td class=\"bar-right\"><img src=\"".get_url_icon('bar_spacer.gif')."\" width=\"!!lb!!\" height=\"3\"></td>
  </tr>
</table>
";