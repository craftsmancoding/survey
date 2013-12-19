<?php
require_once dirname(__FILE__).'/fieldelement.class.php';

class Dropdown extends FieldElement {

    public $tpl = 'dropdown';
    
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
        $props['options'] = '';
        
        $options = json_decode($question->get('options'),true);
//        print_r($options); exit;
        if (is_array($options)) {
            foreach($options as $k => $v) {
                $is_selected = '';
                if ($v == $props['value']) {
                    $this->modx->setPlaceholder($this->config['namespace'].'.'.$k.'.selected', ' selected="selected"');
                    $is_selected = ' selected="selected"';
                }
                
                $props['options'] .= '<option value="'.htmlspecialchars($k).'"'.$is_selected.'>'.htmlspecialchars($v).'</option>';
            }
        }
        $props['value'] = htmlspecialchars($props['value']);
        return $chunk->process($props, $tpl);
    }
}
/*EOF*/