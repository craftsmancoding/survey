<?php
/**
 * @name viewResponse
 * @description View a user's response to a given survey; this will also save a submitted form from takeSurvey.
 * 
 * PARAMETERS
 *
 * &survey (integer) identifying which survey to show
 * &response_id (integer) primary key of the responses table to explicitly identify a response
 * &identifier (string) alt key for the responses table to explicitly identify a response

 * &onIncomplete (integer) page id of where to redirect if the current response is incomplete.
 *      A response may be considered incomplete for the following reasons:
 *          - No existing Response found
 *          - Survey incomplete
 *
 * &toPlaceholders (integer) if set, the snippet won't return output, it will only set placeholders.
 * &onComplete (string) comma-separated list of Snippets to execute on completion of the survey.
 * &debug (boolean) if set, Snippet will return debugging info including a list of all available placeholders.
 *      The debug action will also prevent redirects.
 *
 * INPUT FILTERS
 *
 * get, post, --custom snippet--
 *
 * Dynamic inputs: 'survey','response_id','identifier','debug'
 *
 * These parameters support "Input Filters" so you can easily read parameters out of 
 * the $_GET or $_POST array (or alternatively supply your own Snippet).  The syntax 
 * mimics that of MODX's Output Filters:
 *
 * 
 * To listen for post data in the "survey_id" field (no default supplied)
 *
 *      &survey=`survey_id:post`
 * 
 */

define('SURVEY_RESPONSE_SESSION_KEY', 'response_identifier');
$core_path = $modx->getOption('survey.core_path','', MODX_CORE_PATH); 
if(!$modx->addPackage('survey',$core_path.'components/survey/model/','survey_')) {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'[viewResponse] Error loading package.');
    return 'Package Error.';
}
$debug_info = "=========================\n";
$debug_info .= "viewResponse Debugging Mode\n";
$debug_info .= "=========================\n";
$debug_info .= 'POST: '.print_r($_POST,true) . "\n";
//------------------------------------------------------------------------------
// !Input filters: Read out of get array
//------------------------------------------------------------------------------
$dynamic = array('survey','response_id','identifier','debug');
foreach ($dynamic as $k) {
    if (!isset($scriptProperties[$k])) {
        continue;
    }
    preg_match("/^(.*):((\w+)(=['`\"]?([^'`\"]*)['`\"]?)?)$/i", $scriptProperties[$k], $matches);
    if ($matches) {
        $filter = (isset($matches[3]))? $matches[3] : '';
        $x = (isset($matches[1]))? $matches[1] : ''; // whatever's to the left of the filter
        $y = (isset($matches[5]))? $matches[5] : ''; // any option, e.g. :get="my-default"
        // return print_r($matches,true);
        $raw_k = $x;
        if (strtolower($filter) == 'get') {
            $debug_info .= "$k read from GET with default of $y\n";
            $scriptProperties[$k] = (isset($_GET[$x]))? $_GET[$x]: $y;
        }
        // Don't use getOption here because it will read db config data if there is no $_POST data
        elseif (strtolower($filter) == 'post') {
            $debug_info .= "$k read from POST with default of $y\n";
            $scriptProperties[$k] = (isset($_POST[$x]))? $_POST[$x]: $y;
        }
        else {
            $debug_info .= "$k read from custom input filter Snippet $filter with option of $y\n";
            $scriptProperties[$k] = $modx->runSnippet($filter,array('input'=>$x,'options'=>$y));
        }
    }
}

$debug_info .= "scriptProperties (rendered):\n".print_r($scriptProperties,true)."\n";

$survey_id = $modx->getOption('survey', $scriptProperties);
$response_id = $modx->getOption('response_id', $scriptProperties);
$user_id = $modx->getOption('user_id', $scriptProperties);
$user = $modx->getUser();
if (!$user_id && $user) {
    $user_id = $user->get('id');
    $debug_info .= "No user_id specified, but a valid MODX user was detected ($user_id)\n";
}
$identifier = $modx->getOption('identifier', $scriptProperties, $modx->getOption(SURVEY_RESPONSE_SESSION_KEY,$_COOKIE));
$prefix = $modx->getOption('prefix', $scriptProperties,'');
$onComplete = $modx->getOption('onComplete', $scriptProperties,'');
$onIncomplete = $modx->getOption('onIncomplete', $scriptProperties,false);
$action = $modx->getOption('action', $scriptProperties,$modx->resource->get('id'));
$page = $modx->getOption('page', $scriptProperties,0);
$toPlaceholders = $modx->getOption('toPlaceholders', $scriptProperties);
$debug = (bool) $modx->getOption('debug', $scriptProperties,false);

// Verify the survey
$Survey = $modx->getObject('Survey', array('survey_id' => $survey_id,'is_active'=>1));
if (!$Survey) {
    $modx->log(xPDO::LOG_LEVEL_ERROR, '[takeSurvey] Invalid Survey: '.$survey_id);
    return 'Invalid Survey.';
}


