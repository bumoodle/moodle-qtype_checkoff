<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * True-false question definition class.
 *
 * @package    qtype
 * @subpackage checkoff
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();



/**
 * Class which repesents a Proctor Check-off question.
 * 
 * @uses question
 * @uses _graded_automatically
 * @package 
 * @version $id$
 * @copyright 2011, 2012 Binghamton University
 * @author Kyle Temkin <ktemkin@binghamton.edu> 
 * @license GNU Public License, {@link http://www.gnu.org/copyleft/gpl.html}
 */
class qtype_checkoff_question extends question_graded_automatically 
{
    public $challenge;
    public $correct_response;

    /**
     * Shorthand for get_string which eases changing the question type's name.
     */
    static function _s($s, $a = array())
    {
        return get_string($s, 'qtype_checkoff', $a);
    }

    /**
     * Starts a new question attempt, assigning the user a challenge/response from the pool.
     */
    public function start_attempt(question_attempt_step $step, $variant)
    {
        //get the array of code pairs for the question type, and store them in $pairs
        parse_str(str_replace('&amp;', '&', $this->codepairs), $pairs);

        //pick a random challenge
        $this->challenge = array_rand($pairs);

        //and the corresponding response
        $this->correct_response = $pairs[$this->challenge];
        
        //store both the challenge and the response, for later transactions
        $step->set_qt_var('_challenge', $this->challenge);
        $step->set_qt_var('_correct_response', $this->correct_response);

    }

    /**
     * Restores attempt state from past transactions.
     */
    public function apply_attempt_state(question_attempt_step $step)
    {
        //restore the challenge and correct response
        $this->challenge = $step->get_qt_var('_challenge');
        $this->correct_response = $step->get_qt_var('_correct_response');
    } 

    /**
     * Inidicates the response format expected by the question.
     */
    public function get_expected_data() 
    {
        return array('answer' => PARAM_INTEGER);
    }

    public function get_correct_response() 
    {
        return array('answer' => $this->correct_response);
    }

    
    public function summarise_response(array $response) 
    {
        //if no code has been entered, respond with "no code entered"
        if(!$this->is_complete_response($response))
            return self::_s('nocode');

        //if the correct code has been entered repspond with  "invalid code"
        elseif($this->grade_response($response) === 1)
            return self::_s('validcode');

        //otherwise, the response must be an invalid code
        else
            return self::_s('invalidcode');
    }
    

    public function is_complete_response(array $response) 
    {
        return array_key_exists('answer', $response) and is_numeric($response['answer']);
    }

    public function is_gradable_response(array $response)
    {
        return $this->is_complete_response($response);
    }


    public function get_validation_error(array $response)
    {
        if ($this->is_gradable_response($response)) 
            return '';
        
        return self::_s('pleasentervalidcode');
    }

    public function is_same_response(array $prevresponse, array $newresponse) 
    {
        return question_utils::arrays_same_at_key_missing_is_blank( $prevresponse, $newresponse, 'answer');
    }

    public function grade_response(array $response) 
    {
        //if the entered answer is the correct response, award full marks
        if($response['answer'] == $this->correct_response)
            $fraction = 1;

        //otherwise, award no marks
        else
            $fraction = 0;

        //return the fraction, along with the correct state
        return array($fraction, question_state::graded_state_for_fraction($fraction));
    }

}
