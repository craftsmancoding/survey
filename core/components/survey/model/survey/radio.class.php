<?php
require_once dirname(__FILE__).'/fieldelement.class.php';

class Radio extends FieldElement {

    public $tpl = 'radio';

    /**
     * Custom tpls:
     *      radioTpl
     *      itemTpl
     */    
    public function draw($question,$data) {
        $uniqid = uniqid();
        $chunk = $this->modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
        $chunk->setCacheable(false);
        
        $tpl = $this->modx->getOption('radioTpl', $this->config);
        if (!$tpl) {
            $tpl = file_get_contents(dirname(dirname(dirname(__FILE__))).'/elements/chunks/'.$this->tpl.'.tpl');
        }
        $item_tpl = $this->modx->getOption('itemTpl', $this->config);
        if (!$item_tpl) {
            $item_tpl = file_get_contents(dirname(dirname(dirname(__FILE__))).'/elements/chunks/radio_item.tpl');
        }
        $props = $question->toArray();
        $props['label'] = $question->get('text');
        $props['options'] = '';
        $props['value.label'] = ''; // if the value is "m", the value.label is "male"

        $current_value = ($data->get('value')) ? $data->get('value') : $question->get('default');

        $props['name'] = 'questions[ '.$question->get('question_id').' ]';
        
        $options = json_decode($question->get('options'),true);

        if (is_array($options)) {
            foreach($options as $k => $v) {
                $item = $this->modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
                $item->setCacheable(false);
                $props['id'] = 'question_'.$question->get('question_id').'_'.$k;
                $props['is_checked'] = '';
                $props['value'] = $k;
                $placeholder = $this->config['namespace'].$k.'.checked';
                if ($k == $current_value) {
                    $this->modx->setPlaceholder($placeholder, ' checked="checked"');
                    $placeholder2 = $this->config['namespace'].'value.desc';
                    $this->modx->setPlaceholder($placeholder2, $v);
                    $props['is_checked'] = ' checked="checked"';
                    $props['value.label'] = $v; 
                }
                $this->placeholders[] = $placeholder;
                
                $props['options'] .= $item->process($props, $item_tpl);
            }
        }
        // Special inputs e.g. 1-12
        elseif (preg_match('/^(\d+)\-(\d+)$/',$question->get('options'),$m)) {
            for ( $i= $m[1]; $i <= $m[2]; $i++ ) {
                $item = $this->modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
                $item->setCacheable(false);
                $props['id'] = 'question_'.$question->get('question_id').'_'.$i;
                $props['is_checked'] = '';
                $placeholder = $this->config['namespace'].$i.'.checked';
                if ($i == $current_value) {
                    $this->modx->setPlaceholder($placeholder, ' checked="checked"');
                    $props['is_checked'] = ' checked="checked"';
                    $props['value.label'] = $i;
                }
                $this->placeholders[] = $placeholder;
                $props['options'] .= $item->process($props, $item_tpl);
            }
        }
        //$props['value'] = htmlspecialchars($props['value']);
        $placeholder = $this->config['namespace'].'options';
        $this->modx->setPlaceholder($placeholder,$props['options']);
        $this->placeholders[] = $placeholder;        
        $placeholder = $this->config['namespace'].'name';
        $this->modx->setPlaceholder($placeholder,$props['name']);
        $this->placeholders[] = $placeholder;        
        $props['id'] = 'question_'.$question->get('question_id');
        return $chunk->process($props, $tpl);
    }
}
/*EOF*/