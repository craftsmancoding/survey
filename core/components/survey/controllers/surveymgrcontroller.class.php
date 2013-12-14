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
     * Shows all survey in a grid or list.
     *
     */
    public function show_all($args) {
        $data = array();
        $data['surveys'] = $this->json_surveys(array('is_active'=>1),true);
        $this->modx->regClientCSS($this->assets_url . 'components/survey/css/mgr.css');
        $data['mgr_controller_url'] = $this->mgr_controller_url;
        return $this->_load_view('list.php',$data);
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
            </script>
        ');
        return $this->_load_view('create.php',$data);
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
                // Update Survey Here 
                break;
            case 'delete':
                //Delete Survey
                break;
            case 'create':
            default:
                $Survey = $this->modx->newObject('Survey');    
                $Survey->fromArray($args);
                if (!$Survey->save()) {
                    $out['success'] = false;
                    $out['msg'] = 'Failed to save Survey.';    
                }
                $out['msg'] = 'Survey created successfully.';
        }
                
        return json_encode($out);
    }

    /**
     * get all surveys
     * @param boolean $raw if true, results are returned as PHP array default: false
     * @return mixed A JSON array (string), a PHP array (array), or false on fail (false)
     */
    public function json_surveys($args,$raw=false) {
        $criteria = $this->modx->newQuery('Survey');
        if (isset($args['is_active'])) {
            $criteria->where(array('is_active' => (int) $this->modx->getOption('is_active',$args)));
        }
        $total_pages = $this->modx->getCount('Survey',$criteria);
        
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
            
}
/*EOF*/