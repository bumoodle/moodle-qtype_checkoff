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
 * True-false question renderer class.
 *
 * @package    qtype
 * @subpackage checkoff
 * @copyright  2009 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Generates the output for Proctor Checkoff questions.
 * 
 * @uses qtype
 * @uses _renderer
 * @package 
 * @version $id$
 * @copyright 2011, 2012 Binghamton University
 * @author Kyle Temkin <ktemkin@binghamton.edu> 
 * @license GNU Public License, {@link http://www.gnu.org/copyleft/gpl.html}
 */
class qtype_checkoff_renderer extends qtype_renderer 
{

    static $refresh_enabled = false;

    const PREFIX = 'https://chart.googleapis.com/chart?chs=270x270&cht=qr&chld=|0&chl=';
    const QR_CHECKOFF_SUFFIX = '/checkoff.php';


    public function formulation_and_controls(question_attempt $qa, question_display_options $options)
    {


        //get the question, as well as the entered answer, if one exists
        $question = $qa->get_question();
        $response = $qa->get_last_qt_var('answer', '');
        $challenge = $qa->get_last_qt_var('_challenge', false);
        $correct_response = $qa->get_last_qt_var('_correct_response', false);

        //
        // Summary, if correct
        //

        //if the correct answer has already been entered, respond only with a "checked off" message
        if($correct_response !== false and $response == $correct_response)
        {
            //include the question text, as well as the "checked off" message
            $output = html_writer::tag('div', $question->format_questiontext($qa), array('class' => 'qtext'));
            $output .= html_writer::tag('div', get_string('checkedoff', 'qtype_checkoff'));

            //pass along the previously entered value 
            $name = $qa->get_qt_field_name('answer');
            $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => $name, 'value' => $response));

            //and return only this summary
            return $output;
        }

        //start a div which is at least the height of the QR code
        $output = html_writer::start_tag('div', array('style' => 'min-height: 270px;'));

        //
        //QR Code
        //

        //if QR code entry is enabled, display the QR code
        if($question->inputmode == qtype_checkoff_input_mode::ANY || $question->inputmode == qtype_checkoff_input_mode::QR_CODE)
        {
            $output .= html_writer::start_tag('div', array('style' => 'float: right; margin-left: 20px;'));
            $output .= html_writer::tag('td', html_writer::empty_tag('img', array('src' => self::get_qr_code_link($qa))));
            $output .= html_writer::end_tag('div');
    
            //attach a piece of javascript which allows us to auto-refresh on QR capture
            self::attach_javacscript($qa->get_usage_id());

        }

        //
        //Question text
        //

        //output the question text
        $output .= html_writer::tag('div', $question->format_questiontext($qa), array('class' => 'qtext'));


        //
        //Code-pair entry
        //

        //if code-pair entry is enabled, display the challenge/reponse form
        if($question->inputmode == qtype_checkoff_input_mode::ANY || $question->inputmode == qtype_checkoff_input_mode::CODE_PAIR)
        {
            
            $output .= html_writer::start_tag('table');

            //Row 1: Proctor Challenge Code
            $output .= html_writer::start_tag('tr');
            $output .= html_writer::tag('td', get_string('proctorcode', 'qtype_checkoff'));
            $output .= html_writer::tag('td', $challenge, array('style' => 'font-family: monospace;'));
            $output .= html_writer::end_tag('tr');

            //Row 1: Proctor Response Input
            $name = $qa->get_qt_field_name('answer');
            $output .= html_writer::start_tag('tr');
            $output .= html_writer::tag('td', get_string('proctorresponse', 'qtype_checkoff'));
            $output .= html_writer::tag('td', html_writer::empty_tag('input', array('type' => 'password', 'name' => $name, 'size' => '6')));
            $output .= html_writer::end_tag('tr');


            $output .= html_writer::end_tag('table');

        }

        $output .= html_writer::end_tag('div');

        if ($qa->get_state() == question_state::$invalid) 
            $output .= html_writer::nonempty_tag('div', $question->get_validation_error($responsearray), array('class' => 'validationerror'));


        return $output;
    }

    static function get_qr_code_link(question_attempt $qa)
    {
        global $CFG;

        //determine the base URI for the backend check-off handler
        $uri =  $CFG->wwwroot .'/'. get_string('pluginname_link', 'qtype_checkoff') . self::QR_CHECKOFF_SUFFIX;

        //create a new link to the plugin URI
        $target = new moodle_url($uri);

        //create a new array of paramters
        $params = array();

        //retrieve the information necessary to identify question, on the back-end
        $params['quba'] = $qa->get_usage_id();
        $params['slot'] = $qa->get_slot();

        //and pass those along to the target
        $target->params($params);

        $target = str_replace('&amp;', '&', (string)$target);

        //return the full URL
        return self::PREFIX . urlencode((string)$target); 
    }

    static function attach_javacscript($quba_id)
    {
        global $PAGE;

        //ensure that the refresh script only runs once (singleton)
        if(self::$refresh_enabled)
        {
            return;
        }
        self::$refresh_enabled = true;

        $PAGE->requires->js('/question/type/checkoff/refresh.js.php?quba='.$quba_id); 
        $PAGE->requires->js('/theme/mymobile/javascript/jquery-1.6.4.min.js');

        $PAGE->requires->js_init_call('M.autorefresh.init'); //, array(), false, $autorefresh_mod);
    }


    public function correct_response(question_attempt $qa)
    {
        return '';
    }
}
