<?php
// +-------------------------------------------------+
// � 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_docnum_licence_ui.class.php,v 1.1.2.1 2021/03/22 16:24:02 tsamson Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path.'/templates/onto/contribution/onto_contribution_datatype_ui.tpl.php');
require_once($class_path.'/encoding_normalize.class.php');

class onto_contribution_datatype_docnum_licence_ui extends onto_common_datatype_ui {
    
    /** Aggregations: */
    
    /** Compositions: */
    
    /*** Attributes: ***/
    
    static protected $licence_data;
    
    /**
     *
     *
     * @param property property la propri�t� concern�e
     * @param restriction $restrictions le tableau des restrictions associ�es � la propri�t�
     * @param array datas le tableau des datatypes
     * @param string instance_name nom de l'instance
     * @param string flag Flag
     
     * @return string
     * @static
     * @access public
     */
    static public function get_form($item_uri,$property, $restrictions,$datas, $instance_name,$flag) {
        global $msg,$charset,$ontology_tpl, $ontology_contribution_tpl;
        
        $form=$ontology_tpl['form_row'];
        $form=str_replace("!!onto_row_label!!",htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8') ,ENT_QUOTES,$charset) , $form);
        
        static::get_licence_data();
        
        $content='';
        if (!empty($datas)) {
            $i=1;
            $first=true;
            $new_element_order=max(array_keys($datas));
            
            $form=str_replace("!!onto_new_order!!",$new_element_order , $form);
            foreach ($datas as $data) {
                $row=$ontology_tpl['form_row_content'];
                $inside_row = $ontology_contribution_tpl['form_row_content_licence'];
                
                $inside_row=str_replace("!!form_row_content_licence_value!!",$data->get_value(), $inside_row);
                $inside_row=str_replace('!!onto_row_licence_data!!', encoding_normalize::json_encode(static::$licence_data), $inside_row);
                $inside_row=str_replace("!!onto_row_content_list_range!!",$property->range[0] , $inside_row);
                
                $row=str_replace("!!onto_inside_row!!",$inside_row , $row);
                $row=str_replace("!!onto_row_inputs!!",'' , $row);
                
                $row=str_replace("!!onto_row_order!!",0 , $row);
                
                $content.=$row;
                $first=false;
                $i++;
            }
        }else{
            $form=str_replace("!!onto_new_order!!", "0", $form);
            
            $row=$ontology_tpl['form_row_content'];
            
            $inside_row = $ontology_contribution_tpl['form_row_content_licence'];
            $inside_row=str_replace("!!form_row_content_licence_value!!","", $inside_row);
            $inside_row=str_replace('!!onto_row_licence_data!!', encoding_normalize::json_encode(static::$licence_data), $inside_row);
            $inside_row=str_replace("!!onto_row_content_list_range!!",$property->range[0] , $inside_row);
            
            $row=str_replace("!!onto_inside_row!!",$inside_row , $row);
            $row=str_replace("!!onto_row_inputs!!",'' , $row);
            $row=str_replace("!!onto_row_order!!","0" , $row);
            
            $content.=$row;
        }
        
        $form=str_replace("!!onto_rows!!",$content ,$form);
        $form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
        
        return $form;
        
    } // end of member function get_form
    
    public static function get_licence_data() {
        if (isset(static::$licence_data)) {
            return static::$licence_data;
        }
        static::$licence_data = [];
        $query = "SELECT * FROM explnum_licence_profiles P
            JOIN explnum_licence L ON P.explnum_licence_profile_explnum_licence_num = L.id_explnum_licence
            ORDER BY P.explnum_licence_profile_label";
        $result = pmb_mysql_query($query);
        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                if (!isset(static::$licence_data[$row["id_explnum_licence"]])) {
                    static::$licence_data[$row["id_explnum_licence"]] = [
                        "id" => $row["id_explnum_licence"],
                        "label" => $row["explnum_licence_label"],
                        "profiles" => [],
                    ];
                }
                static::$licence_data[$row["id_explnum_licence"]]["profiles"][$row["id_explnum_licence_profile"]] = [
                    "id" => $row["id_explnum_licence_profile"],
                    "label" => $row["explnum_licence_profile_label"],
                    "logo" => $row["explnum_licence_profile_logo_url"],
                    "explanation" => $row["explnum_licence_profile_explanation"],
                ];
            }
        }
        return static::$licence_data;
    }
    
    /**
     *
     *
     * @param onto_common_datatype datas Tableau des valeurs � afficher associ�es � la propri�t�
     * @param property property la propri�t� � utiliser
     * @param string instance_name nom de l'instance
     *
     * @return string
     * @access public
     */
    public function get_display($datas, $property, $instance_name) {
        
        $display='<div id="'.$instance_name.'_'.$property->pmb_name.'">';
        $display.='<p>';
        $display.=$property->get_label().' : ';
        foreach($datas as $data){
            $display.=$data->get_formated_value();
        }
        $display.='</p>';
        $display.='</div>';
        return $display;
        
    } // end of member function get_display
}