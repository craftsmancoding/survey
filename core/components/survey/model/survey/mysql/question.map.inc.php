<?php
$xpdo_meta_map['Question']= array (
  'package' => 'survey',
  'version' => '1.0',
  'table' => 'questions',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'question_id' => NULL,
    'survey_id' => NULL,
    'user_id' => NULL,
    'text' => '',
    'type' => 'text',
    'default' => '',
    'options' => NULL,
    'page' => NULL,
    'is_active' => 1,
    'is_required' => 1,
    'seq' => NULL,
    'timestamp_created' => 'CURRENT_TIMESTAMP',
    'timestamp_modified' => NULL,
  ),
  'fieldMeta' => 
  array (
    'question_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
    'survey_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'user_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
      'comment' => 'MODX user id',
    ),
    'text' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'comment' => 'The actual question',
    ),
    'type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '16',
      'phptype' => 'string',
      'default' => 'text',
      'null' => false,
      'comment' => 'Identifies the type of response (e.g. text, dropdown)',
    ),
    'default' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'default' => '',
      'null' => false,
      'comment' => 'Prepopulate the field',
    ),
    'options' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
      'comment' => 'optionally used by some types, e.g. dropdown req\'s a list of options',
    ),
    'page' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'comment' => 'optionally used to group questions into pages',
    ),
    'is_active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
      'comment' => 'Used to disable/enable products',
    ),
    'is_required' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'default' => 1,
      'comment' => 'Required Qs must be answered before the survey can be completed.',
    ),
    'seq' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
    ),
    'timestamp_created' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => true,
      'default' => 'CURRENT_TIMESTAMP',
    ),
    'timestamp_modified' => 
    array (
      'dbtype' => 'timestamp',
      'phptype' => 'timestamp',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'columns' => 
      array (
        'question_id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Data' => 
    array (
      'class' => 'Data',
      'local' => 'question_id',
      'foreign' => 'question_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Survey' => 
    array (
      'class' => 'Survey',
      'local' => 'survey_id',
      'foreign' => 'survey_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
