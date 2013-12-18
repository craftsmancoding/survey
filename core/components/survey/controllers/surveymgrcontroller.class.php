<?php
/**
 * @package survey
 */
class SurveyMgrController{
    
    public $modx;
    public $core_path;
    public $assets_url;
    public $connector_url;
    public $mgr_controller_url;
    public $jquery_url;

    public function __construct($modx) {
          
        $this->modx = &$modx;
        $this->core_path = $this->modx->getOption('survey.core_path','',MODX_CORE_PATH);

        $this->modx->addPackage('survey',$this->core_path.'components/survey/model/','survey_');
        $this->assets_url = $this->modx->getOption('survey.assets_url', null, MODX_ASSETS_URL);
        $this->connector_url = $this->assets_url.'components/survey/connector.php?f=';
        $this->jquery_url = $this->assets_url.'components/survey/js/jquery-2.0.3.min.js';
        $a = (int) $this->modx->getOption('a',$_GET);
        if (!$a) {
            $Action = $this->modx->getObject('modAction', array('namespace'=>'survey','controller'=>'controllers/index'));
            $a = $Action->get('id');
        }
        $this->mgr_controller_url = MODX_MANAGER_URL .'?a='.$a.'&f=';

    }

    /**
     * Load a view file. We put in some commonly used variables here for convenience
     *
     * @param string $file: name of a file inside of the "views" folder
     * @param array $data: an associative array containing key => value pairs, passed to the view
     * @return string
     */
    private function _load_view($file, $data=array(),$return=false) {
        $file = basename($file);
    	if (file_exists($this->core_path.'components/survey/views/'.$file)) {
    	    if (!isset($return) || $return == false) {
    	        ob_start();
    	        include ($this->core_path.'components/survey/views/'.$file);
    	        $output = ob_get_contents();
    	        ob_end_clean();
    	    }     
    	} 
    	else {
    		$output = $this->modx->lexicon('view_not_found', array('file'=> 'views/'.$file));
    	}
    
    	return $output;
    }

    /**
    * Format Options
    * @param string $val
    * @return json $json_options
    **/
    public function format_options($val='') 
    {
        $temp_options = array();
        $arr_options = explode('||', $val);
        if(is_array($arr_options)) {
           foreach ($arr_options as $options) {
                $option = explode('==', $options);
                if(isset($option[1])) {
                   $temp_options[$option[1]] = $option[0];
                } else {
                    $temp_options = $option;
                }
           }  
        }
       $json_options = json_encode($temp_options);
       return $json_options;
    }
    
    /**
     * Shows all survey in a grid or list.
     *
     */
    public function show_all($args) {
        $data = array();
        $data['surveys'] = $this->json_surveys(array('is_active'=>1),true);
        $this->modx->regClientCSS($this->assets_url . 'components/survey/css/mgr.css');
        $this->modx->regClientStartupScript($this->jquery_url);
        $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            var mgr_controller_url = "'.$this->mgr_controller_url.'";
            </script>
        ');
        $data['mgr_controller_url'] = $this->mgr_controller_url;
        return $this->_load_view('survey-list.php',$data);
    }

    /**
     * Shows all survey in a grid or list.
     *
     */
    public function show_questions($args) {
        $data = array();
        $survey_id = (int) $this->modx->getOption('survey_id', $args);
        if (!$Survey = $this->modx->getObject('Survey', $survey_id)) {        
            return 'Survey not found : '.$survey_id;
        }
        $data['questions'] = $this->json_questions(array('is_active'=>1,'survey_id'=>$survey_id),true);
        return $this->_load_view('question-list.php',$data);
    }

     /**
     * Get a single question for Ajax update.
     */
    public function get_question($args) {
        $id = (int) $this->modx->getOption('question_id', $args);
        $Question = $this->modx->getObject('Question',$id);
        if (!$Question) {
            return 'Error loading Question. '.print_r($args,true);
        }
        $data = $Question->toArray();
        return $this->_load_view('update-question.php',$data);
    }

    /**
    * Create New Survey
    **/
    public function create($args) 
    {
        $data = array();
        $this->modx->regClientCSS($this->assets_url . 'components/survey/css/mgr.css');
        $this->modx->regClientStartupScript($this->jquery_url);
        $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            var connector_url = "'.$this->connector_url.'";
            var mgr_controller_url = "'.$this->mgr_controller_url.'";
            </script>
        ');
        return $this->_load_view('create-survey.php',$data);
    }

    /**
    * Update a Survey
    **/
    public function update($args) 
    {
        $survey_id = (int) $this->modx->getOption('survey_id', $args);
        if (!$Survey = $this->modx->getObject('Survey', $survey_id)) {        
            return 'Survey not found : '.$survey_id;
        }
        $data = $Survey->toArray();
        $data['questions'] = $this->json_questions(array('is_active'=>1,'survey_id'=>$survey_id),true);
        $question_data = array(
            'survey_id'=>$survey_id,
        );

        $data['question-modal'] = $this->_load_view('create-question.php',$question_data);
        $this->modx->regClientCSS($this->assets_url . 'components/survey/css/mgr.css');
        $this->modx->regClientStartupScript($this->jquery_url);
        $this->modx->regClientStartupScript($this->assets_url.'components/survey/js/bootstrap.js');
        $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
            var connector_url = "'.$this->connector_url.'";
             var mgr_controller_url = "'.$this->mgr_controller_url.'";
            </script>
        ');
        $data['loader_path'] = $this->assets_url.'components/survey/images/gif-load.gif';
        return $this->_load_view('update-survey.php',$data);
    }

