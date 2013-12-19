<?php
// Built to live inside assets/mycomponents/xxxxx/ :
require_once '../../../config.core.php';
require_once MODX_CORE_PATH . 'config/config.inc.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('mgr');


// http://rtfm.modx.com/display/revolution20/Creating+a+Resource+Class
//------------------------------------------------------------------------------
//! CONFIGURATION
//------------------------------------------------------------------------------
// Your package shortname:
$package_name = 'survey';



// Set this to false if you've started to customize the PHP classes, otherwise
// your changes will be overwritten!
$regenerate_classes = true;
 
//$adjusted_core_path = MODX_CORE_PATH;
$adjusted_core_path = MODX_BASE_PATH.'assets/mycomponents/survey/core/';

//------------------------------------------------------------------------------
//  DO NOT TOUCH BELOW THIS LINE
//------------------------------------------------------------------------------
 
if (!defined('MODX_CORE_PATH')) {
    print_msg('<h1>Parsing Error</h1>
        <p>MODX_CORE_PATH not defined! Did you include the correct config file?</p>');
    exit;
}


$xpdo_path = strtr(MODX_CORE_PATH . 'xpdo/xpdo.class.php', '\\', '/');
include_once ( $xpdo_path );
  
// A few definitions of files/folders:
$package_dir = $adjusted_core_path . "components/$package_name/";
$model_dir = $adjusted_core_path . "components/$package_name/model/";
$class_dir = $adjusted_core_path . "components/$package_name/model/$package_name";
$schema_dir = $adjusted_core_path . "components/$package_name/model/schema";
$mysql_class_dir = $adjusted_core_path . "components/$package_name/model/$package_name/mysql";
$xml_schema_file = $adjusted_core_path . "components/$package_name/model/schema/$package_name.mysql.schema.xml";
  
// A few variables used to track execution times.
$mtime= microtime();
$mtime= explode(' ', $mtime);
$mtime= $mtime[1] + $mtime[0];
$tstart= $mtime;
  
// Validations
if ( empty($package_name) ) {
    print_msg('<h1>Parsing Error</h1>
        <p>The $package_name cannot be empty!  Please adjust the configuration and try again.</p>');
    exit;
}
  
// Create directories if necessary
$dirs = array($package_dir, $schema_dir ,$mysql_class_dir, $class_dir);
  
foreach ($dirs as $d) {
    if ( !file_exists($d) ) {
        if ( !mkdir($d, 0777, true) ) {
            print_msg( sprintf('<h1>Parsing Error</h1>
                <p>Error creating <code>%s</code></p>
                <p>Create the directory (and its parents) and try again.</p>'
                , $d
            ));
            exit;
        }
    }
    if ( !is_writable($d) ) {
        print_msg( sprintf('<h1>Parsing Error</h1>
            <p>The <code>%s</code> directory is not writable by PHP.</p>
            <p>Adjust the permissions and try again.</p>'
        , $d));
        exit;
    }
}
  
 
print_msg( sprintf('<br/><strong>Ok:</strong> The necessary directories exist and have the correct permissions inside of <br/>
        <code>%s</code>', $package_dir));
  
if (file_exists($xml_schema_file)) {
    print_msg( sprintf('<br/><strong>Ok:</strong> Using existing XML schema file:<br/><code>%s</code>',$xml_schema_file));
}
 
$xpdo = new xPDO("mysql:host=$database_server;dbname=$dbase", $database_user, $database_password);

$xpdo->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
 
$manager = $xpdo->getManager();
$generator = $manager->getGenerator();
 
// Use this to generate classes and maps from your schema
if ($regenerate_classes) { 
    print_msg('<br/>Attempting to remove/regenerate class files...');
    delete_class_files($class_dir);
    delete_class_files($mysql_class_dir);

}

print "<br/>Parsing schema $xml_schema_file <br/>";
$generator->parseSchema($xml_schema_file,$model_dir);
$generator->parseSchema($adjusted_core_path . "components/$package_name/model/schema/foxycart.mysql.schema.xml",$model_dir);

if(!$xpdo->addPackage('survey',$adjusted_core_path.'components/survey/model/','survey_')) {
    return 'Package Error.';
}




// Clear out Tables
print '<h3>Dropping Tables...</h3>';
//$xpdo->setLogLevel(xPDO::LOG_LEVEL_FATAL); // keep it quiet
$manager->removeObjectContainer('Survey');
$manager->removeObjectContainer('Question');
$manager->removeObjectContainer('Response');
$manager->removeObjectContainer('Data'); 


print '<code>Done.</code>';
/*
print '<h3>Removing Taxonomy and Term Records on modx_site_content Tables...</h3>';
//Removing Taxonomies
$taxonomies = $modx->getCollection('Taxonomy',array('class_key'=>'Taxonomy'));
if($taxonomies) {
    foreach ($taxonomies as $page) {
        if ($page->remove() == false) {
           echo 'An error occurred while trying to remove Taxonomy ' . $page->get('pagetitle') . '<br>';
        } else {
             echo $page->get('pagetitle') . ' Taxonomy Deleted. <br>' ;
        }
    }
}
//Removing Terms
$terms = $modx->getCollection('Term',array('class_key'=>'Term'));
if($terms) {
    foreach ($terms as $page) {
        if ($page->remove() == false) {
           echo 'An error occurred while trying to remove Term ' . $page->get('pagetitle') . '<br>';
        } else {
             echo $page->get('pagetitle') . ' Term Deleted. <br>' ;
        }
    }
}
*/


// Re-create them
$xpdo->setLogLevel(xPDO::LOG_LEVEL_WARN);
print '<h3>Creating Tables...</h3>';
$manager->createObjectContainer('Survey');
$manager->createObjectContainer('Question');
$manager->createObjectContainer('Response');
$manager->createObjectContainer('Data');
print '<code>Done.</code>';

//$product_taxonomies = include $data_src_dir . 'transport.product_taxonomies.php';

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);
 
print_msg("<br/><br/><strong>Finished!</strong> Execution time: {$totalTime}<br/>");
print_msg("<br/>Check <code>$class_dir</code> for your newly generated class files!"); 
exit();
 
//------------------------------------------------------------------------------
//! FUNCTIONS
//------------------------------------------------------------------------------
/**
 * Deletes the MODX class files in a given directory.
 *
 * @param string $dir: full path to directory containing class files you wish to delete.
 * @return void
 */
function delete_class_files($dir) {
    global $verbose;
  
    $all_files = scandir($dir);
    foreach ( $all_files as $f ) {

        if ( preg_match('#\.class\.php$#i', $f) || preg_match('#\.map\.inc\.php$#i', $f)) {
            if (in_array(basename($f),array('moxycart.class.php','taxonomyparents.class.php',
                'termparents.class.php','store.class.php','taxonomy.class.php','term.class.php'
                ,'moxycart.snippets.class.php','rc4crypt.class.php','foxycartdatafeed.class.php'))) {
                continue; // skip
            } 

            if ( unlink("$dir/$f") ) {
                if ($verbose) {
                    print_msg( sprintf('<br/>Deleted file: <code>%s/%s</code>',$dir,$f) );
                }
            }
            else {
                print_msg( sprintf('<br/>Failed to delete file: <code>%s/%s</code>',$dir,$f) );
            }
        }
    }
}
 
/**
 * Formats/prints messages. HTML is stripped if this is run via the command line.
 *
 * @param string $msg to be printed
 * @return void this actually prints data to stdout
 */
function print_msg($msg) {
    if ( php_sapi_name() == 'cli' ) {
        $msg = preg_replace('#<br\s*/>#i', "\n", $msg);
        $msg = preg_replace('#<h1>#i', '== ', $msg);
        $msg = preg_replace('#</h1>#i', ' ==', $msg);
        $msg = preg_replace('#<h2>#i', '=== ', $msg);
        $msg = preg_replace('#</h2>#i', ' ===', $msg);
        $msg = strip_tags($msg) . "\n";
    }
    print $msg;
}

/**
 * Insert Records on respective tables
 *
 * @param array $items to be printed
 * @param string $tbl to be printed
 * @param string $crc to be printed
 * @return print message
 */
function insert_records($items, $crc) {
    global $modx;
    if (is_array($items)) {
        foreach($items as $i) {
            $item = $modx->newObject('modResource');
            $item->fromArray($i);
            if (!$item->save()) {
                print "Error saving $crc {$i['pagetitle']}!<br/>";
            }
            else {

                print "$crc created {$i['pagetitle']}<br/>";
            }
        }
    }
    else {
        print "ERROR: $crc not an array.<br/>";
    }
}
