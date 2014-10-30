<?php

namespace OpenExam\Models;

use OpenExam\Library\Core\Exam\Grades;
use OpenExam\Library\Core\Exam\State;
use OpenExam\Library\Model\Filter;
use OpenExam\Library\Security\Roles;
use Phalcon\DI as PhalconDI;
use Phalcon\DiInterface;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Query\Builder;

/**
 * The exam model.
 * 
 * Represents an exam. This class is the central model to which most other
 * models are related.
 * 
 * An exam is in one of these states: preparing, upcoming, active, finished 
 * or decoded.
 * 
 * The grades property (array) is defined as JSON in the database. It is
 * either an object defining grades or an array defining the function body
 * for a function that evaluates the final score for a single student on 
 * the exam:
 * 
 * <code>
 * { data: { "U":"0", "G":"55", "VG":"80" } }
 * { func: { // source code } }
 * </code>
 * 
 * @property Contributor[] $contributors The contributors for this exam.
 * @property Decoder[] $decoders The decoders for this exam.
 * @property Invigilator[] $invigilators The invigilators for this exam.
 * @property Lock[] $locks The computer locks aquired for this exam.
 * @property Resource[] $resources The multimedia or resource files associated with this exam.
 * @property Question[] $questions The questions that belongs to this exam.
 * @property Student[] $students The students assigned to this exam.
 * @property Topic[] $topics The topics associated with this exam.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 */
class Exam extends ModelBase
{

        /**
         * Show responsible people for examination.
         */
        const RESULT_EXPOSE_EMPLOYEES = 1;
        /**
         * Include statistics of all students.
         */
        const RESULT_OTHERS_STATISTICS = 2;

        /**
         * This object ID.
         * @var integer
         */
        public $id;
        /**
         * The name of the exam.
         * @var string
         */
        public $name;
        /**
         * The exam description.
         * @var string
         */
        public $descr;
        /**
         * The exam start date/time (might be null).
         * @var string
         */
        public $starttime;
        /**
         * The exam end date/time (might be null).
         * @var string
         */
        public $endtime;
        /**
         * The exam create date/time.
         * @var string
         */
        public $created;
        /**
         * The exam update date/time.
         * @var string
         */
        public $updated;
        /**
         * The creator of the exam.
         * @var string
         */
        public $creator;
        /**
         * Bitmask of exposed details in result (see RESULT_XXX constants).
         * @var integer
         */
        public $details;
        /**
         * Is this exam decoded?
         * @var bool
         */
        public $decoded;
        /**
         * The organization unit.
         * @var string
         */
        public $orgunit;
        /**
         * The exam grades.
         * @var string
         */
        public $grades;
        /**
         * Is this exam a testcase?
         * @var bool
         */
        public $testcase;
        /**
         * Does this exam require client lockdown?
         * @var bool
         */
        public $lockdown;
        /**
         * Examination state (bitmask).
         * @var int 
         */
        public $state;
        /**
         * Examination flags (e.g. decodable).
         * @var string[]
         */
        public $flags;

        protected function initialize()
        {
                parent::initialize();

                $this->hasMany('id', 'OpenExam\Models\Contributor', 'exam_id', array('alias' => 'contributors'));
                $this->hasMany('id', 'OpenExam\Models\Decoder', 'exam_id', array('alias' => 'decoders'));
                $this->hasMany('id', 'OpenExam\Models\Invigilator', 'exam_id', array('alias' => 'invigilators'));
                $this->hasMany('id', 'OpenExam\Models\Lock', 'exam_id', array('alias' => 'locks'));
                $this->hasMany('id', 'OpenExam\Models\Question', 'exam_id', array('alias' => 'questions'));
                $this->hasMany("id", "OpenExam\Models\Resource", "exam_id", array("alias" => 'resources'));
                $this->hasMany('id', 'OpenExam\Models\Student', 'exam_id', array('alias' => 'students'));
                $this->hasMany('id', 'OpenExam\Models\Topic', 'exam_id', array('alias' => 'topics'));

                $this->addBehavior(new Timestampable(array(
                        'beforeValidationOnCreate' => array(
                                'field'  => 'updated',
                                'format' => 'Y-m-d H:i:s'
                        ),
                        'beforeValidationOnUpdate' => array(
                                'field'  => 'updated',
                                'format' => 'Y-m-d H:i:s'
                        )
                )));

                $this->addBehavior(new Timestampable(array(
                        'beforeValidationOnCreate' => array(
                                'field'  => 'created',
                                'format' => 'Y-m-d H:i:s'
                        )
                )));
        }

        /**
         * Called before model is created.
         */
        protected function beforeValidationOnCreate()
        {
                if (!isset($this->details)) {
                        $this->details = $this->getDI()->get('config')->result->details;
                }
                if (!isset($this->decoded)) {
                        $this->decoded = false;
                }
                if (!isset($this->testcase)) {
                        $this->testcase = false;
                }
                if (!isset($this->lockdown)) {
                        $this->lockdown = false;
                }
        }

        /**
         * Called before model is saved.
         */
        protected function beforeSave()
        {
                $this->decoded = $this->decoded ? 'Y' : 'N';
                $this->testcase = $this->testcase ? 'Y' : 'N';
                $this->lockdown = $this->lockdown ? 'Y' : 'N';
        }

