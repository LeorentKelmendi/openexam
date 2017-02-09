<?php

// 
// The source code is copyrighted, with equal shared rights, between the
// authors (see the file AUTHORS) and the OpenExam project, Uppsala University 
// unless otherwise explicit stated elsewhere.
// 
// File:    Serializable.php
// Created: 2017-02-09 14:50:13
// 
// Author:  Anders Lövgren (QNET/BMC CompDept)
// 

/**
 * Notice: 
 * -----------
 * This is derived work based on Phalcon\Adapter\Result\Serializable
 * from phalcon/incubator version 2.0.13.
 */

namespace OpenExam\Library\Database\Cache\Result;

use PDOStatement;
use Phalcon\Db\Result\Pdo as PdoResultSet;

/**
 * Serializable result set.
 * 
 * Fetches all the data in a result providing a serializable resultset. This
 * class implements the ResultInterface interface, except for the constructor
 * and setFetchMode(). 
 * 
 * Some methods might show odd behavior compared to the real result set due
 * to absent of real PDO statement.
 * 
 * @author Anders Lövgren (QNET/BMC CompDept)
 */
class Serializable
{

        /**
         * The result set data.
         * @var array 
         */
        protected $_data = array();
        /**
         * The fetch position.
         * @var int 
         */
        protected $_position = 0;
        /**
         * The PDO internal statement.
         * @var PDOStatement 
         */
        private $_statement;

        /**
         * Constructor.
         * @param PdoResultSet $result The result set.
         */
        public function __construct($result)
        {
                $this->_statement = $result->getInternalResult();
                $this->_data = $result->fetchAll();
        }

        /**
         * Resets the internal pointer.
         */
        public function __wakeup()
        {
                $this->_position = 0;
        }

        /**
         * Return serializable properties.
         * @return array
         */
        public function __sleep()
        {
                return array('_data', '_position');
        }

        /**
         * Returns the number of rows in the internal array.
         *
         * @return integer
         */
        public function numRows()
        {
                return count($this->_data);
        }

        /**
         * Fetches a row in the resultset.
         *
         * @return array|boolean
         */
        public function fetch()
        {
                if (!isset($this->_data[$this->_position])) {
                        return false;
                }

                return $this->_data[$this->_position++];
        }

        /**
         * Changes the fetch mode.
         *
         * @param integer $fetchMode The PDO fetch mode.
         * @todo Not yet implemented
         */
        public function setFetchMode($fetchMode)
        {
                // ignore
        }

        /**
         * Returns the full data in the resultset.
         *
         * @return array
         */
        public function fetchAll()
        {
                return $this->_data;
        }

        /**
         * Moves internal resultset cursor.
         * @param int $number The new position.
         */
        public function dataSeek($number)
        {
                if ($number >= 0 && $number < count($this->_data)) {
                        $this->_position = $number;
                }
        }

        /**
         * execute the statement again.
         * @return boolean
         */
        public function execute()
        {
                if (isset($this->_statement)) {
                        return $this->_statement->execute();
                }
        }

        /**
         * Return current row as array.
         * 
         * This method returns an array of strings that corresponds to the 
         * fetched row, or false if there are no more rows.
         * 
         * @return boolean|array
         */
        public function fetchArray()
        {
                if ($this->_position == count($this->_data)) {
                        return false;
                } else {
                        return $this->_data[$this->_position++];
                }
        }

        /**
         * Gets the internal PDO result object.
         * @return PDOStatement
         */
        public function getInternalResult()
        {
                return $this->_statement;
        }

}
