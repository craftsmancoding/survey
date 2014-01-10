<?php
/**
 * @name takeSurvey
 * @description Display a survey's questions to the user.
 *
 * Display a survey's questions to the user.  The user doesn't need to be logged in, but logging in does
 * help uniquely identify the user.  The "response" object identifies a unique survey response, i.e. 
 * the user's answers to all the survey's questions at a given point in time.  We uniquely identify
 * a response using some custom hashing and we reference it in the $_SESSION array.
 *
 * PARAMETERS
 *
 * &survey (integer) identifying which survey to show
 * &page (integer) question grouper so a user doesn't have to see all Q's on one page
 * &response_id (integer) primary key of the responses table, used to edit existing response
 * &identifier (string) alt key for the responses table to obfuscate the primary key, used to edit existing response
 * &prefix (string) used as a prefix for placeholders that are set.
 * &action (integer) page id of where to submit the form to -- the target page must contain the takeSurvey Snippet. 
 *      Optional. Default: the id of the current page.
 * &onComplete (string) comma-separated list of Snippets to execute on completion of the survey.
 * &redirect (integer) page id to forward to on completion
 * &toPlaceholders (integer) if set, the snippet won't return output, it will only set placeholders
 * &debug (boolean) if set, Snippet will return debugging info including a list of all available placeholders.
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
 *
 */


if (isset($scriptProperties['log_level'])) {
    $modx->setLogLevel($scriptProperties['log_level']);
}


$modx->log(xPDO::LOG_LEVEL_DEBUG,'[takeSurvey] Snippet called on page '.$modx->resource->get('id'));

define('SURVEY_RESPONSE_SESSION_KEY', 'response_identifier');
$core_path = $modx->getOption('survey.core_path','', MODX_CORE_PATH); 
if(!$modx->addPackage('survey',$core_path.'components/survey/model/','survey_')) {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'[takeSurvey] Error loading package.');
    return 'Package Error.';
}
$debug_info = "=========================\n";
$debug_info .= "takeSurvey Debugging Mode\n";
$debug_info .= "=========================\n";
//------------------------------------------------------------------------------
// !Input filters: Read out of get array
//------------------------------------------------------------------------------
$dynamic = array('survey','page','response_id','identifier','debug');
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
$modx->log(xPDO::LOG_LEVEL_DEBUG,'[takeSurvey] scriptProperties: '.print_r($scriptProperties,true));

$survey_id = $modx->getOption('survey', $scriptProperties);
$response_id = $modx->getOption('response_id', $scriptProperties);
$user_id = $modx->getOption('user_id', $scriptProperties);
$user = $modx->getUser();
if (!$user_id) {
    if($user) {
        $user_id = $user->get('id');
    }
    if ($user_id) {
        $debug_info .= "No user_id specified, but a valid MODX user was detected ($user_id)\n";
        $modx->log(xPDO::LOG_LEVEL_DEBUG,"[takeSurvey] No user_id specified, but a valid MODX user was detected ($user_id)");
    }
    else {
        $debug_info .= "No user_id specified. No authenticated user detected.\n";
        $modx->log(xPDO::LOG_LEVEL_DEBUG,"[takeSurvey] No user_id specified. No authenticated user detected.");
    }
}
//$modx->log(xPDO::LOG_LEVEL_ERROR, print_r($_COOKIE,TRUE));
//$identifier = $modx->getOption('identifier', $scriptProperties, $modx->getOption(SURVEY_RESPONSE_SESSION_KEY,$_SESSION));
$identifier = $modx->getOption('identifier', $scriptProperties, $modx->getOption(SURVEY_RESPONSE_SESSION_KEY,$_COOKIE));
$prefix = $modx->getOption('prefix', $scriptProperties,'');
$onComplete = $modx->getOption('onComplete', $scriptProperties,'');
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

//------------------------------------------------------------------------------
//! Frame the Form
//------------------------------------------------------------------------------
$props = array();
$props['survey_id'] = $Survey->get('survey_id');
$props['page'] = $page;
$props['submit'] = 'Next'; // todo: "
$props['action'] = $modx->makeUrl($modx->resource->get('id'),'','','abs');


