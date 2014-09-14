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
 * Question type class for the true-false question type.
 *
 * @package    qtype
 * @subpackage checkoff
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Enumeration which defines the various methods of checking a user off.
 */
abstract class qtype_checkoff_input_mode
{
    const ANY = 0;
    const CODE_PAIR = 1;
    const QR_CODE = 2;
}


/**
 * The true-false question type class.
 *
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_checkoff extends question_type 
{
 

    /**
     a Specifies the extra question fields, which are used by the default load/save routines to store quesiton data
     * in the question_checkoff database.
     */
    public function extra_question_fields()
    {
        return array('question_checkoff', 'inputmode', 'codepairs');
    }

    /**
     * Specifies the column in question_partner which represents the question id.
     */
    public function questionid_column_name()
    {
        return 'question';
    }

    /**
     * Indicate a random guess score. 
     * 
     * @param mixed $questiondata 
     * @return void
     */
    public function get_random_guess_score($questiondata) 
    {
        //random guesses are one in a million- essentially zero
        return 0;
    }


    public function save_question_options($question) {
        parent::save_question_options($question);
        $this->save_hints($question);
    }

    //start ~ktemkin
    /**
     * Genereate a set of challenge/response pairs, which will be made known to a proctor,
     * but not a student. Theses numbers are completely random, and can be used to verify 
     * student work.
     *
     * The proctor is presented with the "challenge", and must supply the "response".
     * 
     * @param int $amount 
     * @return void
     */
    public static function generate_pairs($amount = 35)
    {
        
        $pairs = array();
        
        //generate a set of challenge/response pairs
        //(this will typically be equal to $amount, but might be less in the event of a collision)
        for($i = 0; $i < $amount; ++$i)
            $pairs[self::generate_challenge()]  = self::generate_response();
            
        //if we somehow didn't generate any pairs, try again
        //(this should be astronomically unlikely)
        if(!count($pairs))
            return self::generate_pairs();
            
        //return the generated pairs
        ksort($pairs);
        return $pairs;
    }
    
    static function generate_challenge()
    {
        //keys should follow the same pattern as responses
        return self::generate_response();
    }
    
    static function generate_response()
    {
        //return a zero-padded six digit number
        return str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Returns true iff the given user can check off the given question attempt.
     * TODO: Migrate me, as appropriate.
     */
    public static function can_perform_checkoff($qa, $user = null) {
        return has_capability('mod/quiz:grade', self::get_context_from_qa($qa), $user);
    }

    /**
     * Returns the context that owns the given question attempt.
     */
    private static function get_context_from_qa($qa) {
        //Get the owning context from the owning Question Usage By Activity.
        $quba = question_engine::load_questions_usage_by_activity($qa->get_usage_id());
        return $quba->get_owning_context();
    }

}
