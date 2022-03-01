<?php
//-------------------------------------> L L I U R E X <--------------------------------------//
// Modulo de importación/exportacion de usuarios de pmb, a partir de un fichero de texto plano
function string_to_array($string)
{
    $largo = strlen($string); //Largo de cadena
    $final_array = array();
    for ($i = 0;$i < $largo;$i++)
    {
        $caracter = $string[$i];
        array_push($final_array, $caracter);
    }
    return $final_array;
}

//-----------Funcion para validar el fichero xml de alumnos y profesores ------------------------//
function validaFichero($archivo)
{

    $doc = new DOMDocument();

    if ($doc->load($archivo) === false)
    {
        print ("Error loading file!");
        return 1;
    }

    $listaAlumnos = $doc->getElementsByTagName("alumne");
    if ($listaAlumnos->length == 0)
    {
        print ("Not students found!");
        return 1;

    }
    else
    {
        $listaTagsAlu = $listaAlumnos->item(0);
        $nia = $listaTagsAlu->getElementsByTagName("nia");
        if ($nia->length == 0)
        {
            print ("No NIA found!");
            return 1;
        }
        $nom = $listaTagsAlu->getElementsByTagName("nom");
        if ($nom->length == 0)
        {
            print ("No name found!");
            return 1;
        }
        $cognoms = $listaTagsAlu->getElementsByTagName("cognoms");
        if ($cognoms->length == 0)
        {
            print ("No surname found!");
            return 1;
        }
        $nif = $listaTagsAlu->getElementsByTagName("nif");
        if ($nif->length == 0)
        {
            print ("No nif found!");
            return 1;
        }
        $numeroExpediente = $listaTagsAlu->getElementsByTagName("numeroExpedient");
        if ($numeroExpediente->length == 0)
        {
            print ("No expedient found!");
            return 1;
        }

    }
    $listaProfesores = $doc->getElementsByTagName("professor");

    if ($listaProfesores->length == 0)
    {
        print ("No teachers found!");
        return 1;

    }
    else
    {
        $listaTagsProfe = $listaProfesores->item(0);
        $nom = $listaTagsProfe->getElementsByTagName("nom");
        if ($nom->length == 0)
        {
            print ("No teacher name found!");
            return 1;
        }
        $cognoms = $listaTagsProfe->getElementsByTagName("cognoms");
        if ($cognoms->length == 0)
        {
            print ("No teacher surname found!");
            return 1;
        }
        $nif = $listaTagsProfe->getElementsByTagName("document");
        if ($nif->length == 0)
        {
            $nif = $listaTagsProfe->getElementsByTagName("nif	");
            if ($nif->length == 0)
            {
                print ("No teacher nif found!");
                return 1;
            }
        }
    }
    return 0;

}

// Wrapper de transición
function sacaCamposItacaAluOld($archivo, $ide)
{
    $all_data = sacaCamposItacaAlu($archivo, $ide);
    return $all_data[0]; // Solo devolvemos la parte antigua

}

