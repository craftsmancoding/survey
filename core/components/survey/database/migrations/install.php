<?php
/**
 * install.php
 *
 * Runs when the package is installed, also when the install or migrate 
 * functions are run.
 * 
 * Parameters
 *
 * $object (array) contains all Repoman configuration data
 *
 */
if (!defined('MODX_CORE_PATH')) {return;}

$core_path = $modx->getOption('survey.core_path','',MODX_CORE_PATH);

$manager = $modx->getManager();

$modx->addPackage('survey',$core_path.'components/'.$object['namespace'].'/model/','survey_');

$manager->createObjectContainer('Survey');
$manager->createObjectContainer('Question');
$manager->createObjectContainer('Response');
$manager->createObjectContainer('Data');

/*EOF*/