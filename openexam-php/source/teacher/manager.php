<?php

// 
// Copyright (C) 2010 Computing Department BMC, 
// Uppsala Biomedical Centre, Uppsala University.
// 
// File:   source/teacher/manager.php
// Author: Anders L�vgren
// Date:   2010-04-26
// 
// This script is for managing exams (the actual test). It let people assigned the
// teacher role to define new examinations and edit old ones. An examination is
// defined in the exam table.
// 
// The teacher is the owner of the exam, and can delegate (grant) the contributor, 
// examinator and decoder role to other users. By default, the teacher role is
// assigned all other roles.
// 

// 
// Force logon for unauthenticated users:
// 
$GLOBALS['logon'] = true;

// 
// System check:
// 
if(!file_exists("../../conf/database.conf")) {
    header("location: setup.php?reason=database");
}
if(!file_exists("../../conf/config.inc")) {
    header("location: setup.php?reason=config");
}

// 
// Include external libraries:
// 
include "MDB2.php";
include "CAS.php";

// 
// Locale and internationalization support:
// 
include "include/locale.inc";

// 
// Include configuration:
// 
include "conf/config.inc";
include "conf/database.conf";

// 
// Include logon and user interface support:
// 
include "include/cas.inc";
include "include/ui.inc";
include "include/error.inc";

// 
// Include database support:
// 
include "include/database.inc";

// 
// Business logic:
// 
include "include/teacher.inc";
include "include/teacher/manager.inc";

// 
// The index page:
// 
class ManagerPage extends TeacherPage
{
    private $params = array( "exam"   => "/^\d+$/",
			     "action" => "/^(add|edit|show|copy|delete)$/",
			     "role"   => "/^(contributor|examinator|decoder)$/",
			     "user"   => "/^\d+$/" );
    
    public function __construct()
    {
	parent::__construct(_("Examination Management"), $this->params);
    }

    // 
    // The main entry point. This is where all processing begins.
    // 
    public function printBody()
    {
	echo "<h3>" . _("Examination Management") . "</h3>\n";
	
        //
	// Authorization first:
	//
	if(isset($_REQUEST['exam'])) {
	    self::checkAccess($_REQUEST['exam']);
	}
	
	//
	// Bussiness logic:
	//
	if(!isset($_REQUEST['exam'])) {
	    if(!isset($_REQUEST['action'])) {
		self::showAvailableExams();
	    } elseif($_REQUEST['action'] == "add") {
		self::addExam(isset($_REQUEST['name']));
	    }
	} else {
	    if(isset($_REQUEST['action'])) {
		if(isset($_REQUEST['role'])) {
		    if($_REQUEST['action'] == "delete" && !isset($_REQUEST['user'])) {
			die(sprintf(_("Missing parameter '%s'"), "user"));
		    }
		    if($_REQUEST['role'] == "contributor") {
			if($_REQUEST['action'] == "add") {
			    self::addContributor($_REQUEST['exam'], isset($_REQUEST['uuid']));
			} elseif($_REQUEST['action'] == "delete") {
			    self::deleteContributor($_REQUEST['exam'], $_REQUEST['user']);
			}
		    } elseif($_REQUEST['role'] == "examinator") {
			if($_REQUEST['action'] == "add") {
			    self::addExaminator($_REQUEST['exam'], isset($_REQUEST['uuid']));
			} elseif($_REQUEST['action'] == "delete") {
			    self::deleteExaminator($_REQUEST['exam'], $_REQUEST['user']);
			}
		    } elseif($_REQUEST['role'] == "decoder") {
			if($_REQUEST['action'] == "add") {
			    self::addDecoder($_REQUEST['exam'], isset($_REQUEST['uuid']));
			} elseif($_REQUEST['action'] == "delete") {
			    self::deleteDecoder($_REQUEST['exam'], $_REQUEST['user']);
			}
		    }
		} else {
		    if($_REQUEST['action'] == "show") {
			self::showExam($_REQUEST['exam']);
		    } elseif($_REQUEST['action'] == "edit") {
			self::editExam($_REQUEST['exam'], isset($_REQUEST['name']));
		    } elseif($_REQUEST['action'] == "copy") {
			self::copyExam($_REQUEST['exam']);
		    } elseif($_REQUEST['action'] == "delete") {
			self::deleteExam($_REQUEST['exam']);
		    } 
		}
	    } else {
		self::showExam($_REQUEST['exam']);
	    }
	} 
    }

