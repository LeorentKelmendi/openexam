<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Mvc\Model\Migration;

class TeachersMigration_090 extends Migration {

  public function up() {
    $this->morphTable(
      'teachers', array(
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
            'user', array(
              'type' => Column::TYPE_VARCHAR,
              'notNull' => true,
              'size' => 10,
              'after' => 'id',
            )
          ),
        ),
        'indexes' => array(
          new Index('PRIMARY', array('id')),
        ),
        'options' => array(
          'TABLE_TYPE' => 'BASE TABLE',
          'AUTO_INCREMENT' => '12',
          'ENGINE' => 'MyISAM',
          'TABLE_COLLATION' => 'utf8_general_ci',
        ),
      )
    );
  }

}
