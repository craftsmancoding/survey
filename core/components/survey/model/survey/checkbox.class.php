<?php
require_once dirname(__FILE__).'/fieldelement.class.php';

class Checkbox extends FieldElement {
    
    public $tpl = 'checkbox';

    /**
     * Custom tpls:
     *      dropdownTpl
     *      optionTpl
     */    
    public function draw($question,$data) {
        $uniqid = uniqid();
        $chunk = $this->modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
        $chunk->setCacheable(false);
        $tpl = $this->modx->getOption('checkboxTpl', $this->config);
        if (!$tpl) {
            $tpl = file_get_contents(dirname(dirname(dirname(__FILE__))).'/elements/chunks/'.$this->tpl.'.tpl');
        }

        $props = $question->toArray();
        $props['value'] = $data->get('value');
        $props['id'] = 'question_'.$question->get('question_id');
        $props['name'] = 'questions[ '.$question->get('question_id').' ]';
        $props['label'] = $question->get('text');
        $props['options'] = '';
        $props['is_checked'] = '';

        $is_checked = '';
        $placeholder = $this->config['namespace'].'checked';
        if ($props['value']) {
            $this->modx->setPlaceholder($placeholder, ' checked="checked"');
            $props['is_checked'] = ' checked="checked"';
        }
        $this->placeholders[] = $placeholder;
        
        $placeholder = $this->config['namespace'].'name';
        $this->modx->setPlaceholder($placeholder,$props['name']);
        $this->placeholders[] = $placeholder;
        
        $props['value'] = htmlspecialchars($props['value']);
        return $chunk->process($props, $tpl);
    
    }
}
/*EOF*/