<?php
/*
 * TODO:
 * Prepared statements gegen SQL-Injections
 */
class Zutatenmanager{
    //Erstmal nur statische Methodenb
    function __construct(){
    }
    
    static function loadWarengruppen(){
        global $wpdb; //Wp Datenbank-Objekt
        $wg = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ZM_Warengruppe");
        return $wg;
    }
    
    //Wird keine Warengruppe Ã¼bergeben, werden alle Zutaten mit ihren entsprechenden Warengruppen zurÃ¼ckgegeben.
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
    
    
    //Zutat mit PK lÃ¶schen. TODO: Beziehungen prÃ¼fen.
    static function deleteIngredient($id){
        global $wpdb;
        $wpdb->delete( $wpdb->prefix."ZM_Zutat", array( 'PK_Zutat' => $id ));
        //False on error
        if($result == false){
            return false;
        }
        return true;
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
            
            //Hier wird von folgendem Verhalten ausgegangen: Die Liste der Zutaten kommt nach Gruppe sortiert asc/desc. Sobald der erste String auftaucht, wird dieser angezeigt. Ã„ndert sich der String, wird auch der neue wieder einmal angezeigt in der Form #String#.
            if($gruppeNMinus1 !== $zutat->Gruppe && !$zutat->Gruppe == ""){ //TODO: Null-Verhalten != "" und Sortierung checken
                $obLine .= "#" . $zutat->Gruppe . "#" . ($htmlMode ? "<br/>" :"\r\n"); //Ã„ndert sich die Gruppe, schreibe diese zusÃ¤tzlich als eigene Zeile einmalig davor
            }
            
            $obLine .= round($zutat->Menge, 2) . " " . $zutat->Label . " " . $zutat->Bezeichnung . ($zutat->Zusatz == "" ? " " : " ". $zutat->Zusatz ."") . ($htmlMode ? "<br/>" :"\r\n");
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
    
    public static function getEinheiten(){
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."ZM_Einheit ORDER BY PK_Einheit");
    }
    
}