        /**
         * Called after model is saved.
         */
        protected function afterSave()
        {
                $this->decoded = $this->decoded == 'Y';
                $this->testcase = $this->testcase == 'Y';
                $this->lockdown = $this->lockdown == 'Y';
        }

        /**
         * Called after the model was read.
         */
        protected function afterFetch()
        {
                $this->decoded = $this->decoded == 'Y';
                $this->testcase = $this->testcase == 'Y';
                $this->lockdown = $this->lockdown == 'Y';
                $state = new State($this);
                $this->state = $state->getState();
                $this->flags = $state->getFlags();
                parent::afterFetch();
        }

        public function getSource()
        {
                return 'exams';
        }

        /**
         * Get examination state.
         * @return State
         */
        public function getState()
        {
                return new State($this);
        }

        /**
         * Get examination graduation.
         * @return Grades
         */
        public function getGrades()
        {
                return new Grades($this);
        }

        /**
         * Get filter for result set.
         * @param array $params The query parameters.
         * @return Filter The result set filter object.
         */
        public function getFilter($params)
        {
                $filter = array();

                foreach (array('state', 'flags') as $key) {
                        if (isset($params[$key])) {
                                $filter[$key] = $params[$key];
                        }
                }

                if (count($filter) != 0) {
                        return new Filter($filter);
                } else {
                        return parent::getFilter($params);
                }
        }

        /**
         * Specialization of the query() function for the exam model.
         * 
         * This function provides role based access to the exam model. If the 
         * primary role is set, then the returned criteria is prepared with
         * inner joins against resp. role table.
         * 
         * The critera should ensure that only exams related to caller and
         * requested role is returned.
         * 
         * @param DiInterface $dependencyInjector
         * @return Criteria  
         */
        public static function query($dependencyInjector = null)
        {
                if (!isset($dependencyInjector)) {
                        $dependencyInjector = PhalconDI::getDefault();
                }

                $user = $dependencyInjector->get('user');
                $role = $user->getPrimaryRole();

                if ($user->getUser() == null) {
                        return parent::query($dependencyInjector);
                }
                if ($user->hasPrimaryRole() == false || Roles::isGlobal($role)) {
                        return parent::query($dependencyInjector);
                }

                $criteria = parent::query($dependencyInjector);
                if ($role == Roles::CORRECTOR) {
                        $criteria
                            ->join(self::getRelation('question'), self::getRelation('exam', 'id', 'exam_id', 'question'))
                            ->join(self::getRelation('corrector'), self::getRelation('question', 'id', 'question_id', 'corrector'))
                            ->where(sprintf("user = '%s'", $user->getPrincipalName()));
                } elseif ($role == Roles::CREATOR) {
                        $criteria->where(sprintf("creator = '%s'", $user->getPrincipalName()));
                } else {
                        $criteria
                            ->join(self::getRelation($role), self::getRelation('exam', 'id', 'exam_id'))
                            ->where(sprintf("user = '%s'", $user->getPrincipalName()));
                }

                return $criteria;
        }

        /**
         * Specialization of find() for exam model.
         * 
         * This function provides checked access for queries against the exam
         * model. If primary role is unset, user is not authenticated or if
         * accessed using a global role (teacher, admin, trsuted or custom),
         * then the behavour is the same as calling parent::find().
         * 
         * @param array $parameters The query parameters.
         * @return mixed
         * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_Model_Query_Builder.html
         * @uses Model::find()
         */
        public static function find($parameters = null)
        {
                $dependencyInjector = PhalconDI::getDefault();

                $user = $dependencyInjector->get('user');
                $role = $user->getPrimaryRole();

                // 
                // Wrap string search in array:
                // 
                if (is_string($parameters)) {
                        $parameters = array($parameters);
                }

                // 
                // Don't accept access to other models:
                // 
                if (isset($parameters['models'])) {
                        unset($parameters['models']);
                }

                // 
                // Group by exam by default:
                // 
                if (!isset($parameters['group'])) {
                        $parameters['group'] = self::getRelation('exam') . '.id';
                }

                // 
                // Use parent find() if user is not authenticated:
                // 
                if ($user->getUser() == null) {
                        return parent::find($parameters);
                }

                // 
                // Use parent find() if primary role is unset or if accessed 
                // using global role (these are not tied to any exam).
                // 
                if ($user->hasPrimaryRole() == false || Roles::isGlobal($role)) {
                        return parent::find($parameters);
                }

                // 
                // Create the builder using supplied options (conditions,
                // order, limit, ...):
                // 
                $builder = new Builder($parameters);

                if ($role == Roles::CORRECTOR) {
                        $builder
                            ->from(self::getRelation('exam'))
                            ->join(self::getRelation('question'), self::getRelation('exam', 'id', 'exam_id', 'question'))
                            ->join(self::getRelation('corrector'), self::getRelation('question', 'id', 'question_id', 'corrector'))
                            ->andWhere(sprintf("user = '%s'", $user->getPrincipalName()));
                } elseif ($role == Roles::CREATOR) {
                        $builder
                            ->from(self::getRelation('exam'))
                            ->andWhere(sprintf("creator = '%s'", $user->getPrincipalName()));
                } else {
                        $builder
                            ->from(self::getRelation('exam'))
                            ->join(self::getRelation($role), self::getRelation('exam', 'id', 'exam_id'))
                            ->andWhere(sprintf("user = '%s'", $user->getPrincipalName()));
                }

                return $builder->getQuery()->execute();
        }

}