function sacaCamposItacaAluNew($archivo, $ide)
{
    $all_data = sacaCamposItacaAlu($archivo, $ide);
    return $all_data[1]; // Solo devolvemos la parte nueva

}
function sacaCamposItacaAlu($archivo, $ide)
{

    $vectorA = array();

    // Array asociativo de transición
    $data = array();

    $indice = 0;

    /*Instancio la clase DOM que nos permitira operar con el XML*/
    $doc = new DOMDocument();

    /* Cargo el XML, En este caso es un archivo llamado llxgesc.xml, podriamos usar loadXML si desamos leer de un string*/
    $doc->load($archivo);

    // Obtengo el nodo alumne (listaAlumnos) del XML a traves del metodo getElementsByTagName, retorna una lista de todos los nodos encontrados
    $listaAlumnos = $doc->getElementsByTagName("alumne");

    /*Al ser $listaAlumnos una lista de nodos   lo puedo recorrer y obtener todo  su contenido*/
    $data = array();
    foreach ($listaAlumnos as $alumno)
    {
        /* Obtengo el valor del primer elemento 'item(0)' de la lista $autors.  Si existiera un atributo en el nodo para obtenerlo usaria
         $authors->getAttribute('atributo');    */
        $nombres = $alumno->getElementsByTagName("nom"); // $authors = $book->getElementsByTagName( "author" );
        $nombre = $nombres->item(0)->nodeValue;

        $apellidos = $alumno->getElementsByTagName("cognoms"); // $publishers = $book->getElementsByTagName( "publisher" );
        $apellido = $apellidos->item(0)->nodeValue; // $publisher = $publishers->item(0)->nodeValue;
        // Añadimos el campo de grupo
        $grupos = $alumno->getElementsByTagName("grup");
        $grupo = $grupos->item(0)->nodeValue;

        /* INI LLIUREX 24/09/2015 */
        //Si la tabla no esta vacia
        if ($ide == "Exp")
        {
            $expedientes = $alumno->getElementsByTagName("numeroExpedient"); // $publishers = $book->getElementsByTagName( "publisher" );
            $expediente = $expedientes->item(0)->nodeValue; // $publisher = $publishers->item(0)->nodeValue;
            // Eliminamos la barra del numero de expediente
            if ($expediente != "")
            {
                $expediente = trim($expediente);
                // Para separar el numero de expediente y extraer sólo los números tenemos que eliminar separadores que pueden ser barra, punto, espacio o sin separador
                $cadena = "";
                $expediente = string_to_array($expediente);
                if (count($expediente) > 0)
                {
                    for ($i = 0;$i < count($expediente);$i++)
                    {
                        $digito = $expediente[$i];
                        if ($digito >= "0" && $digito <= "9") $cadena .= $digito;
                    }
                }
                $cadena = trim($cadena);
                if (strlen($cadena) < 4) $cadena = str_pad($cadena, 4, "0", STR_PAD_LEFT);
            }
            else
            {
                $expedientes = $alumno->getElementsByTagName("nia"); // $publishers = $book->getElementsByTagName( "publisher" );
                $expediente = $expedientes->item(0)->nodeValue; // $publisher = $publishers->item(0)->nodeValue;
                $cadena = $expediente;
            }
        }
        $nias = $alumno->getElementsByTagName("nia"); // $publishers = $book->getElementsByTagName( "publisher" );
        $nia = $nias->item(0)->nodeValue; // $publisher = $publishers->item(0)->nodeValue


        /*		$posicionBarra = strpos ($expediente, "/");
        if (!$posicionBarra) $posicionBarra = strpos ($expediente, "-");

        $trozo1 = substr ( $expediente , 0, $posicionBarra );
        $trozo2 = substr ( $expediente , $posicionBarra+1, strlen($expediente)-$posicionBarra);
        $cadena = $trozo1 . $trozo2; */

        // Tenemos que añadir ceros delante hasta completar un minimo de 4 digitos en el numero de expediente
        // con menos digitos el lector de CB no funciona
        if ($ide == "Exp")
        {
            // Inicializamos el vector
            for ($i = $indice;$i < $indice + 25;$i++) $vectorA[$i] = "";
            $vectorA[$indice] = $cadena;
            $vectorA[$indice + 24] = $nia;

        }
        else
        {

            // Inicializamos el vector
            for ($i = $indice;$i < $indice + 24;$i++) $vectorA[$i] = "";
            $vectorA[$indice] = $nia;

        }

        //$vector[$indice] = $cadena;
        $vectorA[$indice + 1] = utf8_decode(trim($apellido));
        $vectorA[$indice + 2] = utf8_decode(trim($nombre));
        //$vector[$indice+17] = "va_ES";
        $indice = $i;

        // Creamos el campo en el hash asociativo
        $data[$nia] = array(
            "nombre" => utf8_decode(trim($nombre)) ,
            "apellidos" => utf8_decode(trim($apellido)) ,
            "grupo" => utf8_decode(trim($grupo))
        );

    }
    $vectorA[$indice] = "Campo vacio";

    // Devolvemos el vector antiguo y el array asociativo nuevo de transición.
    return [$vectorA, $data];
}

