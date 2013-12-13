<?php
if (!defined('MODX_CORE_PATH')) {return;}
$core_path = $modx->getOption('survey.core_path','',MODX_CORE_PATH);
$modx->addPackage('survey',$core_path.'components/survey/model/','survey_');

$Survey = $modx->newObject('Survey');
$Survey->fromArray(
    array(
        'name'=>'Test Survey',
        'description'=>'For testing...',
        'is_active'=>1,
        'is_editable'=>1,
        'seq'=>1,
        'timestamp_created'=>date('Y-m-d H:i:s')
    )
);

$questions = array();

$Q = $modx->newObject('Question');
$Q->fromArray(
    array(
        'text'=>'What is your name?',
        'type'=>'text',
        'options'=>'',
        'page'=>0,
        'is_active'=>1,
        'is_requred'=>1,
        'seq'=>1,
        'timestamp_created'=>date('Y-m-d H:i:s')
    )
);
$questions[] = $Q;

$Q = $modx->newObject('Question');
$Q->fromArray(
    array(
        'text'=>'What is your Gender?',
        'type'=>'dropdown',
        'options'=>'{"male":"Male","female":"Female"}',
        'page'=>0,
        'is_active'=>1,
        'is_requred'=>1,
        'seq'=>1,
        'timestamp_created'=>date('Y-m-d H:i:s')
    )
);
$questions[] = $Q;

$Q = $modx->newObject('Question');
$Q->fromArray(
    array(
        'text'=>'Tell us about yourself',
        'type'=>'textarea',
        'options'=>'',
        'page'=>0,
        'is_active'=>1,
        'is_requred'=>1,
        'seq'=>1,
        'timestamp_created'=>date('Y-m-d H:i:s')
    )
);
$questions[] = $Q;
$Survey->addMany($questions);
$Survey->save();
/*EOF*/