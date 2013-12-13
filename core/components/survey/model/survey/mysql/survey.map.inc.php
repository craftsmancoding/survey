<?php
$xpdo_meta_map['Survey']= array (
  'package' => 'survey',
  'version' => '1.0',
  'table' => 'surveys',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'survey_id' => NULL,
    'user_id' => NULL,
    'name' => NULL,
    'description' => NULL,
    'date_open' => NULL,
    'date_closed' => NULL,
    'is_active' => 1,
    'is_editable' => 1,
    'seq' => NULL,
    'timestamp_created' => 'CURRENT_TIMESTAMP',
    'timestamp_modified' => NULL,
  ),
  'fieldMeta' => 
  array (
    'survey_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '4',
      'phptype' => 'integer',
      'null' => false,
      'index' => 'pk',
      'generated' => 'native',
    ),
    'user_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => true,
      'comment' => 'MODX user id',
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'description' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'date_open' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'comment' => 'When can users start filling out this survey?',
    ),
    'date_closed' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'comment' => 'When will responses be closed?',
    ),
    'is_active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
    ),
    'is_editable' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'attributes' => 'unsigned',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
      'comment' => 'If editable, then users can change their answers after submitting them',
    ),
    'seq' => 
    array (
      'dbtype' => 'int',
      'precision' => '3',
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
        'survey_id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Questions' => 
    array (
      'class' => 'Question',
      'local' => 'survey_id',
      'foreign' => 'survey_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Responses' => 
    array (
      'class' => 'Response',
      'local' => 'survey_id',
      'foreign' => 'survey_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Data' => 
    array (
      'class' => 'Data',
      'local' => 'survey_id',
      'foreign' => 'survey_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
