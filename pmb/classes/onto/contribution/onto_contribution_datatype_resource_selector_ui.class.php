<?php
// +-------------------------------------------------+
// � 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: onto_contribution_datatype_resource_selector_ui.class.php,v 1.12.2.7 2021/02/05 15:54:32 gneveu Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once $class_path.'/onto/common/onto_common_datatype_ui.class.php';
require_once($include_path.'/templates/onto/contribution/onto_contribution_datatype_ui.tpl.php');
/**
 * class onto_common_datatype_resource_selector_ui
 * 
 */
class onto_contribution_datatype_resource_selector_ui extends onto_common_datatype_resource_selector_ui {
	
	/**
	 * 
	 *
	 * @param onto_common_property $property la propri�t� concern�e
	 * @param restriction $restrictions le tableau des restrictions associ�es � la propri�t� 
	 * @param array datas le tableau des datatypes
	 * @param string instance_name nom de l'instance
	 * @param string flag Flag

	 * @return string
	 * @static
	 * @access public
	 */
    public static function get_form($item_uri,$property, $restrictions,$datas, $instance_name,$flag) {
        global $msg,$charset,$ontology_tpl, $ontology_contribution_tpl;
        
        if (empty($datas)) {
            $datas = array();
        }
        $form=$ontology_tpl['form_row'];
        
        $form=str_replace("!!onto_row_label!!",htmlentities(encoding_normalize::charset_normalize($property->get_label(), 'utf-8') ,ENT_QUOTES,$charset) , $form);
        
        /** traitement initial du range ?!*/
        $range_for_form = "";
        if(is_array($property->range)){
            foreach($property->range as $range){
                if($range_for_form) $range_for_form.="|||";
                $range_for_form.=$range;
            }
        }
        /** **/
        /** TODO: � revoir avec le chef ** /
         /** On part du principe que l'on a qu'un range **/
        // 		$selector_url = $this->get_resource_selector_url($property->range[0]);
        
        $content='';
        if (!$property->is_no_search()) {
            $content.=$ontology_contribution_tpl['form_row_content_input_sel'];
        }
        
        if (($restrictions->get_max() < count($datas) || $restrictions->get_max() === -1) && !$property->is_no_search()) {
            $content.=$ontology_contribution_tpl['form_row_content_input_add_ressource_selector'];
        }
        
        if (!empty($datas)) {
            $i=1;
            $first=true;
            $new_element_order=max(array_keys($datas));
            
            $form=str_replace("!!onto_new_order!!",$new_element_order , $form);
            
            foreach($datas as $key=>$data){
                $row=$ontology_contribution_tpl['form_row_content'];
                
                if($data->get_order()){
                    $order=$data->get_order();
                }else{
                    $order=$key;
                }

                $inside_row=$ontology_contribution_tpl['form_row_content_resource_selector'];

                $inside_row .= $ontology_contribution_tpl['form_row_content_type'];
                $formated_value = $data->get_formated_value();
                $value = $data->get_value();
                
                $inside_row=str_replace("!!form_row_content_resource_selector_display_label!!",htmlentities(stripslashes((is_array($formated_value) ? reset($formated_value) : $formated_value)),ENT_QUOTES,$charset), $inside_row);
				$inside_row=str_replace("!!onto_current_element!!",onto_common_uri::get_id($item_uri),$inside_row);
                $inside_row=str_replace("!!onto_current_range!!",$range_for_form,$inside_row);
                $inside_row=str_replace("!!onto_row_content_range!!",$range_for_form , $inside_row);
                
                if (!$property->is_no_search()) {
				    $inside_row=str_replace("!!form_row_content_resource_selector_value!!", (is_array($value) ? reset($value) : $value), $inside_row);
                } else {
				    $inside_row=str_replace("!!form_row_content_resource_selector_value!!", "", $inside_row);
                }
                $inside_row=str_replace("!!form_row_content_resource_selector_range!!",$data->get_value_type() , $inside_row);
                $row=str_replace("!!onto_inside_row!!",$inside_row , $row);
                
                $input='';
                if (!$property->is_no_search()) {
                    if($first){
                        $input.=$ontology_contribution_tpl['form_row_content_input_remove'];
                    }else{
                        $input.=$ontology_contribution_tpl['form_row_content_input_del'];
                    }
                }
                
                if (!empty($property->has_linked_form) && $first) {
                    $input .= $ontology_contribution_tpl['form_row_content_linked_form'];
                    $url = './ajax.php?module=ajax&categ=contribution&sub='.$property->linked_form['form_type'].'&area_id='.$property->linked_form['area_id'].'&id='.onto_common_uri::get_id($data->get_raw_value()).'&sub_form=1&form_id='.$property->linked_form['form_id'].'&form_uri='.urlencode($property->linked_form['form_id_store']);
                    $input = str_replace("!!url_linked_form!!", $url, $input);
                    $input = str_replace("!!linked_form_title!!", $property->linked_form['form_title'], $input);
                }
                if (strpos($input, "!!property_name!!")) {
                    $input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
                }
                
				$row=str_replace("!!onto_row_inputs!!",$input , $row);
                $row=str_replace("!!onto_row_order!!",$order , $row);
                
                $content.=$row;
                $first=false;
                $i++;
            }
        }else{
            $form=str_replace("!!onto_new_order!!","0" , $form);
            
            $row=$ontology_contribution_tpl['form_row_content'];
            
            $inside_row=$ontology_contribution_tpl['form_row_content_resource_selector'];
            $inside_row=str_replace("!!form_row_content_resource_selector_display_label!!","" , $inside_row);
            $inside_row=str_replace("!!form_row_content_resource_selector_value!!","" , $inside_row);
            $inside_row=str_replace("!!form_row_content_resource_selector_range!!","" , $inside_row);
            $inside_row=str_replace("!!onto_current_element!!",onto_common_uri::get_id($item_uri),$inside_row);
            $inside_row=str_replace("!!onto_current_range!!",$range_for_form,$inside_row);
            
            $inside_row=str_replace("!!onto_row_content_range!!",$range_for_form,$inside_row);
            
            $row=str_replace("!!onto_inside_row!!",$inside_row , $row);
            
            $input='';
            if (!$property->is_no_search()) {
                $input.=$ontology_contribution_tpl['form_row_content_input_remove'];
            }
            $input = str_replace("!!property_name!!", rawurlencode($property->pmb_name), $input);
            $row=str_replace("!!onto_row_inputs!!",$input , $row);
            
            $row=str_replace("!!onto_row_order!!","0" , $row);
            
            $content.=$row;
            
        }
        
        //sans content on a pas les champs cach� donc toujours besoin
        $form=str_replace("!!onto_rows!!",$content ,$form);
        //remplace les id des input donc n�cessaire        
        $form=str_replace("!!onto_row_id!!",$instance_name.'_'.$property->pmb_name , $form);
        $form=str_replace("!!onto_completion!!",self::get_completion_from_range($range_for_form), $form);
        
        //le selecteur doit il etre masqu� ?

        $form=str_replace("!!onto_selector_url!!",self::get_resource_selector_url($property->range[0]), $form);

        return $form;
        } // end of member function get_form	
} // end of onto_contribution_datatype_resource_selector_ui
