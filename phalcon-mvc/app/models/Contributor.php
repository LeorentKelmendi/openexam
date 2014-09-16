<?php

namespace OpenExam\Models;

/**
 * The contributor model.
 * 
 * Represents a user having the contributor role.
 * 
 * @property Exam $Exam The related exam.
 * @author Anders Lövgren (QNET/BMC CompDept)
 */
class Contributor extends ModelBase
{

        /**
         * This object ID.
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

        protected function initialize()
        {
                parent::initialize();
                $this->belongsTo('exam_id', 'OpenExam\Models\Exam', 'id', array('foreignKey' => true, 'alias' => 'Exam'));
        }

        public function getSource()
        {
                return 'contributors';
        }

}
