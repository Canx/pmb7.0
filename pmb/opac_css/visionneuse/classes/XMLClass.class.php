<?php
// +-------------------------------------------------+
// � 2002-2010 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: XMLClass.class.php,v 1.5.6.1 2020/08/19 11:52:41 dgoron Exp $

class XMLClass {
	public $defaultMimetypeFile;			//xml d�crivant les classes � utilis� par d�faut
	public $defaultMimetype;				//tab r�sultant du xml par defaut	
	public $mimetypeFiles = array();		//tableau associatif des manisfest par classes d'affichage 
	public $classMimetypes = array();		//tableau associatif r�sultant des diff�rents manifest, d�crivant les mimetypes support�s par chaque classe
		
    public function __construct($file=""){
    	$this->file = $file;   	
	}
    
 	//M�thodes
 	public function defaultMimetypeParse($parser, $nom, $attributs){
		global $_starttag; $_starttag=true;
		if($nom == 'MIMETYPE' && $attributs['TYPE'] && $attributs['CLASS']){
			$this->defaultMimetype[$attributs['TYPE']] = $attributs['CLASS'];
		}
		if($nom == 'MIMETYPES'){
			$this->defaultMimetype = array();
		}
	}

	//on fait tout dans la m�thode d�butBalise....
	public function finBalise($parser, $nom){//besoin de rien
	}   
	public function texte($parser, $data){//la non plus
	}
	
	public function analyser($file=""){
 		global $charset;
		
		if($file != "") $xmlToParse = $file;
		else $xmlToParse = $this->file;
		
		if (!($fp = @fopen($xmlToParse , "r"))) {
		    die(htmlentities("impossible d'ouvrir le fichier $xmlToParse", ENT_QUOTES, $charset));
			}
		$data = fread ($fp,filesize($xmlToParse));

 		$rx = "/<?xml.*encoding=[\'\"](.*?)[\'\"].*?>/m";
		if (preg_match($rx, $data, $m)) $encoding = strtoupper($m[1]);
			else $encoding = "ISO-8859-1";
		
 		$this->analyseur = xml_parser_create($encoding);
 		xml_parser_set_option($this->analyseur, XML_OPTION_TARGET_ENCODING, $charset);		
		xml_parser_set_option($this->analyseur, XML_OPTION_CASE_FOLDING, true);
		xml_set_object($this->analyseur, $this);
		xml_set_element_handler($this->analyseur, "debutBalise", "finBalise");
		xml_set_character_data_handler($this->analyseur, "texte");
	
		fclose($fp);

		if ( !xml_parse( $this->analyseur, $data, TRUE ) ) {
			die( sprintf( "erreur XML %s � la ligne: %d ( $xmlToParse )\n\n",
			xml_error_string(xml_get_error_code( $this->analyseur ) ),
			xml_get_current_line_number( $this->analyseur) ) );
		}

		xml_parser_free($this->analyseur);
		unset($this->analyseur);
 	}
}
?>