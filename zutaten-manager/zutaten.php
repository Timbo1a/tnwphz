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

/*
TODO: Vernünftige Ein- und Ausgabevalidierung, um XSS vorzugebeugen. Besonders die JSON-Kommunikation muss noch vernünftig kodiert werden.
*/

//Admin Page
function pw_load_scripts($hook) {
    if( $hook == 'toplevel_page_zutaten' ){
        //TODO: Eigentlich werden die Skripte bereits geladen...
        wp_enqueue_script( 'zm-jquery', plugins_url( 'zutaten/js/zm-jquery3.js?'.rand(1,99)  , dirname(__FILE__) ) );
        //wp_enqueue_script( 'zm-jquery-ui', plugins_url( 'zutaten/js/zm-jquery-ui.js?'.rand(1,99)  , dirname(__FILE__) ) );
        wp_enqueue_script( 'zm-dataTables', plugins_url( 'zutaten/js/zm-dt.js?'.rand(1,99)  , dirname(__FILE__) ) );
        wp_enqueue_script( 'zm-dataTables', plugins_url( 'zutaten/js/zm-dt-buttons.js?'.rand(1,99)  , dirname(__FILE__) ) );
        wp_enqueue_style('admin-styles', plugins_url( 'zutaten/css/dt.css?'.rand(1,99)  , dirname(__FILE__) ) );
        //wp_enqueue_script( 'zm-custom', plugins_url( 'zutaten/js/zm-dt-responsive.js?'.rand(1,99) , dirname(__FILE__) ) );
        wp_enqueue_script( 'zm-custom', plugins_url( 'zutaten/js/zm-custom.js?'.rand(1,99) , dirname(__FILE__) ) );
    }
    
}
add_action('admin_enqueue_scripts', 'pw_load_scripts');

// function admin_style($hook) {
//     error_log("padmin_style");
//     //     if( $hook != 'toplevel_page_zutaten' ) return
//     wp_enqueue_style('admin-styles', plugins_url( 'zutaten/css/dt.css' , dirname(__FILE__) ) );
// }
// //add_action('admin_enqueue_scripts', 'admin_style');
// add_action('admin_enqueue_scripts', 'admin_post');

//Rezepte Aufruf
function recipe_admin_script() {
    global $post_type;
    if( 'recipe' == $post_type ){
        wp_enqueue_script( 'zm-selectPlugin', plugins_url( 'zutaten/js/select2Plugin.min.js?'.rand(1,99)  , dirname(__FILE__) ) );
        wp_enqueue_script( 'zm-custom-post', plugins_url( 'zutaten/js/zm-custom-post.js?'.rand(1,99)  , dirname(__FILE__) ) );
        wp_enqueue_style('admin-styles', plugins_url( 'zutaten/css/zmIng.css?'.rand(1,99)  , dirname(__FILE__) ) );
    }
}
add_action( 'admin_print_scripts-post-new.php', 'recipe_admin_script' );
add_action( 'admin_print_scripts-post.php', 'recipe_admin_script' );

//Fügt im Admin Menü den Punkt Zutaten-Manager hinzu und ruft die Funktion zutatenManagerInit auf, bei einem Klick auf diesen
function zutaten_management(){
    add_menu_page('Zutatenmanagement', 'Zutaten-Manager', 'manage_options', 'zutaten', 'zutatenManagerInit');
}
//Diese Aktionen registrieren "hooks/fiter" auf die Funktionen die im 2. Parameter stehen
//add_filter('the_content', 'hello_world'); //Content eines Beitrags manipulieren
//add_action('admin_head', 'add_to_head'); //Fügt Inhalt dem Headerbereich auf der Seite (nicht Admin) hinzu
add_action('admin_menu', 'zutaten_management');


//action on save
add_action( 'save_post', 'zmSavePostRecipe', 10, 3 );
function zmSavePostRecipe( $post_ID, $post, $update ) {
    global $post_type, $wpdb;
    if($post_type == "recipe"){
        error_log(print_r($post, true));
        
        //TODO: Erstmal alle Zutaten löschen und neu anlegen. Später: abgleich bestehender Daten und nur aktualisieren. Löschen nach FK ist auch nicht optimal. Zusammenfassung der Update Methode
        $wpdb->query("DELETE FROM ".$wpdb->prefix."ZM_Rezept_Map WHERE FK_WP_Posts_ID = " .$post->ID );
        //TODO: Etwas rudimentär und unperformant. Wird später noch optimiert. Vor Allem muss noch dringend parametrisiert und plausibilisiert werden
        foreach($_POST['zmZutat'] as $key => $zutat){
            //Leere Werte noch zu null Werten für die DB
            $wpdb->query("INSERT INTO ".$wpdb->prefix."ZM_Rezept_Map (FK_Zutat, FK_WP_Posts_ID, FK_Einheit, Menge, Zusatz, Gruppe) VALUES (".esc_html($_POST['zmZutat'][$key]).", ".$post->ID.",".esc_html($_POST['zmEinheit'][$key]).", '".esc_html($_POST['zmMenge'][$key])."', '".esc_html($_POST['zmZusatz'][$key])."', '".esc_html($_POST['zmGruppe'][$key])."')");
        }
    }
    
}