// Get the current response, if it exists, otherwise we begin a new one.
// Detecting an existing response checks these items (in this order)
//  1. &response_id specified explicitly in the Snippet call
//  2. &user_id is specified explicitly in the Snippet call or an authenticated MODX $user is detected
//  3. &identifier is specified explicity in the Snippet call
//  4. $_SESSION data is checked for a valid identifier
//  If none of the above turn up an existing response, a new response object is created.
$Response = null; // init
if ($response_id) {
    $Response = $modx->getObject('Response',$response_id);
    if ($Response) {
        $debug_info .= "Existing Response identified explicitly by response_id ($response_id)\n";
        $modx->log(xPDO::LOG_LEVEL_DEBUG,"[takeSurvey] Existing Response identified explicitly by response_id ($response_id)");
    }
}
elseif ($user_id) {
    $Response = $modx->getObject('Response',array('user_id'=>$user_id,'survey_id'=>$survey_id));
    if ($Response) {
        $debug_info .= "Existing Response identified by user_id $user_id (".$Response->get('response_id').")\n";
        $modx->log(xPDO::LOG_LEVEL_DEBUG,"[takeSurvey] Existing Response identified by user_id $user_id (".$Response->get('response_id').')');
    }
}
elseif ($identifier) {
    $Response = $modx->getObject('Response',array('identifier'=>$identifier));
    if ($Response) {
        $debug_info .= "Existing Response identified by identifier $identifier (".$Response->get('response_id').")\n";
        $modx->log(xPDO::LOG_LEVEL_DEBUG,"[takeSurvey] Existing Response identified by identifier $identifier (".$Response->get('response_id').')');
    }
}

if (!$Response) {
    $Response = $modx->newObject('Response');
    $Response->set('survey_id', $survey_id);    
    if ($user) {
        $Response->set('user_id', $user->get('id'));
    }
    $Response->set('ip', $modx->getOption('REMOTE_ADDR', $_SERVER));
    $Response->set('user_agent', $modx->getOption('HTTP_USER_AGENT', $_SERVER));

    // Create session $identifier
    $identifier = md5(uniqid().print_r($_SERVER,true));
    //$_SESSION[SURVEY_RESPONSE_SESSION_KEY] = $identifier;
    setcookie(SURVEY_RESPONSE_SESSION_KEY,$identifier, time()+3600*24*30);
    $Response->set('identifier', $identifier);
    $Response->save();
    $debug_info .= "No existing Response found. Creating new Response (".$Response->get('response_id').")\n";
    $modx->log(xPDO::LOG_LEVEL_DEBUG,"[takeSurvey] No existing Response found. Creating new Response (".$Response->get('response_id').")");
}

//------------------------------------------------------------------------------
// ! Save if Submitted
//------------------------------------------------------------------------------
if (!empty($_POST) && isset($_POST['questions']) && is_array($_POST['questions'])) {
    //return '<pre>'.print_r($_POST,true).'</pre>';
    $modx->log(xPDO::LOG_LEVEL_DEBUG,"[takeSurvey] POST data detected: ".print_r($_POST,true));
    // save stuff
    foreach ($_POST['questions'] as $question_id => $value) {
        $Data = $modx->getObject('Data', array('survey_id'=>$survey_id, 'response_id'=>$Response->get('response_id'), 'question_id'=>$question_id));
        // TODO: validate $value based on the question type
        if (!$Data) {
            $modx->log(xPDO::LOG_LEVEL_DEBUG,"[takeSurvey] new data being created...");
            $Data = $modx->newObject('Data');
            $Data->set('survey_id',$survey_id);
            $Data->set('response_id',$Response->get('response_id'));
            $Data->set('question_id',$question_id);
        }
        $modx->log(xPDO::LOG_LEVEL_DEBUG,"[takeSurvey] setting value of question_id $question_id to ".$value .' (data_id:'.$Data->get('data_id').')');
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
        $snippets = explode(',',$onComplete);
        if (is_array($snippets)) {
            foreach ($snippets as $s) {
                $modx->runSnippet(trim($s), array('response_id'=>$Response->get('response_id')));
            }
        }
        $modx->log(xPDO::LOG_LEVEL_DEBUG,"[takeSurvey] Survey is complete.");
        //return 'Thank you for completing the survey!';
    }
    if (!isset($scriptProperties['page'])) {
        $page = $page + 1;
    }
}


