<?php

/**
 * Handles QR-code checkoffs
 */

require_once(dirname(__FILE__).'/../../../config.php');

require_once($CFG->dirroot.'/question/engine/lib.php');
require_once($CFG->dirroot.'/mod/quiz/accessmanager.php');
require_once($CFG->dirroot.'/mod/quiz/attemptlib.php');

//get the usage ID and the slot which indicates which question is being checked off
$quba_id = required_param('quba', PARAM_INT);
$slot = required_param('slot', PARAM_INT);

//
// Step 1: Check to ensure the user has permission to grade quizzes
//

//Use a SQL join to determine the course ID from the QUBA. The QUBA id is equal to the quiz attempt's uniqueid; 
//the quiz attempt knows which quiz it belongs to; and the quiz knows what course it belongs to.
$sql = 'SELECT course,userid FROM {quiz_attempts} qa JOIN {quiz} q ON qa.quiz = q.id WHERE qa.uniqueid = ?';
$attempt = $DB->get_record_sql($sql, array($quba_id));

//if we couldn't load the attempt, throw an error
if($attempt===false)
    print_error('invalidactivityid', 'error', '', '');

//use the course ID to get a new course context
$context = context_course::instance($attempt->course);

//First, ensure the user is logged in. 
//Without this call, require_capability will sometimes fail, even if the user is logged in.
require_login($attempt->course);

//require the user to be able to grade quizzes in order to check off a piece of student work
require_capability('mod/quiz:grade', $context);

//
// Step 2: Check off the quesiton manually
//

//get the QUBA object which contains the checkoff question
$quba = question_engine::load_questions_usage_by_activity($quba_id);

//$qa = $quba->get_question_attempt($slot);
$question = $quba->get_question($slot);
$qa = $quba->get_question_attempt($slot);

//act as though the user had just submitted the correct response
$quba->process_action($slot, array('answer' => $question->correct_response), time());
$quba->finish_question($slot, time());

//and save the result
question_engine::save_questions_usage_by_activity($quba);

//
// Step 3: Queue a refresh, get the user information, and inform the grader of success
//

//insert a refresh request for the given QUBA
$DB->insert_record('question_checkoff_refresh', (object)array('quba' => $quba_id), false);

//get information on the current user
$user = $DB->get_record('user', array('id' => $attempt->userid), 'firstname,lastname');

//inform the user of the success
echo get_string('qrsuccess', 'qtype_checkoff', $user);

?>