function zmAjaxHandler() {
    global $wpdb; // this is how you get access to the database
    header("Content-Type: application/json; charset=utf-8");
   
    switch($_POST['function']){
        
        case "loadProductGroups":
            echo ajaxLoadProductGroups();
            break;
        case "loadProductUnits":
            echo ajaxLoadProductUnits();
            break;
        case "zmAllIngredients":
            echo ajaxLoadAllIngredients(esc_attr($_POST['term']));
            break;
        case "zmSimpleAjaxLoadAllIngredients":
            echo simpleAjaxLoadAllIngredients(esc_attr($_POST['term']));
            break;
        case "addIngredient":
            echo ajaxAddIngredient();
            break;
        case "deleteIngredient":
            echo ajaxDeleteIngredient();
            break;
        case "updateIngredient":
            echo ajaxUpdateIngredient();
            break;
            
    }
    
    wp_die(); // this is required to terminate immediately and return a proper response
}
add_action( 'wp_ajax_zmAJAX', 'zmAjaxHandler' );



//Die Funktion, die inital im Backend aufgerufen wird, wenn man den Punkt "Zutaten-Manager" auswählt
function zutatenManagerInit(){
    global $wp;
    if ($_GET['migration'] == "true"){
        migration();
    }else{
        ?>
        <div id="">
            <p>
        		<a href="admin.php?page=zutaten&zm-function=zutaten_overview"><b>Zutaten</b></a> | 
        		<a href="admin.php?page=zutaten&zm-function=warengruppen_overview"><b>Warengruppen</b></a> | 
        		<a href="admin.php?page=zutaten&zm-function=einheiten_overview"><b>Einheiten</b></a>
        	</p>
            <hr />
            <?php
                switch($_GET['zm-function']){
                    case "warengruppen_overview":
                        include "warengruppen_overview.php";
                        break;
                    case "einheiten_overview":
                        include "einheiten_overview.php";
                        break;    
                    default:
                        include "zutaten_overview.php";
                }
            ?>
        </div>
        
<?php 
    }
}