// Is the survey already complete?
if ($Response->get('is_complete') && !$Survey->get('is_editable')) {
    return 'You have already completed this survey and its results are locked from editing.';
}

//------------------------------------------------------------------------------
// !Questions: Iterate
//------------------------------------------------------------------------------
$c = $modx->newQuery('Question');
$c->where(array('survey_id' => $survey_id,'is_active'=>1));
if (isset($page)) {
    $c->andCondition(array('page' => $page));
}
$c->sortby('seq','ASC');

$Questions = $modx->getCollection('Question', $c);
$debug_info .= "Loading Questions using this query:\n";
$debug_info .= $c->toSql();
$debug_info .= "\n";
$modx->log(xPDO::LOG_LEVEL_DEBUG,"[takeSurvey] Displaying questions now from query ". $c->toSql());


if (!$Questions) {
    $debug_info .= "No Questions are available for this survey.\n";
    $modx->log(xPDO::LOG_LEVEL_WARNING,"[takeSurvey] No questions available for this survey.");
    if ($debug) return '<pre>'.$debug_info.'</pre>';
    return 'There are no questions defined for this page of the survey.';
}
$out = '';
$placeholders = array();
foreach ($Questions as $Q) {
    $type = $Q->get('type');
    if (!file_exists($core_path.'components/survey/model/survey/'.$type.'.class.php')) {
        $modx->log(xPDO::LOG_LEVEL_ERROR, '[takeSurvey] Invalid question type: '.$type);
        continue;
    }
    require_once $core_path.'components/survey/model/survey/'.$type.'.class.php';
    
    // Check for existing answer data
    //print_r($Q->toArray()); exit;
    $D = $modx->getObject('Data', array(
        'response_id'=>$Response->get('response_id'), 
        'question_id'=>$Q->get('question_id')
        )
    );
    $array = $Q->toArray();
    $array['value'] = $Q->get('default');
    if (!$D) {
        $modx->log(xPDO::LOG_LEVEL_INFO, '[takeSurvey] no existing answer data found for response '.$Response->get('response_id').' question '.$Q->get('question_id'));
        $D = $modx->newObject('Data');
        $D->set('value', $Q->get('default'));
    }
    else {
        $modx->log(xPDO::LOG_LEVEL_INFO, '[takeSurvey] Existing Data found for response_id '.$Response->get('response_id').' question_id '.$Q->get('question_id'). ' ('.$D->get('data_id').')');    
        $array['value'] = $D->get('value');
    }
    
    
    
    $namespace = $prefix.'q'.$Q->get('question_id').'.'; // e.g. [[+q1.value]]
    $modx->setPlaceholders($array, $namespace);
    $scriptProperties['namespace'] = $namespace;
    $FieldElement = new $type($modx,$scriptProperties);
    $out .= $FieldElement->draw($Q,$D);
    if ($debug) {
        foreach ($array as $k => $v) {
            $placeholders[] = $namespace.$k;
        }
        $placeholders = array_merge($placeholders,$FieldElement->placeholders);
    }

}

if ($debug) {
    $debug_info .= "\nAvailable Placeholders: \n";
    foreach ($placeholders as $p) {
        $debug_info .= '&#91;&#91;&#43;'.$p.'&#93;&#93;'."\n";
    }
    return '<pre>'.$debug_info.'</pre>';
}

$props['elements'] = $out;

if ($toPlaceholders) {
    return ''; // return nothing, we just rely on placeholders.
}

$tpl = file_get_contents($core_path.'components/survey/elements/chunks/form.tpl');
$chunk = $modx->newObject('modChunk', array('name' => "{tmp}-{".uniqid()."}"));
$chunk->setCacheable(false);
$out = $chunk->process($props, $tpl);

return $out; 
/*EOF*/