    // 
    // Show all exams the current user is the owner of.
    // 
    private function showAvailableExams() 
    {
	printf("<p>"  . 
	       _("This page let you create new exams or manage your old ones. ") . 
	       _("These are the exams you are the manager of: ") .
	       "</p>\n");
	
	printf("<ul>\n");
	printf("<li>%s: <span class=\"links\"><a href=\"?action=add\">%s</a></span></li>\n",
	       _("Examinations"), _("Add"));
	$exams = Manager::getExams(phpCAS::getUser());
	foreach($exams as $exam) {
	    printf("<ul>\n");
	    printf("<li><a href=\"?exam=%d&amp;action=show\" title=\"%s\">%s</a> (%s - %s) <span class=\"links\"><a href=\"?exam=%d&amp;action=edit\">%s</a>, <a href=\"?exam=%d&amp;action=copy\">%s</a>, <a href=\"?exam=%d&amp;action=delete\">%s</a></span></li>\n",
		   $exam->getExamID(), 
		   sprintf("%s\n\n%s", utf8_decode($exam->getExamDescription()), _("Click to add/delete contributors, decoders, examinators and questions")),
		   utf8_decode($exam->getExamName()), 
		   strftime(DATETIME_FORMAT, strtotime($exam->getExamStartTime())), 
		   strftime(DATETIME_FORMAT, strtotime($exam->getExamEndTime())),
		   $exam->getExamID(), 
		   _("Edit"),
		   $exam->getExamID(),
		   _("Copy"),
		   $exam->getExamID(),
		   _("Delete"));
	    printf("</ul>\n");
	}
	printf("</ul>\n");
    }

    // 
    // Common form for adding and editing exam properties.
    // 
    private function showExamForm($exam, $data, $action)
    {
	printf("<form action=\"manager.php\" method=\"GET\">\n");
	printf("<input type=\"hidden\" name=\"action\" value=\"%s\" />\n", $action);
	if($exam != 0) {
	    printf("<input type=\"hidden\" name=\"exam\" value=\"%d\" />\n", $exam);
	}
	printf("<label for=\"name\">%s</label>\n", _("Name:"));
	printf("<input type=\"text\" name=\"name\" value=\"%s\" size=\"50\" />\n", utf8_decode($data->getExamName()));
	printf("<br />\n");
	printf("<label for=\"\">%s</label>\n", _("Description:"));
	printf("<textarea name=\"desc\" cols=\"50\" rows=\"10\">%s</textarea>\n", utf8_decode($data->getExamDescription()));
	printf("<br />\n");
	printf("<label for=\"\">%s</label>\n", _("Start time:"));
	printf("<input type=\"text\" name=\"start\" value=\"%s\" size=\"30\" />\n", strftime(DATETIME_FORMAT, strtotime($data->getExamStartTime())));
	printf("<br />\n");
	printf("<label for=\"\">%s</label>\n", _("End time:"));
	printf("<input type=\"text\" name=\"end\" value=\"%s\" size=\"30\" />\n", strftime(DATETIME_FORMAT, strtotime($data->getExamEndTime())));
	printf("<br />\n");
	printf("<input type=\"submit\" value=\"%s\" />\n", _("Submit"));
	printf("</form>\n");
    }
    
    // 
    // Add an new exam.
    // 
    private function addExam($store)
    {
	if(!$store) {
	    printf("<p>" . _("Define the common properties of the exam. Click on the 'Submit' button to create this exam.") . "</p>\n");
	    $data = new DataRecord( array( "examname" => "Name", 
					   "examdescription" => "Description", 
					   "examstarttime" => strftime(DATETIME_FORMAT, time()),
					   "examendtime"   => strftime(DATETIME_FORMAT, time() + 3600 * 24) ));
	    printf("<p>" . _("This page let you edit common properties of the exam.") . "</p>\n");
	    self::showExamForm(0, $data, "add");
	} else {
	    $manager = new Manager(0);
	    $manager->setData(utf8_encode($_REQUEST['name']),
			      utf8_encode($_REQUEST['desc']),
			      strtotime($_REQUEST['start']),
			      strtotime($_REQUEST['end']));
	    // 
	    // By default, add creator of the exam as contributor, examinator and decoder.
	    // 
	    $manager->addContributor(phpCAS::getUser());
	    $manager->addExaminator(phpCAS::getUser());
	    $manager->addDecoder(phpCAS::getUser());
	    header(sprintf("location: manager.php?exam=%d", $manager->getExamID()));
	}
    }
    
