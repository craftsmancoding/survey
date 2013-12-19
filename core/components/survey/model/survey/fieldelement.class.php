<?php
abstract class FieldElement {

    public $modx;
    public $tpl;
    public $config = array();
        
    public function __construct($modx,$config) {
        $this->modx = &$modx;
        $this->config = &$config;
    }
    
    /**
     * Draw the form field element
     *
     * @param object $question containing all info about the question
     * @param object $data
     * @return string HTML for the form field element
     */
    public function draw($question,$data) {
        $uniqid = uniqid();
        $chunk = $this->modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
        $chunk->setCacheable(false);
        $tpl = file_get_contents(dirname(dirname(dirname(__FILE__))).'/elements/chunks/'.$this->tpl.'.tpl');
        $props = $question->toArray();
        $props['value'] = ($data->get('value')) ? $data->get('value') : $question->get('default');
        $props['id'] = 'question_'.$question->get('question_id');
        $props['name'] = 'questions[ '.$question->get('question_id').' ]';
        $props['label'] = $question->get('text');
        
        $props['value'] = htmlspecialchars($props['value']);
        return $chunk->process($props, $tpl);
    }
}
/*EOF*/