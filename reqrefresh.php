<?php
/**
 *   Simple script which outputs 1 if a given QUBA/slot combo is up to date; 0 otherwise.
 */ 

require_once(dirname(__FILE__).'/../../../config.php');

//require_once($CFG->dirroot.'/question/engine/lib.php');
//require_once($CFG->dirroot.'/mod/quiz/accessmanager.php');
//require_once($CFG->dirroot.'/mod/quiz/attemptlib.php');

//get the usage ID which requires a refresh
$quba_id = required_param('quba', PARAM_INT);

//determine if a refresh is required
$refresh_required = $DB->record_exists('question_checkoff_refresh', array('quba' => $quba_id));

if($refresh_required)
{
    //indicate that a refresh is required
    echo '1';

    //then, clear the refresh request
    $DB->delete_records('question_checkoff_refresh', array('quba' => $quba_id));
}

?>
