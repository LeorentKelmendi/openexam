<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

/**
 * Class ComputersMigration_2015
 */
class ComputersMigration_2015 extends Migration {
  /**
   * Define the table structure
   *
   * @return void
   */
  public function morph() {
    $this->morphTable('computers', array(
      'columns' => array(
        new Column(
          'id',
          array(
            'type' => Column::TYPE_INTEGER,
            'notNull' => true,
            'autoIncrement' => true,
            'size' => 11,
            'first' => true,
          )
        ),
        new Column(
          'room_id',
          array(
            'type' => Column::TYPE_INTEGER,
            'default' => "0",
            'size' => 11,
            'after' => 'id',
          )
        ),
        new Column(
          'hostname',
          array(
            'type' => Column::TYPE_VARCHAR,
            'size' => 100,
            'after' => 'room_id',
          )
        ),
        new Column(
          'ipaddr',
          array(
            'type' => Column::TYPE_VARCHAR,
            'notNull' => true,
            'size' => 45,
            'after' => 'hostname',
          )
        ),
        new Column(
          'port',
          array(
            'type' => Column::TYPE_INTEGER,
            'notNull' => true,
            'size' => 11,
            'after' => 'ipaddr',
          )
        ),
        new Column(
          'password',
          array(
            'type' => Column::TYPE_VARCHAR,
            'notNull' => true,
            'size' => 32,
            'after' => 'port',
          )
        ),
        new Column(
          'created',
          array(
            'type' => Column::TYPE_DATETIME,
            'notNull' => true,
            'size' => 1,
            'after' => 'password',
          )
        ),
        new Column(
          'updated',
          array(
            'type' => Column::TYPE_TIMESTAMP,
            'default' => "CURRENT_TIMESTAMP",
            'notNull' => true,
            'size' => 1,
            'after' => 'created',
          )
        ),
      ),
      'indexes' => array(
        new Index('PRIMARY', array('id'), 'PRIMARY'),
        new Index('computers_ibfk_1', array('room_id'), null),
      ),
      'references' => array(
        new Reference(
          'computers_ibfk_1',
          array(
            'referencedSchema' => 'openexam2prod',
            'referencedTable' => 'rooms',
            'columns' => array('room_id', 'room_id', 'room_id', 'room_id'),
            'referencedColumns' => array('id', 'id', 'id', 'id'),
            'onUpdate' => 'RESTRICT',
            'onDelete' => 'RESTRICT',
          )
        ),
      ),
      'options' => array(
        'TABLE_TYPE' => 'BASE TABLE',
        'AUTO_INCREMENT' => '29220',
        'ENGINE' => 'InnoDB',
        'TABLE_COLLATION' => 'utf8_general_ci',
      ),
    )
    );
  }

  /**
   * Run the migrations
   *
   * @return void
   */
  public function up() {

  }

  /**
   * Reverse the migrations
   *
   * @return void
   */
  public function down() {

  }

}
