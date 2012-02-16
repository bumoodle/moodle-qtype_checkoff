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



defined('MOODLE_INTERNAL') || die();


require_once($CFG->dirroot.'/question/type/edit_question_form.php');


/**
 * Proctor Check-off Question Type, editing form.
 * 
 * @uses question
 * @uses _edit_form
 * @package 
 * @version $id$
 * @copyright 2011, 2012 Binghamton University
 * @author Kyle Temkin <ktemkin@binghamton.edu> 
 * @license GNU Public License, {@link http://www.gnu.org/copyleft/gpl.html}
 */
class qtype_checkoff_edit_form extends question_edit_form 
{
    /**
     * Quick alias for get_string, which allows easier changing of the plugin name.
     */
    static function _s($s, $a = array())
    {
        return get_string($s, 'qtype_checkoff', $a);
    }

    /**
     * Add question-type specific form fields.
     *
     * @param object $mform the form being built.
     */
    protected function definition_inner($mform) 
    {
        //offer various choices for the code input method
        $choices = array
            (
                qtype_checkoff_input_mode::ANY => self::_s('codeorqr'),
                qtype_checkoff_input_mode::CODE_PAIR => self::_s('codeonly'),
                qtype_checkoff_input_mode::QR_CODE => self::_s('qronly')
            );          
        $mform->addElement('select', 'inputmode', self::_s('inputmode'), $choices);

        //retrieve a set of code pairs, generating them if they don't already exist 
        $codepairs = $this->get_code_pairs();

        //start a new header, then, link to the Proctor Codes
        $mform->addElement('header', 'downloadsheet', self::_s('printablecodes'));
        $mform->addElement('static', 'viewhtml', self::_s('viewcodelist'), html_writer::link($this->get_code_view_url($codepairs, false), self::_s('ashtml')));
        $mform->addElement('static', 'viewpdf', '', html_writer::link($this->get_code_view_url($codepairs, true), self::_s('aspdf')));

        //and insert the code query as a silent argument
        $mform->addElement('hidden', 'codepairs', $codepairs);
   }

    /**
     * Returns the URL at which the proctor codes can be viewed.
     * 
     * @param mixed $pdf 
     * @return void
     */
    protected function get_code_view_url($codes, $pdf = false)
    {
        global $CFG;

        //create the base URL - we're not using moodle_url here for efficiency (we already have a valid query string)
        $url = $CFG->wwwroot .'/'. self::_s('pluginname_link') . self::_s('codeviewsuffix') . '?'  . str_replace('&amp;', '&', $codes);

        //if PDF is set, add the flag to the URI
        if($pdf)
            $url .= '&pdf=1';

        //return the newly created URL
        return $url; 
    }

    /**
     * Returns a query string which represents the code pairs for this question, generating them if they don't already exist.
     */
    protected function get_code_pairs()
    {
        //if we don't yet have code pairs, return an newly generated set of codes
        if(!isset($this->question->options->codepairs))
            return $this->get_initial_code_string();

        //otherwise, return the code pairs we already have
        else
            return $this->question->options->codepairs;
    }

    /**
     * Gets a new set of code pairs, encoded as a query string.
     */
    protected function get_initial_code_string()
    {
        //generate a list of challenge/response pairs
        $codes = qtype_checkoff::generate_pairs();

        //encode the values as a HTTP query
        return http_build_query($codes);
    }
   
    /**
     * Returns the question type's name.
     */
    public function qtype() 
    {
        return 'checkoff';
    }
}