    // 
    // Edit an existing exam.
    // 
    private function editExam($exam, $store)
    {
	$manager = new Manager($exam);
	
	if(!$store) {
	    $data = $manager->getData();
	    printf("<p>" . _("This page let you edit common properties of the exam. Click on the 'Submit' button to save changes.") . "</p>\n");
	    self::showExamForm($exam, $data, "edit");
	} else {
	    $manager->setData(utf8_encode($_REQUEST['name']), 
			      utf8_encode($_REQUEST['desc']), 
			      strtotime($_REQUEST['start']), 
			      strtotime($_REQUEST['end']));
	    header(sprintf("location: manager.php?exam=%d", $exam));
	}
    }
    
    // 
    // Creates a copy of an existing exam. The contributor, examinator and decoder is 
    // roles are preserved (but re-associated with the copy). The list of questions 
    // is also preserved, but without any associated answers.
    // 
    private function copyExam($exam)
    {
	$orig = new Manager($exam);
	$copy = $orig->copy();
	header(sprintf("location: manager.php?exam=%d&action=edit", $copy->getExamID()));
    }
    
    private function deleteExam($exam)
    {
	$manager = new Manager($exam);
	$manager->delete();
	header("location: manager.php");
    }
    
    // 
    // Show properties for this exam.
    // 
    private function showExam($exam) 
    {
	$manager = new Manager($exam);
	$data = $manager->getData();
	
	printf("<p>" . _("This page let you add/delete contributors, examinators, decoders and questions from this exam. ") . "</p>");
	printf("<ul>\n");
	printf("<li>%s <span class=\"links\"><a href=\"?exam=%d&amp;action=edit\">%s</a>, <a href=\"?exam=%d&amp;action=copy\">%s</a></span></li>\n",
	       utf8_decode($data->getExamName()),
	       $data->getExamID(),
	       _("Edit"),
	       $data->getExamID(),
	       _("Copy"));	
	printf("<ul>\n");
	
	printf("<li>%s <span class=\"links\"><a href=\"?exam=%d&amp;action=add&amp;role=contributor\">%s</a></span></li>\n",
	       _("Contributors"),
	       $data->getExamID(),
	       _("Add"));
	$contributors = $manager->getContributors();
	if($contributors->count() > 0) {
	    printf("<ul>\n");
	    foreach($contributors as $contributor) {
		printf("<li>%s <span class=\"links\"><a href=\"?exam=%d&amp;action=delete&amp;role=contributor&amp;user=%d\">%s</a></span></li>\n",
		       $contributor->getContributorUser(),
		       $contributor->getExamID(),
		       $contributor->getContributorID(),
		       _("Remove"));
	    }
	    printf("</ul>\n");
	}

	printf("<li>%s <span class=\"links\"><a href=\"?exam=%d&amp;action=add&amp;role=examinator\">%s</a></span></li>\n",
	       _("Examinators"),
	       $data->getExamID(),
	       _("Add"));
	$examinators = $manager->getExaminators();
	if($examinators->count() > 0) {
	    printf("<ul>\n");
	    foreach($examinators as $examinator) {
		printf("<li>%s <span class=\"links\"><a href=\"?exam=%d&amp;action=delete&amp;role=examinator&amp;user=%d\">%s</a></span></li>\n",
		       $examinator->getExaminatorUser(),
		       $examinator->getExamID(),
		       $examinator->getExaminatorID(),
		       _("Remove"));
	    }
	    printf("</ul>\n");
	}
	
	printf("<li>%s <span class=\"links\"><a href=\"?exam=%d&amp;action=add&amp;role=decoder\">%s</a></span></li>\n",
	       _("Decoders"),
	       $data->getExamID(),
	       _("Add"));
	$decoders = $manager->getDecoders();
	if($decoders->count() > 0) {
	    printf("<ul>\n");
	    foreach($decoders as $decoder) {
		printf("<li>%s <span class=\"links\"><a href=\"?exam=%d&amp;action=delete&amp;role=decoder&amp;user=%d\">%s</a></span></li>\n",
		       $decoder->getDecoderUser(),
		       $decoder->getExamID(),
		       $decoder->getDecoderID(),
		       _("Remove"));
	    }
	    printf("</ul>\n");
	}
	
	if($manager->isContributor(phpCAS::getUser())) {
	    printf("<li>%s <span class=\"links\"><a href=\"contribute.php?exam=%d&amp;action=add\">%s</a></span></li>\n",
		   _("Questions"),
		   $data->getExamID(),
		   _("Add"));
	    $questions = $manager->getQuestions();
	    if($questions->count() > 0) {
		printf("<ul>\n");
		foreach($questions as $question) {
		    printf("<li>%s <span class=\"links\"><a href=\"contribute.php?exam=%d&amp;action=edit&amp;question=%d\">%s</a>, <a href=\"contribute.php?exam=%d&amp;action=delete&amp;question=%d\">%s</a></span></li>\n",
			   utf8_decode($question->getQuestionName()),
			   $question->getExamID(),
			   $question->getQuestionID(),
			   _("Edit"),
			   $question->getExamID(),
			   $question->getQuestionID(),
			   _("Delete"));
		}
		printf("</ul>\n");
	    }
	}
	
	printf("</ul>\n");
	printf("</ul>\n");
    }