function sacaCamposItacaProf($archivo)
{

    $vectorP = array();
    $indice = 0;

    /*Instancio la clase DOM que nos permitira operar con el XML*/
    $doc = new DOMDocument();

    /* Cargo el XML, En este caso es un archivo llamado llxgesc.xml, podriamos usar loadXML si desamos leer de un string*/
    $doc->load($archivo);

    // Pasamos profesores
    $listaProfesores = $doc->getElementsByTagName("professor");

    foreach ($listaProfesores as $profesor)
    {
        /* Obtengo el valor del primer elemento 'item(0)' de la lista $autors.  Si existiera un atributo en el nodo para obtenerlo usaria
         $authors->getAttribute('atributo');    */
        $nombres = $profesor->getElementsByTagName("nom"); // $authors = $book->getElementsByTagName( "author" );
        $nombre = $nombres->item(0)->nodeValue;

        $apellidos = $profesor->getElementsByTagName("cognoms"); // $publishers = $book->getElementsByTagName( "publisher" );
        $apellido = $apellidos->item(0)->nodeValue; // $publisher = $publishers->item(0)->nodeValue;
        $nifs = $profesor->getElementsByTagName("document"); // $publishers = $book->getElementsByTagName( "publisher" );
        // Si lo que se importa es un fichero GESCEN el campo nif a parsear es distinto
        if ($nifs->length == 0) $nifs = $profesor->getElementsByTagName("nif");

        $nif = $nifs->item(0)->nodeValue; // $publisher = $publishers->item(0)->nodeValue;
        for ($i = $indice;$i < $indice + 24;$i++) $vectorP[$i] = "";

        $tam = strlen($nif);
        $nif = substr(trim($nif) , 1, $tam - 2);
        if (strlen($nif) < 4) $nif = str_pad($nif, 4, "0", STR_PAD_LEFT); // Comprobamos tam min 4 digitos
        $vectorP[$indice] = $nif;

        $vectorP[$indice + 1] = utf8_decode(trim($apellido));
        $vectorP[$indice + 2] = utf8_decode(trim($nombre));
        //$vector[$indice+17] = "va_ES";
        $indice = $i;
    }

    $vectorP[$indice] = "Campo vacio"; // Lo añadimos para hacerlo compatible con la importacion del fichero plano del GESCEN
    return $vectorP;
}

