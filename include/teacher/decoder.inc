<?php

// 
// Copyright (C) 2010 Computing Department BMC, 
// Uppsala Biomedical Centre, Uppsala University.
// 
// File:   include/teacher/decoder.inc
// Author: Anders L�vgren
// Date:   2010-05-05
// 
// Support for decoding exams.
// 

class Decoder
{
    private $exam;
    
    public function __construct($exam)
    {
	$this->exam = $exam;
    }
    
    // 
    // Get all exams where this user is a contributor.
    // 
    public static function getExams($user)
    {
	$sql = sprintf("SELECT e.id AS ExamID, e.name AS ExamName, e.descr AS ExamDescription, 
			       e.starttime AS ExamStartTime, e.endtime AS ExamEndTime, e.created AS ExamCreated,
			       e.updated AS ExamUpdated, e.creator AS ExamCreator, e.decoded AS ExamDecoded
			FROM exams e, decoders d
			WHERE e.id = d.exam_id AND d.user = '%s'
			ORDER BY e.starttime", $user);
	$res = Database::getConnection()->query($sql);
	return new ResultSet($res);
    }
    
    // 
    // Flag this exam as decoded.
    // 
    public function setDecoded()
    {
	$sql = sprintf("UPDATE exams SET decoded = 'Y' WHERE id = %d", $this->exam);
        $res = Database::getConnection()->query($sql);
 	if(PEAR::isError($res)) {
	    throw new DatabaseException($res->getMessage());
	}
    }
}

?>