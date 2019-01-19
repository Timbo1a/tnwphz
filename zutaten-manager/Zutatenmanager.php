<?php

class Zutatenmanager{
    
    function Zutatenmanager(){
        $this->managerHeadline();
        //Initial alle Zutaten aufrufen
        $this->listWarengruppen();
        echo("<hr />");
        $this->listZutaten();
        echo ("<hr />");
        $this->listEinheiten();
        echo ("<hr />");
        $this->listRezept(1);
        echo ("<hr />");
        $this->printZutatenString(1);
    }
    
    function loadWarengruppen(){
        global $wpdb; //Wp Datenbank-Objekt
        $wg = $wpdb->get_results("SELECT * FROM ZM_Warengruppe");
        return $wg;
    }
    
    function managerHeadline(){
        //echo('<div class="postbox"><a href="#">Zutat hinzufügen</a> | </div>');
    }
    
    //Listet alle Zutaten auf
    function listZutaten(){
        $wg = $this->loadZutaten($pkWg);
        echo "<b>Zutaten</b>";
        echo("<ul>");
        
            foreach($wg as $warengruppe){
                echo('<li>(' . $warengruppe->FK_Warengruppe . ') <input type="text" name="textWarengruppe" value="' . $warengruppe->Zutat_Text . '" /> <a href="#">Löschen</a></li>');
            }
        echo('<li>(n) <input type="text" name="addWarengruppe" value="" /> <a href="#">Hinzufügen</a></li>');
        echo("</ul>");
    }
    
    function listWarengruppen(){
        $wg = $this->loadWarengruppen();
        echo "<b>Warengruppen</b>";
        echo("<ul>");
        
            foreach($wg as $warengruppe){
                echo('<li>(' . $warengruppe->PK_Warengruppe . ') <input type="text" name="textZutat" value="' . $warengruppe->Bezeichnung . '" /> <a href="#">Löschen</a></li>');
            }
        echo('<li>(n) <input type="text" name="addZutat" value="" /> <a href="#">Hinzufügen</a></li>');
        echo("</ul>");
        
    }
    
    //Wird keine Warengruppe übergeben, werden alle Zutaten mit ihren entsprechenden Warengruppen zurückgegeben.
    function loadZutaten($pkWarengruppe = null){
        global $wpdb;
        $pkCond = "";
        if($pkWarengruppe !== null){
            $pkCond = " AND PK_Zutat = " . $pkWarengruppe;
        }
        $zt = $wpdb->get_results("SELECT PK_Zutat, FK_Warengruppe, PK_Warengruppe, zt.Bezeichnung AS 'Zutat_Text', wg.Bezeichnung AS 'Warengruppe_Text' 
                                  FROM ZM_Zutat as zt, ZM_Warengruppe as wg WHERE zt.FK_Warengruppe = wg.PK_Warengruppe  ". $pkCond ."  
                                  ORDER BY zt.FK_Warengruppe, zt.PK_Zutat, zt.Bezeichnung;");
        return $zt;
    }
    
    function listRezept($id){
        global $wpdb;
        $rslt = $wpdb->get_results("SELECT * FROM ZM_Rezept_Map as m, ZM_Zutat as z, ZM_Einheit as e
                                    WHERE e.PK_Einheit = m.FK_Einheit AND
                                    z.PK_Zutat = m.FK_Zutat AND m.FK_WP_Posts_ID = ". $id);
        
        echo('<b>Am Post mit der ID ' . $id . ' hängen folgende Zutaten:</b> ');
        echo('<ul>');
            foreach ($rslt as $zutat){
                echo('<li>-' . $zutat->Menge . ' ' . $zutat->Label . ' ' . $zutat->Bezeichnung .  '(' . $zutat->Zusatz . ') </li>');
            }
        echo('</ul>');
    }
    
    
    function listEinheiten(){
        global $wpdb;
        //$myposts = get_posts('');
        $rslt = $wpdb->get_results("SELECT * FROM ZM_Einheit");
        echo ("In der Datenbank vorhanden Einheiten:");
        echo('<ul>');
        
            foreach($rslt as $einheit){
                echo('<li>' . $einheit->Typ. ' - ' . $einheit->Label . '</li>');
            }
        
        echo('</ul>');
    }
    
    function deleteZutat($id){
        
    }
    
    function addZutat($name, $usw){
        
    }
    function printZutatenString(){
       echo Zutatenmanager::buildRezeptString(1, true);
    }
   public static function buildRezeptString($postID =-1, $htmlMode=false){
        global $wpdb;
        if ($postID == -1) return "";
        
        $arrRezept = $wpdb->get_results("SELECT * FROM ZM_Einheit AS zme, ZM_Rezept_Map AS zmrm, ZM_Zutat AS zmz WHERE
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

            $obLine .= $zutat->Menge . " " . $zutat->Label . " " . $zutat->Bezeichnung . ($zutat->Zusatz == "" ? " " : " (". $zutat->Zusatz .")") . ($htmlMode ? "<br/>" :"\r\n");
           // print_r($zutat);
            $obString .= $obLine;
            
            $gruppeNMinus1 = $zutat->Gruppe;
            
            
        }		
            return $obString;	
    }
    
}



// class Einheit{
//     //const einheiten = array("mg", "g", "kg", "ml", "l");
    
//     var $label = "";
//     var $typ = "";
    
//     public function Einheit($id){
        
//     }
    
//     public static function getAllEinheiten(){
//         global $wpdb;
//         $einheiten = array();
//         $rsltEinheiten = $wpdb->get_results("SELECT * FROM ZM_Einheit");
//         print_r($rsltEinheiten);
//         $i=0;
//         foreach($rsltEinheiten as $einheit){
//             $tmpEinheit = new Einheit();
//             $tmpEinheit->typ = $einheit["Typ"];
//             $tmpEinheit->label = $einheit['Label'];
            
//             if($tmpEinheit->typ == Einheit::einheiten['mg']){
//                 print_r($tmpEinheit);
//             }
            
//             echo $einheit['Label'];
//             $i++;
//         }
       
//     }
// }


