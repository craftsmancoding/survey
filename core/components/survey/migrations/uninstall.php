<?php
if (!defined('MODX_CORE_PATH')) {return;}

$modx->log(modX::LOG_LEVEL_INFO,'Running uninstall quiety (only fatal errors displayed)');
$old_level = $modx->setLogLevel(xPDO::LOG_LEVEL_FATAL); // keep it quiet

$core_path = $modx->getOption('survey.core_path','',MODX_CORE_PATH);
$modx->addPackage('survey',$core_path.'components/survey/model/','survey_');
$manager = $modx->getManager();
$manager->removeObjectContainer('Survey');
$manager->removeObjectContainer('Question');
$manager->removeObjectContainer('Response');
$manager->removeObjectContainer('Data');

//$modx->setLogLevel($old_level); // Not working?
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
/*EOF*/