    /**
    * Save the Survey. $args includes all posted data. "action" determines the action here.
    *
    */
    public function survey_save($args) {

        $out = array(
            'success' => true,
            'msg' => ''
        );
        
        $action = $this->modx->getOption('action', $args);
        unset($args['action']);        
        
        switch ($action) {
            case 'update':
               case 'update':
                $Survey = $this->modx->getObject('Survey',$this->modx->getOption('survey_id', $args));
                if (!$Survey) {
                    $out['success'] = false;
                    $out['msg'] = 'Invalid Survey.';
                    return json_encode($out);
                }
                $Survey->fromArray($args);
                if (!$Survey->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to update Survey.';
                    $out['survey_id'] = $Survey->get('survey_id');
                }
                $out['msg'] = 'Survey updated successfully.';    
                break;
            case 'delete':
                $survey_id = $this->modx->getOption('survey_id', $args);
                $Survey = $this->modx->getObject('Survey',$survey_id);
                if (!$this->modx->removeCollection('Question',array('survey_id'=>$survey_id))) {
                    $out['success'] = false;
                    $out['msg'] = 'Survey deleted successfully.Failed to delete Related Questions.';  
                    return json_encode($out);  
                }

                if (!$Survey->remove()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to delete Survey.';    
                } 
                
                $out['msg'] = 'Survey deleted successfully.';
                break;
            case 'create':
            default:
                $Survey = $this->modx->newObject('Survey');    
                $Survey->fromArray($args);
                if (!$Survey->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to save Survey.';    
                }
                $out['survey_id'] = $this->modx->lastInsertId();
                $out['msg'] = 'Survey created successfully.';
        }
                
        return json_encode($out);
    }

    /**
    * Save the QUestion. $args includes all posted data. "action" determines the action here.
    *
    */
    public function question_save($args) {

        $out = array(
            'success' => true,
            'msg' => ''
        );
        
        $action = $this->modx->getOption('action', $args);
        unset($args['action']);        
        
        switch ($action) {
            case 'update':
                $Question = $this->modx->getObject('Question',$this->modx->getOption('question_id', $args));
                if (!$Question) {
                    $out['success'] = false;
                    $out['msg'] = 'Invalid Question.';
                    return json_encode($out);
                }
                $Question->fromArray($args);
                if (!$Question->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to update Question.';
                    $out['Question_id'] = $Question->get('Question_id');
                }
                $out['msg'] = 'Question updated successfully.';    
                break;
            case 'delete':
                $question_id = $this->modx->getOption('question_id', $args);
                $Question = $this->modx->getObject('Question',$question_id);
                if (!$Question->remove()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to delete Question.';    
                } 
                
                $out['msg'] = 'Question deleted successfully.';
                break;
            case 'create':
            default:
                if(empty($args['text'])) {
                    $out['success'] = false;
                    $out['msg'] = 'Question Field is Required.';  
                    return  json_encode($out);
                }
                if(!empty($args['options'])) {
                    $args['options'] = $this->format_options($args['options']);
                }
                
                $Question = $this->modx->newObject('Question');    
                $Question->fromArray($args);
                if (!$Question->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to save Question.';    
                }
                $out['Question_id'] = $this->modx->lastInsertId();
                $out['msg'] = 'Question created successfully.';
        }
                
        return json_encode($out);
    }

    /**
     * get all surveys
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */
    public function json_surveys($args,$raw=false) {
        $sort = $this->modx->getOption('sort',$args,'survey_id');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        $criteria = $this->modx->newQuery('Survey');
        if (isset($args['is_active'])) {
            $criteria->where(array('is_active' => (int) $this->modx->getOption('is_active',$args)));
        }
        $total_pages = $this->modx->getCount('Survey',$criteria);
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('Survey',$criteria);

        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = $p->toArray();
        }
        if ($raw) {
            return $data;
        }
        return json_encode($data);
    }

    /**
    * get all questions
    * @param boolean $raw if true, results are returned as PHP array default: false
    * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
    */
    public function json_questions($args,$raw=false) {
        $survey_id = (int) $this->modx->getOption('survey_id',$args);
        $sort = $this->modx->getOption('sort',$args,'question_id');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        $criteria = $this->modx->newQuery('Question');
        if (isset($args['is_active'])) {
            $criteria->where(array('is_active' => (int) $this->modx->getOption('is_active',$args)));
        }

        if ($survey_id) {
            $criteria->where(array('survey_id'=>$survey_id));
        }

        $total_pages = $this->modx->getCount('Question',$criteria);
        $criteria->sortby($sort,$dir);
        $pages = $this->modx->getCollection('Question',$criteria);

        // return $criteria->toSQL(); <-- useful for debugging
        // Init our array
        $data = array(
            'results'=>array(),
            'total' => $total_pages,
        );
        foreach ($pages as $p) {
            $data['results'][] = $p->toArray();
        }
        if ($raw) {
            return $data;
        }
        return json_encode($data);
    }
            
}
/*EOF*/