//    AJAX Kram erstmal hier..
function ajaxLoadProductGroups(){return '{"data":'.wp_json_encode(Zutatenmanager::loadWarengruppen()).'}';}
function ajaxLoadAllIngredients(){
    global $wpdb;
    return '{"data":'.wp_json_encode($wpdb->get_results("SELECT PK_Zutat, FK_Warengruppe, Bezeichnung, Energie_KJ, Fett,
  Fett_gesaettigt, Kohlenhydrate, Kohlenhydrate_Zucker, Eiweiss, Salz, Einheit, immer_zuhause, created,
  (SELECT COUNT(FK_Zutat) FROM ".$wpdb->prefix."ZM_Rezept_Map WHERE FK_Zutat = PK_Zutat) as 'referenzen' FROM ".$wpdb->prefix."ZM_Zutat")).'}';
}
function ajaxLoadProductUnits(){
    //TODO
}
function simpleAjaxLoadAllIngredients($bezeichnung = ""){
    global $wpdb;
    if (!$bezeichnung == "") $add = " WHERE Bezeichnung LIKE '" . esc_html($bezeichnung) . "%'";
    return '{"data":'.wp_json_encode($wpdb->get_results("SELECT PK_Zutat, FK_Warengruppe, Bezeichnung, Energie_KJ, Fett,
        Fett_gesaettigt, Kohlenhydrate, Kohlenhydrate_Zucker, Eiweiss, Salz, Einheit, immer_zuhause, created FROM ".$wpdb->prefix."ZM_Zutat " .$add)).'}';
}
function ajaxAddIngredient(){
    return '{"data":'.wp_json_encode(Zutatenmanager::addZutat(esc_html($_POST['bezeichnung']),esc_html($_POST['FK_Warengruppe']))).'}';
}
function ajaxDeleteIngredient(){
    return '{"data":'.wp_json_encode(Zutatenmanager::deleteIngredient(esc_html($_POST['id']))).'}';
}
function ajaxUpdateIngredient(){
    $tmpPost = array();
    $tmpPost['data'] = array(
        'PK_Zutat' => urldecode($_POST['data']['PK_Zutat']),
        'FK_Warengruppe' => urldecode($_POST['data']['FK_Warengruppe']),
        'Bezeichnung' => urldecode($_POST['data']['Bezeichnung']),
        'Energie_KJ' => urldecode($_POST['data']['Energie_KJ']),
        'Fett_gesaettigt' => urldecode($_POST['data']['Fett_gesaettigt']),
        'Kohlenhydrate' => urldecode($_POST['data']['Kohlenhydrate']),
        'Kohlenhydrate_Zucker' => urldecode($_POST['data']['Kohlenhydrate_Zucker']),
        'Eiweiss' => urldecode($_POST['data']['Eiweiss']),
        'Salz' => urldecode($_POST['data']['Salz']),
        'Einheit' => urldecode($_POST['data']['Einheit']),
        'immer_zuhause' => urldecode($_POST['data']['immer_zuhause']),
        'created' => urldecode($_POST['data']['created'])
    );
    return '{"data":'.wp_json_encode(Zutatenmanager::updateIngredient($tmpPost['data'])).'}';
}
///   //AJAX

function createZutatenManagerMetabox() {add_meta_box('idZutatenManagerMetabox', 'Zutaten Manager 2.0', 'contentZutatenManagerMetaBox', 'recipe', 'normal', 'default');}
add_action( 'add_meta_boxes', 'createZutatenManagerMetabox' );

function contentZutatenManagerMetaBox(){
    global $post;
    $id = $post->ID;
    //print_r($post);
    
    global $wpdb;
    //get ingredients for this post
    $oRezept = Zutatenmanager::getRezept($id);
    $units = Zutatenmanager::GetEinheiten();
    ?>


    <label for="js-example-basic-single">
        Anklicken um eine Zutat auszuwählen:<br/>
    <select class="js-example-basic-single" name="state"></select><button id="zmAddIngredientToRecipe">Dem Rezept hinzufügen</button>
    </label>
    <?php 
    $unitDDBuffer = "";
    $i=0;
        $unitDDBuffer = '<select name="zmEinheit[%nRows%]">';
        foreach($units as $unit){
            $unitDDBuffer .= '<option value="'.$unit->PK_Einheit.'">'.esc_html($unit->Typ).'</option>';
        }
        $unitDDBuffer .= "</select>";
    ?>
    <input type="hidden" id="zmHiddenSelectBoxForAddFunction" value="<?php echo base64_encode($unitDDBuffer); ?>" />
    <!-- Rezeptegruppenverwaltung -->
    <!-- Für später
    <br/>
    <select class="selectboxMulti" id="zmSelectboxMulti" name="states[]" multiple="multiple">
        <option value="AL">Für den Nachtisch</option>
        <option value="WY">Für das Dressing</option>
    </select><br />
    <input type="text" value="asd" id="zmSubRecipeGroupItemName" /><button onclick="javascript:addSubRecipeGroupItem($('#zmSubRecipeGroupItemName').val())">Add</button>
    -->
    <!-- //Rezeptegruppenverwaltung -->
    <form>
        <table id="ZMIngredientTable">
            <tr><th>Menge</th><th>Einheit</th><th>Zutat</th><th>Zusatzbeschreibung</th><th>Gruppe</th><th>Aktion</th></tr>
            <?php 
            $i=0;
            $unitDDBuffer = "";
            foreach($oRezept as $oIngredient){
                $unitDDBuffer = '<select name="zmEinheit['.$i.']">';
                foreach($units as $unit){
                    $unitDDBuffer .= '<option '.($unit->PK_Einheit == $oIngredient->FK_Einheit ? "selected":"").' value="'.$unit->PK_Einheit.'">'.esc_html($unit->Typ).'</option>';
                }
                $unitDDBuffer .= "</select>";
            
                echo ('<tr>
                    <td><input size="5" type="text" value="'.$oIngredient->Menge.'" name="zmMenge['.$i.']" /></td>
                    <td>'.$unitDDBuffer.'</td>
                    <td><input type="hidden" value="'.$oIngredient->PK_Zutat.'" name="zmZutat['.$i.']" /><b>'.$oIngredient->Bezeichnung.'</b></td>
                    <td><input type="text" name="zmZusatz['.$i.']" value="'.$oIngredient->Zusatz.'" /></td>
                    <td><input type="text" name="zmGruppe['.$i.']" value="'.$oIngredient->Gruppe.'" /></td>
                    <td><a href="javascript:void(0);" onClick="$(this).parent().parent().remove();" id="zmDeleteRowLink">Löschen</a></td>
                </tr>');
                $i++;
            }
            
            ?>
        </table>
    </form>
    <input type="hidden" id="hiddenSelectBoxForAddFunction" value="<?php echo base64_encode($unitDDBuffer) ?>" />
        <?php 
    }

        //Temporäre Fkt für die Migration
        function migration(){
            global $wpdb;
            $postmeta = $wpdb->get_results("SELECT * FROM wp_postmeta WHERE meta_key = 'recipe_ingredient';");
            
            echo('<table style="border:1px">');
            echo ('<tr><th>id</th><th>Postmeta</th><th>Migriert</th></tr>');
            
            foreach($postmeta as $meta){
                //$wpdb->query("INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES (".$meta->post_id.", 'migrated', 'true')");
                $ids = $wpdb->get_results("SELECT * FROM wp_postmeta WHERE meta_key = 'migrated' AND post_id = " .$meta->post_id);
                $newRecipe = Zutatenmanager::buildRezeptString($meta->post_id);
                echo ('<tr><td><b>'.$meta->post_id.'</b></td><td><pre>'.$meta->meta_value.'</pre></td><td><pre>'.$newRecipe.'</td></tr>');
            }
            
    echo('</table>');
    
}

?>