//Funcion para insertar los datos en la base de datos
// TODO: el array asociativo data se accede mediante el NIA de los alumnos de momento
function inserta_datos($vacia, $referencia, $tot, $campos, $dbh, $idused, $lang, $tipo_user, $data)
{

    $resul_comp = array();
    $correctorNIA = 0;
    $correctorVacia = 0;
    $num = 0;
    $cont = 0;
    $contAct = 0;
    $categ = 0;

    if ($tipo_user == 'A')
    {
        $categ = 5;
    }
    else
    {
        $categ = 7;
    }

    if (!$vacia == 0)
    {

        if ($idused == "Exp" && $tipo_user == "A")
        {
            $correctorVacia++;
            $correctorNIA = 24;
        }

        if ($idused == "NIA" && $tipo_user == "A")
        {
            $correctorVacia++;
        }
        if ($tipo_user == "P")
        {
            $correctorNIA = 23;
        }
    }
    else
    {
        $correctorNIA = 23;
    }

    while ($num < $campos)
    {
        if ($tot[$num] == " ")
        {
            $tot[$num] = NULL;
        }

        //----------------------LLIUREX 19/12/2018-----------------------------------------
        //----Changed calls to database. Used pmb_mysql instead mysql_  ----------------
        if (((($num + 1) % $referencia) == 0) && ($num != 0))
        { //cada 24 debido a que hay 24 campos
            // Comprobamos si existe ya un alumno el mismo nombre y apellidos en la BD del PMB, si está actualizamos
            // su datos personales si no los incorporamos al PMB
            $sql_comp = "SELECT `empr`.`id_empr`, `empr`.`empr_login`, `empr`.`empr_password`, `empr`.`empr_location` FROM `empr` WHERE (`empr`.`empr_cb`='" . $tot[$num - $correctorNIA] . "' AND `empr`. `empr_nom` like '" . $tot[$num - (22 + $correctorVacia) ] . "' AND `empr`. `empr_prenom` like '" . $tot[$num - (21 + $correctorVacia) ] . "' )";
            $resul1 = @pmb_mysql_query($sql_comp, $dbh);
            $fecha = date('Y-m-d');
            $fecha_cad = date('Y-m-d', strtotime('+455 day'));

            //Se actualiza el usuario y password del usuario si vien en el fichero. Si no se utiliza el Número Expediente o NIA como usuario y password
            if (trim($tot[$num - 9]) != "")
            {
                $user_a = addslashes($tot[$num - 9]);
                if (trim($tot[$num - 8]) != "") $pass_a = addslashes($tot[$num - 8]);
                else $pass_a = $tot[$num - $correctorNIA];
            }
            else
            {
                $user_a = $tot[$num - $correctorNIA];
                $pass_a = $tot[$num - $correctorNIA];
                //echo "a".$user_a." ".$pass_a."a";
                //exit(0);

            }
            if (trim($tot[$num - 3]) != "") $loca = intval(($tot[$num - 3]));
            else $loca = 1;

            // Ponemos los nombre de los campos
            $nia = $tot[$num - $correctorNIA];
	    $nombre = $data[$nia]["nombre"];
	    $apellidos = $data[$nia]["apellidos"];
            $grupo = $data[$nia]["grupo"];

            //Si el alumno esta repetido se actualizan sus datos a excepción del empr_cb
            if (@pmb_mysql_num_rows($resul1) != 0)
            {

                $sql_user_cad = "SELECT `empr`.`id_empr`, `empr`.`empr_login`, `empr`.`empr_password`, `empr`.`empr_location` FROM `empr` WHERE (`empr`.`empr_cb`='" . $nia . "' AND `empr`. `empr_nom` like '" . $tot[$num - (22 + $correctorVacia) ] . "' AND `empr`. `empr_prenom` like '" . $tot[$num - (21 + $correctorVacia) ] . "' AND  `empr`. `empr_date_expiration`< '" . $fecha . "')";
                $resul_cad = @pmb_mysql_query($sql_user_cad, $dbh);

                //echo "$msg[usur_imp_b] <b>" . $tot[$num-23] . "</b><br>";
                $row1 = pmb_mysql_fetch_array($resul1);
                $requete = "UPDATE empr SET ";
                $requete .= "empr_nom='" . fields_slashes($nombre) . "',";
                $requete .= "empr_prenom='" . fields_slashes($apellidos) . "',";
                $requete .= "empr_grupo='" . fields_slashes($grupo) . "',";
                $requete .= "empr_adr1='" . fields_slashes($tot[$num - (20 + $correctorVacia) ]) . "',";
                $requete .= "empr_adr2='" . fields_slashes($tot[$num - (19 + $correctorVacia) ]) . "',";
                $requete .= "empr_cp='" . fields_slashes($tot[$num - (18 + $correctorVacia) ]) . "',";
                $requete .= "empr_ville='" . fields_slashes($tot[$num - (17 + $correctorVacia) ]) . "',";
                $requete .= "empr_pays='" . fields_slashes($tot[$num - (16 + $correctorVacia) ]) . "',";
                $requete .= "empr_mail='" . fields_slashes($tot[$num - (15 + $correctorVacia) ]) . "',";
                $requete .= "empr_tel1='" . fields_slashes($tot[$num - (14 + $correctorVacia) ]) . "',";
                $requete .= "empr_tel2='" . fields_slashes($tot[$num - (13 + $correctorVacia) ]) . "',";
                $requete .= "empr_prof='" . fields_slashes($tot[$num - (12 + $correctorVacia) ]) . "',";
                $requete .= "empr_year=" . intval(($tot[$num - (11 + $correctorVacia) ])) . ",";
                if ($idused == "Exp" && $tipo_user == "A")
                {
                    $requete .= "empr_NIA='" . fields_slashes($tot[$num]) . "',";
                    $requete .= "empr_Tipo='" . $tipo_user . "',";
                }

                if ($idused == "Exp" && $tipo_user == "P")
                {
                    $requete .= "empr_Tipo='" . $tipo_user . "',";
                }

                if ($row1['empr_login'] == "")
                {
                    $requete .= "empr_login='" . $user_a . "', ";
                    $requete .= "empr_password='" . $pass_a . "', ";
                }
                //$requete .= "empr_msg='".$tot[$num-7]."' ";
                //$requete .= "empr_lang='".$lang."', ";
                //$requete .= "type_abt='".$tot[$num-5]."', ";
                //$requete .= "last_loan_date='".$tot[$num-4]."', ";
                if ($row1['empr_location'] == "" || intval($row1['empr_location']) == 0) $requete .= "empr_location='" . $loca . "', ";
                //$requete .= "date_fin_blocage=$tot[$num-22],";
                //$requete .= "total_loans=$tot[$num-22],";
                //$requete .= "empr_statut='"$tot[$num-22]."',";
                $requete .= "empr_modif='" . $fecha . "',";
                if (@pmb_mysql_num_rows($resul_cad) != 0)
                {
                    $requete .= "empr_date_expiration='" . $fecha_cad . "',";
                }
                $requete .= "empr_sexe=" . intval(($tot[$num - (10 + $correctorVacia) ])) . "";
                $requete .= " WHERE id_empr=" . intval($row1['id_empr']) . " ";
                $resul2 = @pmb_mysql_query($requete, $dbh);
                $contAct++;

            }
            else
            {

                if ($tot[$num - $correctorVacia] == "") $tot[$num - $correctorVacia] = 1;
                if ($idused == "Exp")
                {

                    if ($tipo_user == "A")
                    {
                        $sql = "insert into empr (empr_cb, empr_nom, empr_prenom, empr_grupo, empr_adr1, empr_adr2, empr_cp, empr_ville, empr_pays, empr_mail, empr_tel1, empr_tel2, empr_prof, empr_year, empr_sexe, empr_login, empr_password, empr_msg, empr_lang, type_abt, last_loan_date, empr_location, date_fin_blocage, total_loans, empr_statut, empr_creation, empr_modif, empr_date_adhesion, empr_date_expiration, empr_categ, empr_codestat,empr_NIA,empr_Tipo) values ( '" . fields_slashes($nia) . "', '" . fields_slashes($nombre) . "', '" . fields_slashes($apellidos) . "', '" . fields_slashes($grupo) . "', '" . fields_slashes($tot[$num - (20 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (19 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (18 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (17 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (16 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (15 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (14 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (13 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (12 + $correctorVacia) ]) . "', " . intval(($tot[$num - (11 + $correctorVacia) ])) . ", " . intval(($tot[$num - (10 + $correctorVacia) ])) . ", '" . $user_a . "', '" . $pass_a . "', '" . fields_slashes($tot[$num - (7 + $correctorVacia) ]) . "', '" . $lang . "', '" . fields_slashes($tot[$num - (5 + $correctorVacia) ]) . "', '" . $tot[$num - (4 + $correctorVacia) ] . "', $loca, '" . $tot[$num - (2 + $correctorVacia) ] . "', '" . $tot[$num - (1 + $correctorVacia) ] . "', '" . $tot[$num - ($correctorVacia) ] . "', '" . $fecha . "', '" . $fecha . "', '" . $fecha . "', '" . $fecha_cad . "','" . $categ . "', 2, '" . fields_slashes($tot[$num]) . "', '" . $tipo_user . "')";

                    }

                    if ($tipo_user == "P")
                    {
                        $sql = "insert into empr (empr_cb, empr_nom, empr_prenom, empr_adr1, empr_adr2, empr_cp, empr_ville, empr_pays, empr_mail, empr_tel1, empr_tel2, empr_prof, empr_year, empr_sexe, empr_login, empr_password, empr_msg, empr_lang, type_abt, last_loan_date, empr_location, date_fin_blocage, total_loans, empr_statut, empr_creation, empr_modif, empr_date_adhesion, empr_date_expiration, empr_categ, empr_codestat,empr_Tipo) values ( '" . fields_slashes($tot[$num - $correctorNIA]) . "', '" . fields_slashes($tot[$num - (22 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (21 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (20 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (19 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (18 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (17 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (16 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (15 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (14 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (13 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (12 + $correctorVacia) ]) . "', " . intval(($tot[$num - (11 + $correctorVacia) ])) . ", " . intval(($tot[$num - (10 + $correctorVacia) ])) . ", '" . $user_a . "', '" . $pass_a . "', '" . fields_slashes($tot[$num - (7 + $correctorVacia) ]) . "', '" . $lang . "', '" . fields_slashes($tot[$num - (5 + $correctorVacia) ]) . "', '" . $tot[$num - (4 + $correctorVacia) ] . "', $loca, '" . $tot[$num - (2 + $correctorVacia) ] . "', '" . $tot[$num - (1 + $correctorVacia) ] . "', '" . $tot[$num - ($correctorVacia) ] . "', '" . $fecha . "', '" . $fecha . "', '" . $fecha . "', '" . $fecha_cad . "', '" . $categ . "', 2,'" . $tipo_user . "')";

                    }
                }
                else
                {
                    $sql = "insert into empr (empr_cb, empr_nom, empr_prenom, empr_grupo, empr_adr1, empr_adr2, empr_cp, empr_ville, empr_pays, empr_mail, empr_tel1, empr_tel2, empr_prof, empr_year, empr_sexe, empr_login, empr_password, empr_msg, empr_lang, type_abt, last_loan_date, empr_location, date_fin_blocage, total_loans, empr_statut, empr_creation, empr_modif, empr_date_adhesion, empr_date_expiration, empr_categ, empr_codestat) values ( '" . fields_slashes($nia) . "', '" . fields_slashes($nombre) . "', '" . fields_slashes($apellidos) . "', '" . fields_slashes($grupo) . "', '" . fields_slashes($tot[$num - (20 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (19 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (18 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (17 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (16 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (15 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (14 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (13 + $correctorVacia) ]) . "', '" . fields_slashes($tot[$num - (12 + $correctorVacia) ]) . "', " . intval(($tot[$num - (11 + $correctorVacia) ])) . ", " . intval(($tot[$num - (10 + $correctorVacia) ])) . ", '" . $user_a . "', '" . $pass_a . "', '" . fields_slashes($tot[$num - (7 + $correctorVacia) ]) . "', '" . $lang . "', '" . fields_slashes($tot[$num - (5 + $correctorVacia) ]) . "', '" . $tot[$num - (4 + $correctorVacia) ] . "', $loca, '" . $tot[$num - (2 + $correctorVacia) ] . "', '" . $tot[$num - (1 + $correctorVacia) ] . "', '" . $tot[$num - ($correctorVacia) ] . "', '" . $fecha . "', '" . $fecha . "', '" . $fecha . "', '" . $fecha_cad . "', '" . $categ . "', 2 )";
                }

                $resul2 = @pmb_mysql_query($sql, $dbh);

                //---------------------------------FIN LLIUREX 19/12/2018-----------------------------
                $cont++;
            }
        }

        $num++;
    } // Fin del While
    $resul_comp[0] = $num;
    $resul_comp[1] = $cont;
    $resul_comp[2] = $contAct;
    $resul_comp[3] = $fecha;
    $resul_comp[4] = $fecha_cad;
    return $resul_comp;

}

//Funcion para comprobar que tipo de identificador (Número de Expediente o NIA) se esta usando
function identificador_usado($tot, $campos, $dbh)
{
    $idUsado = 0;
    $usanExp = 0;
    $j = 0;
    $k = 0;

    //Comprobamos si en la base de datos estan usando el número de Expediente
    while ($j < $campos)
    {
        if (((($j + 1) % 25) == 0) && ($j != 0))
        {
            $sql_comp_exp = "SELECT `empr`.`id_empr`, `empr`.`empr_login`, `empr`.`empr_password`, `empr`.`empr_location` FROM `empr` WHERE (`empr`.`empr_cb`='" . $tot[$j - 24] . "')";
            //----------------------LLIUREX 19/12/2018-----------------------------------------
            //----Changed calls to database. Used pmb_mysql instead mysql_ -----------------------
            $resul_exp = @pmb_mysql_query($sql_comp_exp, $dbh);
            if (@pmb_mysql_num_rows($resul_exp) != 0)
            {
                $usanExp++;
            }
        }
        $j++;
    }

    $usanNia = 0;

    //Comprobamos si en la base de datos estan usando el NIA
    while ($k < $campos)
    {
        if (((($k + 1) % 25) == 0) && ($k != 0))
        {
            $sql_comp_nia = "SELECT `empr`.`id_empr`, `empr`.`empr_login`, `empr`.`empr_password`, `empr`.`empr_location` FROM `empr` WHERE (`empr`.`empr_cb`='" . $tot[$k] . "')";
            $resul_nia = @pmb_mysql_query($sql_comp_nia, $dbh);
            if (@pmb_mysql_num_rows($resul_nia) != 0)
            {
                //---------------------------------FIN LLIUREX 19/12/2018---------------------
                $usanNia++;

            }
        }
        $k++;
    }

    if ($usanExp > $usanNia)
    {
        $idUsado = "Exp";
    }
    else
    {
        $idUsado = "NIA";
    }

    return $idUsado;

}

//Funcion para comprobar si es posible lanzar la migración
function comprueba_migracion($dbh)
{
    global $msg;

    //1.Comprobamos si ya se ha realizado un migración anteriormente
    $name_cb = 'empr_cb_old';
    $sql_idcb_old = "SELECT idchamp from empr_custom where name='" . $name_cb . "'";
    //----------------------LLIUREX 19/12/2018-----------------------------------------
    //----Changed calls to database. Used pmb_mysql instead mysql_ -----------------------
    $id_cb_old = @pmb_mysql_query($sql_idcb_old, $dbh);
    $valor = pmb_mysql_fetch_array($id_cb_old);

    $sql_comprobacion = "SELECT * from empr_custom_values where empr_custom_champ='" . $valor['idchamp'] . "'";
    $existen_registros = @pmb_mysql_num_rows(@pmb_mysql_query($sql_comprobacion, $dbh));

    if ($existen_registros == 0)
    {

        // 2. Comprobamos si exisge el campo empr_NIA en la tabla empr
        $sql_NIA = "SELECT column_name from INFORMATION_SCHEMA.columns where table_schema='pmb' and table_name='empr' AND column_name='empr_NIA'";
        $existeNIA = @pmb_mysql_query($sql_NIA, $dbh);

        if (@pmb_mysql_num_rows($existeNIA) > 0)
        {
            $sql_alu_nia = "SELECT count(empr_cb) FROM empr WHERE empr_Tipo='A' AND NOT ISNULL(empr_NIA)";
            $alu_con_nia = pmb_mysql_fetch_row(@pmb_mysql_query($sql_alu_nia, $dbh));

            //$sql_alu_sinia="SELECT empr_cb FROM empr WHERE empr_Tipo<>'P' AND ISNULL(empr_NIA) AND (YEAR(UTC_DATE())-YEAR(empr_modif))<2";
            $sql_alu_sinnia = "SELECT count(*) from empr where (ISNULL(empr_NIA) or empr_NIA='') and ISNULL(empr_Tipo) and (YEAR(UTC_DATE())-YEAR(empr_date_expiration))<2";
            $alu_sin_nia = pmb_mysql_fetch_row(@pmb_mysql_query($sql_alu_sinnia, $dbh));

            //-----------------------------FIN LLIUREX 19/12/2018----------------------------
            //2. Comprobamos que la mayoria de los alumnos dispone del nia
            if ($alu_con_nia[0] > $alu_sin_nia[0])
            {
                //echo "<h3>$msg[usur_migr_a]</h3><br>$msg[usur_migr_b]</br><UI><ul><br>";
                //echo "<h3><center>PROCESO DE MIGRACIÓN DEL ID DE LOS ALUMNOS AL NIA DISPONIBLE</center></h3><div><b>Mediante este proceso podrá sustituir el Número de Expediente usado como id de los alumnos por el NIA</b><b><UL><li>Total alumnos con NIA: $alu_con_nia</li><li>Total de alumnos sin nia: $alu_sin_nia</li><UL></b></div><div><b><center>Para iniciar el proceso de migración haga clic aqui</b></center></div>";
                echo "<h3><center><a href=./admin.php?categ=empr&sub=migration>$msg[usur_migr_a]</a></center></h3>";

            }

        }
    }
}

/* FIN LLIUREX 24/09/2015 */

function fields_slashes($field)
{

    $que = array(
        "&",
        "<",
        ">",
        "\\",
        "/"
    );
    $por = array(
        "&amp;",
        "&lt;",
        "&gt;",
        "_",
        "_"
    );

    return addslashes(str_replace($que, $por, $field));
}

?>
