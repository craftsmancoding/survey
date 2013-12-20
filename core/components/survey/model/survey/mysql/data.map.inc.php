<?php
$xpdo_meta_map['Data']= array (
  'package' => 'survey',
  'version' => '1.0',
  'table' => 'data',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'data_id' => NULL,
    'survey_id' => NULL,
    'response_id' => NULL,
    'question_id' => NULL,
    'value' => NULL,
    'timestamp_created' => 'CURRENT_TIMESTAMP',
    'timestamp_modified' => NULL,
  ),
  'fieldMeta' => 
  array (
    'data_id' => 
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
    'response_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'question_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
    ),
    'value' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
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
        'data_id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
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
    'Response' => 
    array (
      'class' => 'Response',
      'local' => 'response_id',
      'foreign' => 'response_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Question' => 
    array (
      'class' => 'Question',
      'local' => 'question_id',
      'foreign' => 'question_id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
