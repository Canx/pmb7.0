<?php
$base_path = ".";
$base_auth = "ADMINISTRATION_AUTH";
$base_title = "\$msg[7]";
require_once ("$base_path/includes/init.inc.php");
require_once ("$base_path/usur_imp_itaca_func.php");

$categor = $_GET['categor'];

switch ($categor)
{ // Selección de opciones.

    case 'import':
        {
                // Formulario de tablas de importacion
                $nomfich = "./temp/" . $_FILES['fich']['name']; //nombre fichero en el cliente
                $tipo = $_FILES['fich']['type']; //tipo fichero
                $sep = $_POST['separador'];

                // Se admiten ficheros xml y .dat
                /* INICIO LLIUREX 24/09/2015 */
                if (!strcmp($tipo, "text/xml") || !strcmp($tipo, "application/octet-stream"))
                {
                    require ("$base_path/includes/db_param.inc.php");
                    //----------------------LLIUREX 19/12/2018-----------------------------------------
                    //----Changed calls to database. Used pmb_mysql instead mysql_ -----------------------
                    //$link2 = @mysql_connect(SQL_SERVER, USER_NAME, USER_PASS) OR die("Error MySQL");
                    // Comprobamos si la tabla de usuarios (empr) esta vacia
                    $sql_vacia = "SELECT * FROM empr";
                    $vacia = @pmb_mysql_num_rows(@pmb_mysql_query($sql_vacia, $dbh));

                    if (move_uploaded_file($_FILES['fich']['tmp_name'], $nomfich))
                    { //el POsT devuelve el nombre de archivo en el servidor y el segundo campo es a donde se va a mover.
                        $valida = validaFichero($nomfich);
                        if ($valida == 1)
                        {
                            echo "<b><center> " . $nomfich . " " . $msg["xml_incorrect"] . "</center></b>";
                            break;
                        }
                        else
                        {

                            if ($vacia == 0)
                            {
                                $tipo = "NIA";
                                $referencia = 24; //Si la tabla esta vacia el número  máximo de campos será 24

                            }
                            else
                            {
                                $tipo = "Exp";
                                $referencia = 25; //Si la tabla esta vacia el número  máximo de campos será 25

                            }

                            // TODO: Sacar $totAlu y ademas nuevo array asociativo $data!!!
                            $temp = sacaCamposItacaAlu($nomfich, $tipo);
                            $totAlu = $temp[0];
                            $dataAlu = $temp[1];

                            $temp = sacaCamposItacaProf($nomfich);
			    $totProf = $temp[0];
			    $dataProf = $temp[1];
                        }

                    }

                    $camposAlu = (count($totAlu)) - 1; //total de campos, se le resta 1 debido a que coge un campo mas (vacio)
                    if (($camposAlu % $referencia) != 0)
                    {
                        exit("Campos $camposAlu"); //Se muestra mensaje advirtiendo que el fichero no tiene la estructura correcta
                        exit("<b><center>$msg[usur_imp_a]</center></b>");
                    }
                    
		    // Añadir campo empr_grupo si no existe
                    $sql = "SELECT column_name from INFORMATION_SCHEMA.columns where table_schema='pmb' and table_name='empr' AND column_name='empr_grupo'";
                    $existeGrupo = @pmb_mysql_num_rows(@pmb_mysql_query($sql, $dbh));
		    

                    // Si el campo empr_grupo no existe se crea
                    if (! $existeGrupo)
                    {
                       $sql = "ALTER TABLE empr ADD empr_grupo varchar(15)";
                       $insert = @pmb_mysql_query($sql, $dbh);
                    }

                    //Si la tabla esta vacia se utilizará el NIA como identificador
                    $tipo_user = 0;
                    $existeNIA = 0;
                    if ($vacia != 0)
                    {
                        $idused = identificador_usado($totAlu, $camposAlu, $dbh);

                        if ($idused == "Exp")
                        {
                            $sql = "SELECT column_name from INFORMATION_SCHEMA.columns where table_schema='pmb' and table_name='empr' AND column_name='empr_NIA'";
                            $existeNIA = @pmb_mysql_query($sql, $dbh);
                            //Si el campo empr_NIA no existe se crea
                            if (@pmb_mysql_num_rows($existeNIA) == 0)
                            {
                                $sql = "ALTER TABLE empr ADD empr_NIA varchar(15)";
                                $insert = @pmb_mysql_query($sql, $dbh);
                            }
                            //Comprobamos si existe el campo empr_Tipo para distinguir entre alumnos y profesores
                            $sql = "SELECT column_name from INFORMATION_SCHEMA.columns where table_schema='pmb' and table_name='empr' AND column_name='empr_Tipo'";
                            $existeTipo = @pmb_mysql_query($sql, $dbh);
                            //Si el campo empr_Tipo no existe se crea
                            if (@pmb_mysql_num_rows($existeTipo) == 0)
                            {
                                $sql = "ALTER TABLE empr ADD empr_Tipo varchar(1)";
                                $insert = @pmb_mysql_query($sql, $dbh);
                            }

                        }
                        $tipo_user = "A";
                    }
                    //Importamos datos alumnos
                    $resul_comp = inserta_datos($vacia, $referencia, $totAlu, $camposAlu, $dbh, $idused, $lang, $tipo_user, $dataAlu);

                    //Importamos datos profesores;
                    $camposProf = (count($totProf)) - 1;
                    $tipo_user = "P";
                    $resul_prof = inserta_datos($vacia, 24, $totProf, $camposProf, $dbh, $idused, $lang, $tipo_user, $dataProf);
                    $contR = $resul_comp[1] + $resul_prof[1];
                    $contAct = $resul_comp[2] + $resul_prof[2];

                    //Se muestra mensaje indicado el número de registros importados
                    echo "<b>$msg[usur_imp_c] </b>";
                    if ($contR > 0)
                    {
                        echo "<b>$msg[usur_imp_d]  " . $contR . "</b>";
                    }
                    if ($contAct > 0)
                    {
                        echo " <b>$msg[usur_imp_q]  " . $contAct . "</b>";
                    }
                    $migracion = comprueba_migracion($dbh);
                    //---------------------------FIN LLIUREX 19/12/2018------------------------------
                    if ($resul_comp[0] > 0)
                    { //comparativa campos con los valores a insertar. Se muestra a modo de ejemplo el primer registro importado
                        $fecha = $resul_comp[3];
                        $fecha_cad = $resul_comp[4];
                        echo "<h3>$msg[usur_imp_e]&nbsp;&nbsp;&nbsp;</h3><div class='form-contenu'><table width='98%' border='0' cellspacing='10'><td class='jauge'><b>$msg[usur_imp_f] <br>$msg[usur_imp_g]</b></td><td class='jauge' width='27%'><center><b>$msg[usur_imp_h] <br>$msg[usur_imp_i]</b></center></td><td class='jauge' width='60%'><b>$msg[usur_imp_j]<br>$msg[usur_imp_k]</b></td><tr><td class='nobrd'><font color='#FF0000'>id_empr</font></td><td class='nobrd'><center><input name='id_empr' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem0' value='' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_cb</td><td class='nobrd'><center><input name='empr_cb' value='1' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem1' value='" . $totAlu[0] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_nom</td><td class='nobrd'><center><input name='empr_nom' value='2' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem2' value='" . $totAlu[1] . "'  type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_prenom</td><td class='nobrd'><center><input name='empr_prenom' value='3' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem3' value='" . $totAlu[2] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_adr1</td><td class='nobrd'><center><input name='empr_adr1' value='4' type='text' size='1' disabled><td class='nobrd'><input name='exem5' value='" . $totAlu[3] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_adr2</td><td class='nobrd'><center><input name='empr_adr2' value='5' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem6' value='" . $totAlu[4] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_cp</td><td class='nobrd'><center><input name='empr_cp' value='6' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem7' value='" . $totAlu[5] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_ville</td><td class='nobrd'><center><input name='empr_ville' value='7' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem8' value='" . $totAlu[6] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_pays</td><td class='nobrd'><center><input name='empr_pays' value='8' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem9' value='" . $totAlu[7] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_mail</td><td class='nobrd'><center><input name='empr_mail' value='9' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem10' value='" . $totAlu[8] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_tel1</td><td class='nobrd'><center><input name='empr_tel1' value='10' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem11' value='" . $totAlu[9] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_tel2</td><td class='nobrd'><center><input name='empr_tel2' value='11' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem12' value='" . $totAlu[10] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_prof</td><td class='nobrd'><center><input name='empr_prof' value='12' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem13' value='" . $totalu[11] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_year</td><td class='nobrd'><center><input name='empr_year' value='13' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem14' value='" . $totAlu[12] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_categ</font></td><td class='nobrd'><center><input name='empr_categ' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem15' value='7' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_codestat</font></td><td class='nobrd'><center><input name='empr_codestat' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem16' value='2' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_creation</font></td><td class='nobrd'><center><input name='empr_creation' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem17' value='" . $fecha . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_modif</font></td><td class='nobrd'><center><input name='empr_modif' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem18' value='" . $fecha . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_sexe</td><td class='nobrd'><center><input name='empr_sexe' value='14' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem19' value='" . $totAlu[13] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_login</td><td class='nobrd'><center><input name='empr_login' value='15' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem20' value='" . $totAlu[14] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_password</td><td class='nobrd'><center><input name='empr_password' value='16' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem21' value='" . $totAlu[15] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_date_adhesion</font></td><td class='nobrd'><center><input name='empr_date_adhesion' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem22' value='" . $fecha . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_date_expiration</font></td><td class='nobrd'><center><input name='empr_date_expiration' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem23' value='" . $fecha_cad . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_msg</td><td class='nobrd'><center><input name='empr_msg' value='17' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem24' value='" . $totAlu[16] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_lang</td><td class='nobrd'><center><input name='empr_lang' value='18' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem25' value='" . $totAlu[17] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'><font color='#FF0000'>empr_ldap</font></td><td class='nobrd'><center><input name='empr_ldap' value='0' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem26' value='' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>type_abt</td><td class='nobrd'><center><input name='type_abt' value='19' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem27' value='" . $totAlu[18] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>last_loan_date</td><td class='nobrd'><center><input name='last_loan_date' value='20' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem28' value='" . $totAlu[19] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_location</td><td class='nobrd'><center><input name='empr_location' value='21' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem29' value='" . $totAlu[20] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>date_fin_blocage</td><td class='nobrd'><center><input name='date_fin_blocage' value='22' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem30' value='" . $totAlu[21] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>total_loans</td><td class='nobrd'><center><input name='total_loans' value='23' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem31' value='" . $totAlu[22] . "' type='text' disabled size='40'></td></tr><tr><td class='nobrd'>empr_statut</td><td class='nobrd'><center><input name='empr_statut' value='24' type='text' size='1' disabled></center></td><td class='nobrd'><input name='exem32' value='" . $totAlu[23] . "' type='text' disabled size='40'></td></tr></table>";
                        /* FIN LLIUREX 24/09/2015 */
                        fclose($nomfich);
                        unlink($nomfich);
                        break;
                    }

                }
                else
                { //Si el fichero no es xml o .dat se muestra mensaje de advertencia
                    echo "<b><center> " . $nomfich . " " . $msg["usur_imp_l"] . "</center></b>";

                }
            break;
        }

    default:

        // Formulario para elegir fichero a importar de itaca
        echo "<form class='form-admin' name='form1' ENCTYPE=\"multipart/form-data\" method='post' action=\"./admin.php?categ=empr&sub=itaca&action=?&categor=import\"><h3>$msg[import_usu_from_itaca_a]</h3><div class='form-contenu'><div class='row'><div class='colonne60'><label class='etiquette' for='form_import_lec'>$msg[importa_d]&nbsp;</label><input name='fich' accept='text/plain, .xml, .dat' type='file'  size='40'></div><input type='button' name='fichero' value='Continuar' onclick='form.submit()'></div></form>";

        break;

    }
    //-------------------------------------> L L I U R E X <--------------------------------------//

?>
