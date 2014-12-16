<?php

namespace OpenExam\Models;

/**
 * The access definition model.
 * 
 * This class defines an IP-address restriction for an exam. The definition
 * in addr contains either:
 * 
 * <ul>
 * <li>An single IP-address.</li>
 * <li>An range of IP-addresses (e.g. 192.168.4.15 - 192.168.4.60).</li>
 * <li>Network addresses using subnet mask or CIDR-notation.</li>
 * </ul>
 * 
 * Notice:
 * -----------
 * 1. The address range are allowed to cross subnet boundaries (e.g. multiple 
 *    class C: 192.168.4.1 - 192.168.6.255).
 * 
 * Example:
 * -----------
 * <code>
 * $access->name = "BMC;Tentasalar;B1:3";
 * 
 * $access->addr = "192.168.25.67";                     // Single IP-address.
 * $access->addr = "192.168.25.0/26";                   // CIDR-notation.
 * $access->addr = "192.168.25.0/255.255.255.192";      // Subnet-notation.
 * $access->addr = "192.168.25.1-192.168.25.63";        // Address range.
 * </code>
 * @author Anders Lövgren (Computing Department at BMC, Uppsala University)
 */
class Access extends ModelBase
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
         * The access restriction name (semi-colon separated string).
         * @var string
         */
        public $name;
        /**
         * The IP address definition.
         * @var string
         */
        public $addr;

        protected function initialize()
        {
                parent::initialize();

                $this->belongsTo('exam_id', 'OpenExam\Models\Exam', 'id', array(
                        'foreignKey' => true,
                        'alias'      => 'exam'
                ));
        }

        public function getSource()
        {
                return 'access';
        }

        /**
         * Independent Column Mapping.
         */
        public function columnMap()
        {
                return array(
                        'id'      => 'id',
                        'exam_id' => 'exam_id',
                        'name'    => 'name',
                        'addr'    => 'addr'
                );
        }

}
