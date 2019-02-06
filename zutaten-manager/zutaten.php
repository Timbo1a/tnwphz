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

function pw_load_scripts($hook) {
    if( $hook != 'toplevel_page_zutaten' ) return;
    
    wp_enqueue_script( 'zm-dataTables', plugins_url( 'zutaten/js/zm-dt.js' , dirname(__FILE__) ) );
    //TODO: Eigentlich werden die Skripte bereits geladen...
    //wp_enqueue_script( 'zm-jquery-ui', plugins_url( 'zutaten/js/zm-jquery-ui.js' , dirname(__FILE__) ) );
    //wp_enqueue_script( 'zm-jquery', plugins_url( 'zutaten/js/zm-jquery.js' , dirname(__FILE__) ) );
    wp_enqueue_script( 'zm-custom', plugins_url( 'zutaten/js/zm-custom.js' , dirname(__FILE__) ) );
}
add_action('admin_enqueue_scripts', 'pw_load_scripts');

function admin_style($hook) {
    //     if( $hook != 'toplevel_page_zutaten' ) return
    
    wp_enqueue_style('admin-styles', plugins_url( 'zutaten/css/dt.css' , dirname(__FILE__) ) );
}
add_action('admin_enqueue_scripts', 'admin_style');

//Fügt im Admin Menü den Punkt Zutaten-Manager hinzu und ruft die Funktion zutatenManagerInit auf, bei einem Klick auf diesen
function zutaten_management(){
    add_menu_page('Zutatenmanagement', 'Zutaten-Manager', 'manage_options', 'zutaten', 'zutatenManagerInit');
}
//Diese Aktionen registrieren "hooks/fiter" auf die Funktionen die im 2. Parameter stehen
//add_filter('the_content', 'hello_world'); //Content eines Beitrags manipulieren
//add_action('admin_head', 'add_to_head'); //Fügt Inhalt dem Headerbereich auf der Seite (nicht Admin) hinzu
add_action('admin_menu', 'zutaten_management');

function zmAjaxHandler() {
    global $wpdb; // this is how you get access to the database
    
    $whatever = intval( $_POST['whatever'] );
    switch($_POST['function']){
        
        case "loadProductGroups":
            echo ajaxLoadProductGroups();
            break;
        case "zmAllIngredients":
            echo ajaxLoadAllIngredients();
            break;
        case "addIngredient":
            echo ajaxAddIngredient();
            break;
        case "deleteIngredient":
            echo ajaxDeleteIngredient();
            break;
    }
    
    wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_zmAJAX', 'zmAjaxHandler' );

//Die Funktion, die inital im Backend aufgerufen wird, wenn man den Punkt "Zutaten-Manager" auswählt
function zutatenManagerInit(){
?>
    <div id="tabs">
    	<ul>
    		<li><a href="#tabs-1">Zutaten</a>
    		<li><a href="#tabs-2">Warengruppen</a>
    		<li><a href="#tabs-3">Einheiten</a>
    	</ul>
    
    	<div id="tabs-1"><?php include 'zutaten_overview.php'; ?></div>
    	<div id="tabs-2"><?php include 'warengruppen_overview.php'; ?></div>
    	<div id="tabs-3"><?php echo "Einheiten"; ?></div>
    </div>
<?php 
    
}


//    AJAX Kram erstmal hier..
function ajaxLoadProductGroups(){return json_encode(Zutatenmanager::loadWarengruppen());}
function ajaxLoadAllIngredients(){
    global $wpdb;
    return '{"data":'.json_encode($wpdb->get_results("SELECT PK_Zutat, FK_Warengruppe, Bezeichnung, Energie_KJ, Fett,
  Fett_gesaettigt, Kohlenhydrate, Kohlenhydrate_Zucker, Eiweiss, Salz, Einheit, immer_zuhause, created,
  (SELECT COUNT(FK_Zutat) FROM ".$wpdb->prefix."ZM_Rezept_Map WHERE FK_Zutat = PK_Zutat) as 'referenzen' FROM ".$wpdb->prefix."ZM_Zutat")).'}';
}
function ajaxAddIngredient(){
    return Zutatenmanager::addZutat($_POST['bezeichnung'],$_POST['FK_Warengruppe']);
}
function ajaxDeleteIngredient(){
    return Zutatenmanager::deleteIngredient($_POST['id']);
}
///   //AJAX



function createZutatenManagerMetabox() {add_meta_box('idZutatenManagerMetabox', 'Zutaten Manager 2.0', 'contentZutatenManagerMetaBox', 'recipe', 'normal', 'default');}
add_action( 'add_meta_boxes', 'createZutatenManagerMetabox' );

function contentZutatenManagerMetaBox(){
    global $post;
    //$id = $post->ID;
    //print_r($post);
    ?>
<script type="text/javascript">
function zmAutoFill(){
	$.post( ajaxurl, { action: "zmAJAX", function: "zmAllIngredients" })
	.done(function( data ) {
		$('#ZMJsonDataList').empty();
		var response = $.parseJSON(data);
		$(response.data).each(function(){
			$('#ZMJsonDataList').append("<option value=\""+this.Bezeichnung+"\">");
		});
	});		
}
$(document).ready(function () {
	zmAutoFill();
});

</script>

<datalist id="ZMJsonDataList"><option value="Value1"></datalist>
<input type="text" id="ZMDropdown" list="ZMJsonDataList">
    
    <?php 
}
?>