    // 
    // Helper function for assigning roles to users for this exam. The text
    // parameter is the description text to show.
    // 
    private function addExamRole($exam, $role, $text)
    {
	printf("<p>%s</p>\n", $text);
	printf("<form action=\"manager.php\" method=\"GET\">\n");
	printf("<input type=\"hidden\" name=\"exam\" value=\"%d\" />\n", $exam);
	printf("<input type=\"hidden\" name=\"action\" value=\"add\" />\n");
	printf("<input type=\"hidden\" name=\"role\" value=\"%s\" />\n", $role);
	printf("<label for=\"uuid\">%s</label>\n",  _("UU-ID:"));
	printf("<input type=\"text\" name=\"uuid\" />\n");
	printf("<input type=\"submit\" value=\"%s\" />\n", _("Add"));
	printf("</form>\n");
    }

    private function addContributor($exam, $store)
    {
 	$manager = new Manager($exam);
	
	if(!$store) {
	    $data = $manager->getData();
	    $text = sprintf(_("Allow this user to contribute questions for the examination '%s' by granting he/she the 'contribute' role."),
			    utf8_decode($data->getExamName()), $role);
	    return self::addExamRole($exam, "contributor", $text);
	}
	
	$manager->addContributor($_REQUEST['uuid']);
	header(sprintf("location: manager.php?exam=%d&action=show", $exam));
    }

    private function deleteContributor($exam, $user)
    {
 	$manager = new Manager($exam);
	$manager->deleteContributor($user);
	header(sprintf("location: manager.php?exam=%d", $exam));
    }

    private function addExaminator($exam, $store)
    {
 	$manager = new Manager($exam);
	
	if(!$store) {
	    $data = $manager->getData();
	    $text = sprintf(_("Allow this user to add students for the examination '%s' by granting he/she the 'examinator' role."),
			    utf8_decode($data->getExamName()), $role);
	    return self::addExamRole($exam, "examinator", $text);
	}
	
	$manager->addExaminator($_REQUEST['uuid']);
	header(sprintf("location: manager.php?exam=%d&action=show", $exam));
    }

    private function deleteExaminator($exam, $user)
    {
 	$manager = new Manager($exam);
	$manager->deleteExaminator($user);
	header(sprintf("location: manager.php?exam=%d", $exam));
    }

    private function addDecoder($exam, $store)
    {
 	$manager = new Manager($exam);
	
	if(!$store) {
	    $data = $manager->getData();
	    $text = sprintf(_("Allow this user to decode the real identity behind the students assigned for the examination '%s' by granting he/she the 'decoder' role."),
			    utf8_decode($data->getExamName()), $role);
	    return self::addExamRole($exam, "decoder", $text);
	}
	
	$manager->addDecoder($_REQUEST['uuid']);
	header(sprintf("location: manager.php?exam=%d&action=show", $exam));
    }

    private function deleteDecoder($exam, $user)
    {
 	$manager = new Manager($exam);
	$manager->deleteDecoder($user);
	header(sprintf("location: manager.php?exam=%d", $exam));
    }
    
    // 
    // Verify that the caller has been granted the required role on this exam.
    // 
    private function checkAccess($exam)
    {
	$role = "teacher";
	
	if(!Teacher::userHasRole($exam, $role, phpCAS::getUser())) {
	    ErrorPage::show(_("Access denied!"),
			    sprintf(_("Only users granted the %s role on this exam can access this page. The script processing has halted."), $role));
	    exit(1);
	}
    }
}

$page = new ManagerPage();
$page->render();

?>
