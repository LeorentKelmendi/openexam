<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class LocksMigration_090 extends Migration {

  public function up() {
    $this->morphTable(
      'locks', array(
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
            'computer_id', array(
              'type' => Column::TYPE_INTEGER,
              'size' => 11,
              'after' => 'id',
            )
          ),
          new Column(
            'exam_id', array(
              'type' => Column::TYPE_INTEGER,
              'size' => 11,
              'after' => 'computer_id',
            )
          ),
          new Column(
            'acquired', array(
              'type' => Column::TYPE_DATE,
              'notNull' => true,
              'size' => 1,
              'after' => 'exam_id',
            )
          ),
        ),
        'indexes' => array(
          new Index('PRIMARY', array('id')),
        ),
        'options' => array(
          'TABLE_TYPE' => 'BASE TABLE',
          'AUTO_INCREMENT' => '1',
          'ENGINE' => 'MyISAM',
          'TABLE_COLLATION' => 'utf8_general_ci',
        ),
      )
    );
  }

}
