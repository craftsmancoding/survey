<?php
require_once dirname(__FILE__).'/fieldelement.class.php';

class Dropdown extends FieldElement {

    public $tpl = 'dropdown';
    
    /**
     * Custom tpls:
     *      dropdownTpl
     *      optionTpl
     */
    public function draw($question,$data) {
        $uniqid = uniqid();
        $chunk = $this->modx->newObject('modChunk', array('name' => "{tmp}-{$uniqid}"));
        $chunk->setCacheable(false);
        $tpl = $this->modx->getOption('dropdownTpl', $this->config);
        if (!$tpl) {
            $tpl = file_get_contents(dirname(dirname(dirname(__FILE__))).'/elements/chunks/'.$this->tpl.'.tpl');
        }
//        $optionTpl = '<option value="[[+value]]"[[+is_selected]]>[[+label]]</option>';
        $optionTpl = $this->modx->getOption('optionTpl', $this->config, '<option value="[[+value]]"[[+is_selected]]>[[+label]]</option>');
        
        $props = $question->toArray();
        $props['value'] = ($data->get('value')) ? $data->get('value') : $question->get('default');
        $props['id'] = 'question_'.$question->get('question_id');
        $props['name'] = 'questions[ '.$question->get('question_id').' ]';
        $props['label'] = $question->get('text');
        $props['options'] = '';
        $props['value.label'] = ''; // if the value is "m", the value.label is "male"

        $options = json_decode($question->get('options'),true);

        // For regular array options
        if (is_array($options)) {
            foreach($options as $k => $v) {
                $v = trim($v);
                $is_selected = '';
                if ($k == $props['value']) {
                    $this->modx->setPlaceholder($this->config['namespace'].'.'.$k.'.selected', ' selected="selected"');
                    $this->placeholders[] = $this->config['namespace'].'.'.$k.'.selected';
                    $is_selected = ' selected="selected"';
                    $props['value.label'] = $v;
                    $this->modx->setPlaceholder($this->config['namespace'].'value.label', $v);
                    $this->placeholders[] = $this->config['namespace'].'value.label';
                    error_log($this->config['namespace'].'.value.label '.$v);
                }
                
                //$props['options'] .= '<option value="'.htmlspecialchars($k).'"'.$is_selected.'>'.$v.'</option>';
                $props['options'] .= str_replace(
                    array('[[+value]]','[[+is_selected]]','[[+label]]'), 
                    array(htmlspecialchars($k),$is_selected, $v), 
                    $optionTpl
                );
            }
        }
        // For shorthand options, e.g. 1-12 
        elseif (preg_match('/^(\d+)\-(\d+)$/',$question->get('options'),$m)) {
            for ( $i= $m[1]; $i <= $m[2]; $i++ ) {
                $is_selected = '';
                if ($i == $props['value']) {
                    $this->modx->setPlaceholder($this->config['namespace'].'.'.$i.'.selected', ' selected="selected"');
                    $this->placeholders[] = $this->config['namespace'].'.'.$i.'.selected';
                    $is_selected = ' selected="selected"';
                    $this->modx->setPlaceholder($this->config['namespace'].'value.label', $i);
                    $this->placeholders[] = $this->config['namespace'].'value.label';
                }
                //$props['options'] .= '<option value="'.htmlspecialchars($i).'"'.$is_selected.'>'.htmlspecialchars($i).'</option>';            
                $props['options'] .= str_replace(
                    array('[[+value]]','[[+is_selected]]','[[+label]]'), 
                    array(htmlspecialchars($i),$is_selected, $i), 
                    $optionTpl
                );
            }
        }
        else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Invalid input for Dropdown field.', __CLASS__);
        }

        $placeholder = $this->config['namespace'].'options';
        $this->modx->setPlaceholder($placeholder,$props['options']);
        $this->placeholders[] = $placeholder;
        $placeholder = $this->config['namespace'].'name';
        $this->modx->setPlaceholder($placeholder,$props['name']);
        $this->placeholders[] = $placeholder;

        $props['value'] = htmlspecialchars($props['value']);
        return $chunk->process($props, $tpl);
    }
}
/*EOF*/