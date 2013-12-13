<?php
$xpdo_meta_map['Response']= array (
  'package' => 'survey',
  'version' => '1.0',
  'table' => 'responses',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'response_id' => NULL,
    'survey_id' => NULL,
    'user_id' => NULL,
    'identifier' => NULL,
    'ip' => NULL,
    'user_agent' => '',
    'is_complete' => 0,
  ),
  'fieldMeta' => 
  array (
    'response_id' => 
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
    'identifier' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'comment' => 'Used to anonymize',
    ),
    'ip' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '16',
      'phptype' => 'string',
      'null' => true,
    ),
    'user_agent' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'comment' => 'For a papertrail',
    ),
    'is_complete' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'comment' => 'Goes to 1 when all required questions are answered',
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
        'response_id' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'identifier' => 
    array (
      'alias' => 'identifier',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'identifier' => 
        array (
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
