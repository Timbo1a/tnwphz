<?php

class Zutatenmanager{
    //Erstmal nur statische Methodenb
    function __construct(){
    }
    
    static function loadWarengruppen(){
        global $wpdb; //Wp Datenbank-Objekt
        $wg = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ZM_Warengruppe");
        return $wg;
    }
    
    //Wird keine Warengruppe übergeben, werden alle Zutaten mit ihren entsprechenden Warengruppen zurückgegeben.
    function loadZutaten($pkWarengruppe = null){
        global $wpdb;
        $pkCond = "";
        if($pkWarengruppe !== null){
            $pkCond = " AND PK_Zutat = " . $pkWarengruppe;
        }
        $zt = $wpdb->get_results("SELECT PK_Zutat, FK_Warengruppe, PK_Warengruppe, zt.Bezeichnung AS 'Zutat_Text', wg.Bezeichnung AS 'Warengruppe_Text'
                                  FROM ".$wpdb->prefix."ZM_Zutat as zt, ".$wpdb->prefix."ZM_Warengruppe as wg WHERE zt.FK_Warengruppe = wg.PK_Warengruppe  ". $pkCond ."
                                  ORDER BY zt.FK_Warengruppe, zt.PK_Zutat, zt.Bezeichnung;");
        return $zt;
    }
    
   
    //Zutat mit PK löschen. TODO: Beziehungen prüfen.
    function deleteIngredient($id){
        global $wpdb;
        $data = "Select from ".$wpdb->prefix."ZM_Zutat WHERE id = $id";
        $sql = "DELETE FROM ".$wpdb->prefix."ZM_Zutat WHERE name = $data";
    }
    
    static function addZutat($name, $fk_warengruppe){
        global $wpdb;
        $table = $wpdb->prefix.'ZM_Zutat';
        $data = array('FK_Warengruppe' => $fk_warengruppe, 'Bezeichnung' => $name);
        //$format = array('%d','%s');
        $wpdb->insert($table,$data,$format);
        return $my_id = $wpdb->insert_id;
    }

    //Baut den RezepteString
    public static function buildRezeptString($postID =-1, $htmlMode=false){
        global $wpdb;
        if ($postID == -1) return "";
        
        $arrRezept = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ZM_Einheit AS zme, ".$wpdb->prefix."ZM_Rezept_Map AS zmrm, ".$wpdb->prefix."ZM_Zutat AS zmz WHERE
									zme.PK_Einheit = zmrm.FK_Einheit AND
									zmz.PK_Zutat = zmrm.FK_Zutat AND
									zmrm.FK_WP_Posts_ID = ".$postID."
									ORDER BY Gruppe asc");
        
        $obString = "";
        $gruppeNMinus1 = "";
        foreach($arrRezept as $zutat){
            $obLine = "";
            
            //Hier wird von folgendem Verhalten ausgegangen: Die Liste der Zutaten kommt nach Gruppe sortiert asc/desc. Sobald der erste String auftaucht, wird dieser angezeigt. Ändert sich der String, wird auch der neue wieder einmal angezeigt in der Form #String#.
            if($gruppeNMinus1 !== $zutat->Gruppe && !$zutat->Gruppe == ""){ //TODO: Null-Verhalten != "" und Sortierung checken
                $obLine .= "#" . $zutat->Gruppe . "#" . ($htmlMode ? "<br/>" :"\r\n"); //Ändert sich die Gruppe, schreibe diese zusätzlich als eigene Zeile einmalig davor
            }
            
            $obLine .= round($zutat->Menge, 2) . " " . $zutat->Label . " " . $zutat->Bezeichnung . ($zutat->Zusatz == "" ? " " : " (". $zutat->Zusatz .")") . ($htmlMode ? "<br/>" :"\r\n");
            // print_r($zutat);
            $obString .= $obLine;
            
            $gruppeNMinus1 = $zutat->Gruppe;
            
            
        }
        return $obString;
    }
    
    //Rezept als Objekt
    public static function getRezept($postID = -1){
        global $wpdb;
        if ($postID == -1) return "";
        
        $arrRezept = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ZM_Einheit AS zme, ".$wpdb->prefix."ZM_Rezept_Map AS zmrm, ".$wpdb->prefix."ZM_Zutat AS zmz WHERE
									zme.PK_Einheit = zmrm.FK_Einheit AND
									zmz.PK_Zutat = zmrm.FK_Zutat AND
									zmrm.FK_WP_Posts_ID = ".$postID."
									ORDER BY Gruppe asc");
        return $arrRezept;
    }
    
}

