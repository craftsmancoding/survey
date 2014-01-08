<?php
/**
 * uninstall.php
 *
 * Runs when the package is uninstalled, also when the install or migrate 
 * functions are run.
 * 
 * Parameters
 *
 * $object (array) contains all Repoman configuration data
 *
 */
if (!defined('MODX_CORE_PATH')) {return;}

$core_path = $modx->getOption('survey.core_path','',MODX_CORE_PATH);

$modx->addPackage('survey',$core_path.'components/'.$object['namespace'].'/model/','survey_');
$manager = $modx->getManager();
$manager->removeObjectContainer('Survey');
$manager->removeObjectContainer('Question');
$manager->removeObjectContainer('Response');
$manager->removeObjectContainer('Data');

/*EOF*/