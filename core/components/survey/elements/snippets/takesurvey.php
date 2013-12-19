<?php
/**
 * takeSurvey
 *
 * Display a survey's questions to the user.  The user doesn't need to be logged in, but logging in does
 * help uniquely identify the user.  The "response" object identifies a unique survey response, i.e. 
 * the user's answers to all the survey's questions at a given point in time.  We uniquely identify
 * a response using some custom hashing and we reference it in the $_SESSION array.
 *
 * PARAMETERS
 *
 * survey (integer) identifying which survey to show
 * page (integer) question grouper so a user doesn't have to see all Q's on one page
 * response_id (integer) primary key of the responses table
 * identifier (string) alternate key for the responses table to obfuscate the primary key 
 * prefix (string) used as a prefix for any placeholders that are set.
 *
 * INPUT FILTERS
 *
 * get, post, --custom snippet--
 *
 * These parameters support "Input Filters" so you can easily read parameters out of 
 * the $_GET or $_POST array (or alternatively supply your own Snippet).  The syntax 
 * mimics that of MODX's Output Filters:
 *
 * To listen to the "pg" URL parameter (i.e. the $_GET array) and use a default of "1":
 *
 *      &page=`pg:get=1`
 * 
 * To listen for post data in the "survey_id" field (no default supplied)
 *
 *      &survey=`survey_id:post`
 *
 * @name takeSurvey
 * @description 
 *
 */
define('SURVEY_RESPONSE_SESSION_KEY', 'response.identifier');

$core_path = $modx->getOption('survey.core_path','', MODX_CORE_PATH); 
if(!$modx->addPackage('survey',$core_path.'components/survey/model/','survey_')) {
    return 'Package Error.';
}


// Input filters: Read out of get array
$dynamic = array('survey','page','response_id','identifier');
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


$survey_id = $modx->getOption('survey', $scriptProperties);
$response_id = $modx->getOption('response_id', $scriptProperties);
$identifier = $modx->getOption('identifier', $scriptProperties, $modx->getOption(SURVEY_RESPONSE_SESSION_KEY,$_SESSION));
$prefix = $modx->getOption('prefix', $scriptProperties,'');

// Verify the survey
$Survey = $modx->getObject('Survey', array('survey_id' => $survey_id,'is_active'=>1));
if (!$Survey) {
    $modx->log(xPDO::LOG_LEVEL_ERROR, '[takeSurvey] Invalid Survey: '.$survey_id);
    return 'Invalid Survey.';
}

// Get the current response, if it exists, otherwise we begin a new one.
$Response = null; // init
if ($response_id) {
    $Response = $modx->getObject('Response',$response_id);
}
elseif ($identifier) {
    $Response = $modx->getObject('Response',array('identifier'=>$identifier));
}
if (!$Response) {
    $Response = $modx->newObject('Response');
    $Response->set('survey_id', $survey_id);
    $user = $modx->getUser();
    if ($user) {
        $Response->set('user_id', $user->get('id'));
    }
    $Response->set('ip', $modx->getOption('REMOTE_ADDR', $_SERVER));
    $Response->set('user_agent', $modx->getOption('HTTP_USER_AGENT', $_SERVER));

    // Create session $identifier
    $identifier = md5(uniqid().print_r($_SERVER,true));
    $_SESSION[SURVEY_RESPONSE_SESSION_KEY] = $identifier;
    $Response->set('identifier', $identifier);
    $Response->save();
}

// Is the survey already complete?
if ($Response->get('is_complete') && !$Survey->get('is_editable')) {
    return 'You have already completed this survey and its results are locked from editing.';
}

// Load up the questions
$c = $modx->newQuery('Question');
$c->where(array('survey_id' => $survey_id,'is_active'=>1));
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
    if (!file_exists($core_path.'components/survey/model/survey/'.$type.'.class.php')) {
        $modx->log(xPDO::LOG_LEVEL_ERROR, '[takeSurvey] Invalid question type: '.$type);
    }
    require_once $core_path.'components/survey/model/survey/'.$type.'.class.php';
    
    // Check for existing answer data
    $D = $modx->getObject('Data', array('response_id'=>$Response->get('response_id'), 'question_id'=>$Q->get('question_id')));
    if (!$D) {
        $modx->log(xPDO::LOG_LEVEL_INFO, '[takeSurvey] no existing answer data found for response '.$Response->get('response_id').' question '.$Q->get('question_id'));
        $D = $modx->newObject('Data');
    }
    $array = $Q->toArray();
    $array['value'] = ($D->get('value')) ? $D->get('value') : $Q->get('default');
    $namespace = $prefix.'q'.$Q->get('question_id'); // e.g. [[+q1.value]]
    $modx->setPlaceholders($array, $namespace);
    $FieldElement = new $type($modx,array('namespace'=>$namespace));
    $out .= $FieldElement->draw($Q,$D);   
}

return $out;
/*
if (isset($_SESSION['survey'])) {

}
*/
 
/*EOF*/