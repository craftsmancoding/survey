<?php
if (!defined('MODX_CORE_PATH')) {return;}

$core_path = $modx->getOption('survey.core_path','',MODX_CORE_PATH);
$modx->log(modX::LOG_LEVEL_INFO,'Core path: '.$core_path);

$manager = $modx->getManager();

$generator = $manager->getGenerator();

// Use this to generate classes and maps from your schema
/*
if ($regenerate_classes) { 
    print_msg('<br/>Attempting to remove/regenerate class files...');
    delete_class_files($class_dir);
    delete_class_files($mysql_class_dir);
}
*/

$generator->parseSchema($core_path.'components/survey/model/schema/survey.mysql.schema.xml',$core_path.'components/survey/model/');


$modx->addPackage('survey',$core_path.'components/survey/model/','survey_');

$manager->createObjectContainer('Survey');
$manager->createObjectContainer('Question');
$manager->createObjectContainer('Response');
$manager->createObjectContainer('Data');

/*EOF*/