// Get the current response, if it exists.
// Detecting an existing response checks these items (in this order)
//  1. &response_id specified explicitly in the Snippet call
//  2. &user_id is specified explicitly in the Snippet call or an authenticated MODX $user is detected
//  3. &identifier is specified explicity in the Snippet call
//  4. $_SESSION data is checked for a valid identifier
//  If none of the above turn up an existing response, we must fail.
$Response = null; // init
if ($response_id) {
    $Response = $modx->getObject('Response',$response_id);
    if ($Response) $debug_info .= "Existing Response identified explicitly by response_id: $response_id\n";
}
elseif ($user_id) {
    $Response = $modx->getObject('Response',array('user_id'=>$user_id,'survey_id'=>$survey_id));
    if ($Response) $debug_info .= "Existing Response identified by user_id: $user_id\n";
}
elseif ($identifier) {
    $Response = $modx->getObject('Response',array('identifier'=>$identifier));
    if ($Response) $debug_info .= "Existing Response identified by identifier $identifier\n";
}
if (!$Response) {
    $debug_info .= "No existing Response found.\n";
    if ($debug) {
        return '<pre>'.$debug_info.'</pre>';
    }
    $modx->log(xPDO::LOG_LEVEL_ERROR,'[viewResponse] No existing Response found on page '.$modx->resource->get('id'));
    
    if (!$onIncomplete) {
        return 'No existing Response found.';
    }
    $url = $modx->makeUrl($onIncomplete,'','','full');
    $modx->sendRedirect($url); 
    exit;
}

$modx->setPlaceholder('response_id', $Response->get('response_id'));

//------------------------------------------------------------------------------
// ! Listen for submissions from takeSurvey. Save if Submitted
//------------------------------------------------------------------------------
if (!empty($_POST) && isset($_POST['questions']) && is_array($_POST['questions'])) {
    //return '<pre>'.print_r($_POST,true).'</pre>';
    // save stuff
    foreach ($_POST['questions'] as $question_id => $value) {
        $Data = $modx->getObject('Data', array('survey_id'=>$survey_id, 'response_id'=>$Response->get('response_id'), 'question_id'=>$question_id));
        // TODO: validate $value based on the question type        
        if (!$Data) {
            $Data = $modx->newObject('Data');
            $Data->set('survey_id',$survey_id);
            $Data->set('response_id',$Response->get('response_id'));
            $Data->set('question_id',$question_id);
        }
        $Data->set('value',$value);
        $Data->set('timestamp_modified',date('Y-m-d H:i:s'));
        $Data->save();
    }
    
    // Are we done? All req'd Q's complete?
    $Reqd = $modx->getCollection('Question', array('is_active'=>1,'is_required'=>1));
    $reqd_q = array();
    foreach ($Reqd as $r){
        $reqd_q[] = $r->get('question_id');
    }
    $Answered = $modx->getCollection('Data', array('survey_id'=>$survey_id,'response_id'=>$Response->get('response_id')));
    $ans_q = array();
    foreach ($Answered as $a) {
        $ans_q[] = $a->get('question_id');
    }
    
    sort($reqd_q);
    sort($ans_q);
    $diff = array_diff($reqd_q,$ans_q);
    if (empty($diff)) {
        $Response->set('is_complete',1);
        $Response->save();
    }
    else {
        if (!$onIncomplete) {
            return 'You have not yet completed your survey. Please go back and provide answers to all required questions.';
        }
        if ($debug) {
            $debug_info .= "Survey is not yet complete.\n";
            $debug_info .= "The following questions have not been answered yet: ". print_r($diff,true)."\n";
            return '<pre>'.$debug_info.'</pre>';
        }        
        $url = $modx->makeUrl($onIncomplete,'','','full');
        $modx->sendRedirect($url); 
        exit();    
    }    
}


// Is the survey incomplete?
if (!$Response->get('is_complete')) {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'[viewResponse] Response is incomplete. It cannot be viewed: '.$Response->get('response_id'));
    if (!$onIncomplete) {
        return 'You have not yet completed your survey. Please go back and provide answers to all required questions.';
    }
    if ($debug) {
        $debug_info .= "Survey is not complete.\n";
        return '<pre>'.$debug_info.'</pre>';
    }
    $url = $modx->makeUrl($onIncomplete,'','','full');
    $modx->sendRedirect($url); 
    exit();
}

//------------------------------------------------------------------------------
// !Data: Iterate
//------------------------------------------------------------------------------
$c = $modx->newQuery('Data');
$c->where(array(
    'survey_id' => $survey_id,
    'response_id'=>$Response->get('response_id')
));
$c->sortby('page, seq', 'ASC');
$Data = $modx->getCollectionGraph('Data','{"Question":{}}', $c);
$debug_info .= "Loading Data using this query:\n";
$debug_info .= $c->toSql();
$debug_info .= "\n";
//return $c->toSql();
if (!$Data) {
    $debug_info .= "No response data are available for this survey.\n";
    if ($debug) return '<pre>'.$debug_info.'</pre>';
    return 'No data available for this survey.';
}
$out = '<ol>';
$placeholders = array();
foreach ($Data as $D) {
    // TODO: map out full dropdown descriptions...
    $out .= '<li><strong>'.htmlspecialchars($D->Question->get('text')).'</strong> '.htmlspecialchars($D->get('value')).'</li>';
}
$out .= '</ol>';

if ($debug) {
    return '<pre>'.$debug_info.'</pre>';
}

$snippets = explode(',',$onComplete);
if (is_array($snippets)) {
    foreach ($snippets as $s) {
        $modx->runSnippet(trim($s), array('response_id'=>$Response->get('response_id'),'Data'=>$Data));
    }
}

if (!$toPlaceholders) {
    return $out; 
}
/*EOF*/