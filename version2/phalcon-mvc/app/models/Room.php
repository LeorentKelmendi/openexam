<?php

namespace OpenExam\Models;

use Phalcon\Mvc\Model\Relation;

/**
 * The room model.
 * 
 * @property Computer[] $computers Computers that belongs to this room.
 * @author Anders Lövgren (QNET/BMC CompDept)
 */
class Room extends ModelBase
{

        /**
         * The object ID.
         * @var integer
         */
        public $id;
        /**
         * The room name.
         * @var string
         */
        public $name;
        /**
         * The room description.
         * @var string
         */
        public $description;

        protected function initialize()
        {
                parent::initialize();

                $this->hasMany('id', 'OpenExam\Models\Computer', 'room_id', array(
                        'alias' => 'computers'
                ));
        }

        public function getSource()
        {
                return 'rooms';
        }

}