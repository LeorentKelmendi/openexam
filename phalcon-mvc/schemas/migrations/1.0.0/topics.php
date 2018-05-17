<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class TopicsMigration_100 extends Migration {

  public function up() {
    $this->morphTable(
      'topics', array(
        'columns' => array(
          new Column(
            'id', array(
              'type' => Column::TYPE_INTEGER,
              'notNull' => true,
              'autoIncrement' => true,
              'size' => 11,
              'first' => true,
            )
          ),
          new Column(
            'exam_id', array(
              'type' => Column::TYPE_INTEGER,
              'notNull' => true,
              'size' => 11,
              'after' => 'id',
            )
          ),
          new Column(
            'name', array(
              'type' => Column::TYPE_VARCHAR,
              'notNull' => true,
              'size' => 50,
              'after' => 'exam_id',
            )
          ),
          new Column(
            'randomize', array(
              'type' => Column::TYPE_INTEGER,
              'notNull' => true,
              'size' => 11,
              'after' => 'name',
            )
          ),
        ),
        'indexes' => array(
          new Index('PRIMARY', array('id')),
        ),
        'options' => array(
          'TABLE_TYPE' => 'BASE TABLE',
          'AUTO_INCREMENT' => '1',
          'ENGINE' => 'InnoDB',
          'TABLE_COLLATION' => 'utf8_general_ci',
        ),
      )
    );
  }

}
