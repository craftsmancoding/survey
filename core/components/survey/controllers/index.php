<?php
/** 
 * This file handles manager requests made by Survey. 
 *
 * PARAMETERS
 *  @param string f function name inside of moxycart.class.php where request gets routed
 *      default: help
 */
 
$core_path = $modx->getOption('survey.core_path','',MODX_CORE_PATH);

require_once($core_path.'components/survey/controllers/surveymgrcontroller.class.php');

$Survey = new SurveyMgrController($modx);

$log_level = $modx->getOption('log_level',$_GET, $modx->getOption('log_level'));
$old_level = $modx->setLogLevel($log_level);

$args = array_merge($_POST,$_GET); // skip the cookies, more explicit than $_REQUEST

$function = (isset($_GET['f']) && !empty($_GET['f']))? $_GET['f']:'show_all';

$results = $Survey->$function($args);

$modx->setLogLevel($old_level);
return $results;
/*EOF*/

