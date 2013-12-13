<?php
/**
 * @package survey
 */
class SurveyMgrController{
    
    public $modx;
    public $core_path;
    public $assets_url;
    public $connector_url;
    public $upload_url;
    public $mgr_controller_url;
    public $max_image_width = 300;
    
    public function __construct($modx) {
        $this->modx = &$modx;
        $this->core_path = $this->modx->getOption('survey.core_path','',MODX_CORE_PATH);
        $this->modx->addPackage('survey',$this->core_path.'components/survey/model/');
        $this->assets_url = $this->modx->getOption('survey.assets_url', null, MODX_ASSETS_URL);
        $this->connector_url = $this->assets_url.'components/survey/connector.php?f=';
        $this->upload_dir = 'images/survey/'; // path & url: rel to MODX_ASSETS_PATH & MODX_ASSETS_URL
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
        $this->modx->regClientCSS($this->assets_url . 'components/survey/css/mgr.css');
        $data['mgr_controller_url'] = $this->mgr_controller_url;
        return $this->_load_view('list.php',$data);
    }
            
}
/*EOF*/