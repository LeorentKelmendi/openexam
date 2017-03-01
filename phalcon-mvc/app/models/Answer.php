<?php

// 
// The source code is copyrighted, with equal shared rights, between the
// authors (see the file AUTHORS) and the OpenExam project, Uppsala University 
// unless otherwise explicit stated elsewhere.
// 
// File:    Answer.php
// Created: 2014-02-24 07:04:58
// 
// Author:  Anders Lövgren (Computing Department at BMC, Uppsala University)
// 

namespace OpenExam\Models;

use OpenExam\Library\Model\Behavior\FilterText;
use Phalcon\Mvc\Model\Validator\Uniqueness;

/**
 * The answer model.
 * 
 * @property File[] $files The files associated with this answer.
 * @property Result $result The related result.
 * @property Question $question The related question.
 * @property Student $student The related student.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 */
class Answer extends ModelBase
{

        /**
         * This object ID.
         * @var integer
         */
        public $id;
        /**
         * The question ID.
         * @var integer
         */
        public $question_id;
        /**
         * The student ID.
         * @var integer
         */
        public $student_id;
        /**
         * Is already answered?
         * @var bool
         */
        public $answered;
        /**
         *
         * @var string
         */
        public $answer;
        /**
         *
         * @var string
         */
        public $comment;

        protected function initialize()
        {
                parent::initialize();

                $this->hasMany("id", "OpenExam\Models\File", "answer_id", array(
                        'alias' => 'files'
                ));
                $this->hasOne('id', 'OpenExam\Models\Result', 'answer_id', array(
                        'alias' => 'result'
                ));
                $this->belongsTo('question_id', 'OpenExam\Models\Question', 'id', array(
                        'foreignKey' => true,
                        'alias'      => 'question'
                ));
                $this->belongsTo('student_id', 'OpenExam\Models\Student', 'id', array(
                        'foreignKey' => true,
                        'alias'      => 'student'
                ));

                // 
                // TODO: better do filtering on client side.
                // 
                $this->addBehavior(new FilterText(array(
                        'beforeValidationOnCreate' => array(
                                'fields' => array('answer', 'comment')
                        ),
                        'beforeValidationOnUpdate' => array(
                                'fields' => array('answer', 'comment')
                        )
                    )
                ));
        }

        /**
         * Get source table name.
         * @return string
         */
        public function getSource()
        {
                return 'answers';
        }

        /**
         * Get table column map.
         * @return array
         */
        public function columnMap()
        {
                return array(
                        'id'          => 'id',
                        'question_id' => 'question_id',
                        'student_id'  => 'student_id',
                        'answered'    => 'answered',
                        'answer'      => 'answer',
                        'comment'     => 'comment'
                );
        }

        /**
         * Validates business rules.
         * @return boolean
         */
        protected function validation()
        {
                $this->validate(new Uniqueness(
                    array(
                        'field'   => array('question_id', 'student_id'),
                        'message' => "Student answer has already been inserted"
                    )
                ));

                if ($this->validationHasFailed() == true) {
                        return false;
                }
        }

        /**
         * Called before model is created.
         */
        protected function beforeValidationOnCreate()
        {
                if (!isset($this->answered)) {
                        $this->answered = false;
                }
        }

        /**
         * Called before model is saved.
         */
        protected function beforeSave()
        {
                $this->answered = $this->answered ? 'Y' : 'N';
        }

        /**
         * Called after model is saved.
         */
        protected function afterSave()
        {
                $this->answered = $this->answered == 'Y';
        }

        /**
         * Called after the model was read.
         */
        protected function afterFetch()
        {
                $this->answered = $this->answered == 'Y';
                parent::afterFetch();
        }

        /**
         * Updates a model instance.
         * 
         * This method overides parent method to convert values for boolean
         * properties to real boolean values. It also checks whether any data
         * has changes and by-passes update otherwise.
         * 
         * @param mixed $data
         * @param mixed $whiteList
         * @return boolean
         */
        public function update($data = null, $whiteList = null)
        {
                // 
                // Convert AJAX property values to bool:
                // 
                if (is_int($this->answered)) {
                        if ($this->answered == 0) {
                                $this->answered = false;
                        } else {
                                $this->answered = true;
                        }
                }

                // 
                // Assign data if requested:
                // 
                if (isset($data)) {
                        $this->assign($data, null, $whiteList);
                }

                // 
                // Call parent update() on modified:
                // 
                if (!$this->hasSnapshotData()) {
                        return parent::update();
                }
                if ($this->hasChanged()) {
                        return parent::update();
                }

                // 
                // No update is a success:
                // 
                return true;
        }

}
