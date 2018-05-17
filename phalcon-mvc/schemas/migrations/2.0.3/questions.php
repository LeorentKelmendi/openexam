<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Mvc\Model\Migration;

class QuestionsMigration_203 extends Migration {

  public function up() {
    $this->morphTable(
      'questions',
      array(
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
            'exam_id',
            array(
              'type' => Column::TYPE_INTEGER,
              'notNull' => true,
              'size' => 11,
              'after' => 'id',
            )
          ),
          new Column(
            'topic_id',
            array(
              'type' => Column::TYPE_INTEGER,
              'notNull' => true,
              'size' => 11,
              'after' => 'exam_id',
            )
          ),
          new Column(
            'score',
            array(
              'type' => Column::TYPE_FLOAT,
              'notNull' => true,
              'size' => 1,
              'after' => 'topic_id',
            )
          ),
          new Column(
            'name',
            array(
              'type' => Column::TYPE_VARCHAR,
              'notNull' => true,
              'size' => 30,
              'after' => 'score',
            )
          ),
          new Column(
            'quest',
            array(
              'type' => Column::TYPE_TEXT,
              'notNull' => true,
              'size' => 1,
              'after' => 'name',
            )
          ),
          new Column(
            'status',
            array(
              'type' => Column::TYPE_CHAR,
              'notNull' => true,
              'size' => 1,
              'after' => 'quest',
            )
          ),
          new Column(
            'comment',
            array(
              'type' => Column::TYPE_TEXT,
              'size' => 1,
              'after' => 'status',
            )
          ),
          new Column(
            'grades',
            array(
              'type' => Column::TYPE_VARCHAR,
              'size' => 200,
              'after' => 'comment',
            )
          ),
        ),
        'indexes' => array(
          new Index('PRIMARY', array('id')),
          new Index('exam_id', array('exam_id')),
          new Index('questions_ibfk_2', array('topic_id')),
        ),
        'references' => array(
          new Reference('questions_ibfk_1', array(
            'referencedSchema' => 'openexam',
            'referencedTable' => 'exams',
            'columns' => array('exam_id'),
            'referencedColumns' => array('id'),
          )),
          new Reference('questions_ibfk_2', array(
            'referencedSchema' => 'openexam',
            'referencedTable' => 'topics',
            'columns' => array('topic_id'),
            'referencedColumns' => array('id'),
          )),
        ),
        'options' => array(
          'TABLE_TYPE' => 'BASE TABLE',
          'AUTO_INCREMENT' => '6',
          'ENGINE' => 'InnoDB',
          'TABLE_COLLATION' => 'utf8_general_ci',
        ),
      )
    );
  }
}
