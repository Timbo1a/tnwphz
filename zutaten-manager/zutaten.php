<?php
/*
Plugin Name: Zutatenverwaltung
Plugin URI: https://herdzeit.de/
Description: Ein Plugin zur Zutatenverwaltung mit eigener Datenhaltung.
Version: 0.0.1
Author: Herdzeit
Author URI: Herdzeit.de
License: 
Text Domain: zutaten
*/
require_once 'Zutatenmanager.php';

//Diese Aktionen registrieren "hooks/fiter" auf die Funktionen die im 2. Parameter stehen
//add_filter('the_content', 'hello_world'); //Content eines Beitrags manipulieren
//add_action('wp_head', 'add_to_head'); //Fügt Inhalt dem Headerbereich auf der Seite (nicht Admin) hinzu
add_action('admin_menu', 'zutaten_management');

//Wird für jeden Beitrag, bzw. dessen Inhalt ausgeführt.
// function hello_world($content) {
//     return strtoupper($content); //Macht den gesamten Text eines Posts to upper
// }

//Fügt Inhalt dem Headerbereich auf der Seite (nicht Admin) hinzu
// function add_to_head(){
//     echo ('<script type="text/javascript">alert("tito");</script>');
// }
//Fügt im Admin Menü den Punkt Zutaten-Manager hinzu und ruft die Funktion zutatenManagerInit auf, bei einem Klick auf diesen
function zutaten_management(){
    add_menu_page('Zutatenmanagement', 'Zutaten-Manager', 'manage_options', 'zutaten', 'zutatenManagerInit');    
}
 
//Die Funktion, die inital im Backend aufgerufen wird, wenn man den Punkt "Zutaten-Manager" auswählt
function zutatenManagerInit(){
//     global $wpdb;
//     listWarengruppen();
    new Zutatenmanager(); //-> siehe Zutatenmanager.php
}

// function listWarengruppen(){
//     foreach(loadWarengruppen() as $warengruppe){
//         echo $warengruppe;
//     }
//     echo("<ul>");
    
//     echo('<li>Zutat 1 <a href="#">Löschen</a></li>');
    
//     echo("</ul>");
// }

// function loadWarengruppen(){
//     global $wpdb;
//     $wg = $wpdb->get_results("SELECT * FROM ZM_Warengruppe");
//     print_r($wg);
//     return $wg;
// }



?>