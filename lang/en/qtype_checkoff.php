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
 * Strings for component 'qtype_truefalse', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    qtype
 * @subpackage checkoff
 * @copyright  2012 onwards Binghamton University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//plugin details
$string['pluginname'] = 'Proctor Check-off';
$string['pluginname_help'] = 'In response to a QR code or numeric prompt, the proctor enters a code, or scans a QR image, indicating that the student has completed some offline objective.';
$string['pluginname_link'] = 'question/type/checkoff';
$string['pluginnameadding'] = 'Adding a Check-off point';
$string['pluginnameediting'] = 'Editing a Check-off point';
$string['pluginnamesummary'] = 'A simple prompt which allows for a proctor (such as an instructor or TA) to verify offline student work, like a lab activity.';

//suffix for the code view
$string['codeviewsuffix'] = '/codes.php';

//input mode details
$string['inputmode'] = 'Entry Mode';
$string['codeonly'] = 'Allow the proctor to respond with a code from the list below.';
$string['qronly'] = 'Allow the proctor to respond by scanning a QR code while logged in on a smartphone.';
$string['codeorqr'] = 'Allow the proctor to repond by either scanning a QR or responding with a code.';

//and printable strings
$string['printablecodes'] = 'Printable List of Proctor Codes';
$string['viewcodelist'] = 'View code list';
$string['ashtml'] = 'as Basic HTML';
$string['aspdf'] = 'as PDF';

//state messages
$string['nocode'] = 'No code entered.';
$string['invalidcode'] = 'Incorrect code entered.';
$string['validcode'] = 'Valid proctor code entered.';
$string['pleaseentervalidcode'] = 'Please enter a valid code.';
$string['checkedoff'] = '<b><font color="#287A07">This item or assignment was checked off!</font></b>';

$string['proctorcode'] = 'Proctor Code:';
$string['proctorresponse'] = 'Proctor Response:';


//QR checkoff
$string['qrsuccess'] = '{$a->firstname} {$a->lastname} checked off successfully!';

$string['qrpleasewait'] = 'Just a moment while we check you off...';
