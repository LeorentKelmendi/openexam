<?php

namespace OpenExam\Models;

use OpenExam\Library\Model\Behavior\Student as StudentBehavior;

/**
 * The student model.
 * 
 * Represents a user having the student role. The student can have an 
 * associated tag. It's usually used for storing miscellanous data that can 
 * be used in the result report process.
 * 
 * @property Answer[] $answers Answers related to this student.
 * @property Exam $exam The related exam.
 * @property Lock[] $locks Active computer/exam locks for this student.
 * @author Anders Lövgren (QNET/BMC CompDept)
 */
class Student extends Role
{

        /**
         * The object ID.
         * @var integer
         */
        public $id;
        /**
         * The exam ID.
         * @var integer
         */
        public $exam_id;
        /**
         * The user principal name (e.g. user@example.com).
         * @var string
         */
        public $user;
        /**
         * The student code (anonymous).
         * @var string
         */
        public $code;
        /**
         * Generic tag for this student (e.g. course).
         * @var string
         */
        public $tag;
        /**
         * Override start time defined exam object for this student.
         * @var string 
         */
        public $starttime;
        /**
         * Override end time defined exam object for this student.
         * @var string 
         */
        public $endtime;
        /**
         * Set finished time (after saved, the exam can't be opened again).
         * @var string 
         */
        public $finished;

        protected function initialize()
        {
                parent::initialize();

                $this->hasMany('id', 'OpenExam\Models\Answer', 'student_id', array('alias' => 'answers'));
                $this->hasMany('id', 'OpenExam\Models\Lock', 'student_id', array('alias' => 'locks'));
                $this->belongsTo('exam_id', 'OpenExam\Models\Exam', 'id', array('foreignKey' => true, 'alias' => 'exam'));

                $this->addBehavior(new StudentBehavior(array(
                        'beforeValidationOnCreate' => array(
                                'code' => true,
                                'user' => true
                        )
                )));
        }

        public function getSource()
        {
                return 'students';
        }

}
