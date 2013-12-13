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
        $this->core_path = $this->modx->getOption('tiles.core_path','',MODX_CORE_PATH);
        $this->modx->addPackage('survey',$this->core_path.'components/survey/model/');
        $this->assets_url = $this->modx->getOption('survey.assets_url', null, MODX_ASSETS_URL);
        $this->connector_url = $this->assets_url.'components/survey/connector.php?f=';
        $this->upload_dir = 'images/tiles/'; // path & url: rel to MODX_ASSETS_PATH & MODX_ASSETS_URL
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
     * Shows all tiles in a grid or list.
     *
     *
     *
     */
    public function show_all($args) {

        return 'Show all Surveys... include a button to create a new one';    
/*
        $this->modx->regClientCSS($this->assets_url . 'components/tiles/css/style.css');

        $this->modx->regClientCSS($this->assets_url . 'components/tiles/css/mgr.css');
        $this->modx->regClientCSS($this->assets_url . 'components/tiles/css/dropzone.css');
           
                
        $this->modx->regClientStartupScript($this->assets_url . 'components/tiles/js/jquery-2.0.3.min.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/tiles/js/jquery.easing.1.3.js');
        
        $this->modx->regClientStartupScript($this->assets_url.'components/tiles/js/jquery-ui.js');
        $this->modx->regClientStartupScript($this->assets_url.'components/tiles/js/dropzone.js');
   
        // Get a javascript compatible version of the tile template so it can better format upload progress
        // We send it default parameters from a default new Tile object
        $Tile = $this->modx->newObject('Tile');
        $previewTemplate = $this->_load_view('preview.php',$Tile->toArray());
    	$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
    		var connector_url = "'.$this->connector_url.'";
            var assets_url = "'.MODX_ASSETS_URL.'";
            jQuery(document).ready(function() {
                var myDropzone = new Dropzone("div#image_dropzone", 
                    {
                        url: connector_url+"image_upload",
                        previewTemplate: '.json_encode($previewTemplate).'
                    }
                );
                
                // Refresh the list on success (append new tile to end)
                myDropzone.on("success", function(file,response) {
                    response = jQuery.parseJSON(response);
                    console.log(response);
                    if (response.success) {
                        var url = connector_url + "get_tile&id=" + response.id;
                        jQuery.post( url, function(data){
                            jQuery("#product_images").append(data);
                            jQuery(".dz-preview").remove();
                        });
	               }
	               // TODO: better formatting
	               else {
	                   alert(response.msg);
	               }
                });
            });
    		</script>
    	');

        $sort = $this->modx->getOption('sort',$args,'seq');
        $dir = $this->modx->getOption('dir',$args,'ASC');
        
        $criteria = $this->modx->newQuery('Tile');

        $criteria->sortby($sort,$dir);
        $Tiles = $this->modx->getCollection('Tile',$criteria);

        $data = array();
        $data['mgr_controller_url'] = $this->mgr_controller_url;
        $data['tiles'] = '';
        if ($Tiles) {
            foreach ($Tiles as $T) {
                $tdata = $T->toArray();
                $tdata['mgr_controller_url'] = $this->mgr_controller_url;
                $data['tiles'] .= $this->_load_view('tile.php',$tdata);
            }
        }
        
        return $this->_load_view('list.php',$data);
*/
    }
            
}
/*EOF*/