<?php
/**
 * viewResponse
 *
 * Display a single response to a survey.
 *
 * PARAMETERS
 *
 * survey (integer) identifying which survey to show
 * page (integer) question grouper so a user doesn't have to see all Q's on one page
 * response_id (integer) primary key of the responses table
 * identifier (string) alt key to obfuscate the primary key, e.g. when sharing results
 *
 * INPUT FILTERS
 *
 * get, post, --custom snippet--
 *
 * These parameters support "Input Filters" so you can easily read parameters out of 
 * the $_GET or $_POST array (or alternatively supply your own Snippet).  The syntax 
 * mimics that of MODX's Output Filters:
 *
 * To listen to the "x" URL parameter (i.e. the $_GET array) with no default supplied:
 *
 *      &identifier=`x`
 * 
 * To listen for post data in the "response_id" field (no default supplied):
 *
 *      &response_id=`response_id:post`
 *
 * @name viewResponse
 * @description 
 *
 */

$core_path = $modx->getOption('survey.core_path','', MODX_CORE_PATH); 
if(!$modx->addPackage('survey',$core_path.'components/survey/model/','survey_')) {
    return 'Package Error.';
}


// Input filters: Read out of get array
$dynamic = array('response_id','identifier');
foreach ($dynamic as $k) {
    preg_match("/^(.*):((\w+)(=['`\"]?([^'`\"]*)['`\"]?)?)$/i", $scriptProperties[$k], $matches);
    if ($matches) {
        $filter = (isset($matches[3]))? $matches[3] : '';
        $x = (isset($matches[1]))? $matches[1] : ''; // whatever's to the left of the filter
        $y = (isset($matches[5]))? $matches[5] : ''; // any option, e.g. :get="my-default"
        // return print_r($matches,true);
        $raw_k = $x;
        if (strtolower($filter) == 'get') {
            $scriptProperties[$k] = (isset($_GET[$x]))? $_GET[$x]: $y;
        }
        // Don't use getOption here because it will read db config data if there is no $_POST data
        elseif (strtolower($filter) == 'post') {
            $scriptProperties[$k] = (isset($_POST[$x]))? $_POST[$x]: $y;
        }
        else {
            $scriptProperties[$k] = $modx->runSnippet($filter,array('input'=>$x,'options'=>$y));
        }
        //unset($scriptProperties[$k]);
    }
}

$survey = $modx->getOption('survey', $scriptProperties);
$prefix = $modx->getOption('prefix', $scriptProperties,'');

$c = $modx->newQuery('Question');
$c->where(array('survey_id' => $survey,'is_active'=>1));
if (isset($scriptProperties['page'])) {
    $c->andCondition(array('page' => $scriptProperties['page']));
}
$c->sortby('seq','ASC');

$Questions = $modx->getCollection('Question', $c);
//return $c->toSql();
if (!$Questions) {
    return 'There are no questions defined for this page of the survey.';
}
$out = '';
foreach ($Questions as $Q) {
    $type = $Q->get('type');
    if (!file_exists($core_path.'components/survey/model/'.$type.'.class.php')) {
        $modx->log(1, '[takeSurvey] Invalid question type: '.$type);
    }
    $FieldElement = new $type();
    $out .= $FieldElement->draw($Q);
    
}

return $out;
/*
if (isset($_SESSION['survey'])) {

}
*/
 
/*EOF*/