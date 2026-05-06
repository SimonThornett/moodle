<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     mod_qbank
 * @copyright   2024 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author      Simon Adams <simon.adams@catalyst-eu.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addanotherbank'] = 'Add question bank';
$string['coursecategory'] = 'Shared teaching resources for category: {$a}';
$string['courserestore'] = 'Course restore';
$string['createqbank'] = 'Create question bank {$a}';
$string['modulename'] = 'Question bank';
$string['modulename_help'] = 'This activity allows a teacher to create, preview, and edit questions in a database of question categories.

Questions are then used in a quiz or other activity.

After a question is used, version control and statistics are available.';
$string['modulenameplural'] = 'Question banks';
$string['noqbankinstances'] = 'There are no question banks in this course.';
$string['pluginadministration'] = 'Question bank administration';
$string['pluginname'] = 'Question bank';
$string['privacy:metadata'] = 'The Question bank plugin does not store any personal data, core_question automatically tracks all sorts of data for questions.';
$string['progress_createqbank'] = 'Creating question banks';
$string['qbank:addinstance'] = 'Add a new question bank';
$string['qbank:view'] = 'View question bank';
$string['qbankname'] = 'Question bank name';
$string['qbankname_help'] = 'Enter the question bank name.';
$string['removeunusedquestionversions'] = 'Remove unused question versions';
$string['saveanddisplay'] = 'Save and display';
$string['saveandreturn'] = 'Save and return to question bank list';
$string['sharedbank'] = '{$a} shared question bank';
$string['showdescription'] = 'Display description on manage question banks page';
$string['showdescription_help'] = 'If enabled, the description above will be displayed on the manage question bank page just below the link to the bank.';
$string['transfernotfinished'] = 'The adhoc tasks \\mod_qbank\\task\\transfer_question_categories and \\mod_qbank\\task\\transfer_questions are not yet complete or have failed. Questions previously created in different contexts may not be transferred to course shared question banks yet. Questions can\'t be managed or shared until these tasks are complete.';
$string['unknownbanktype'] = 'Unknown question bank type {$a}';
$string['versioncleanupperiod'] = 'Creation time cleanup period';
$string['versioncleanupperiod_desc'] = 'Minimum time since the version was created to be removed if unused (excluding version 1 and latest). Setting to 0 disables the task.
<p>When subsequent tasks run it only checks between the last processed versions time created and this setting, for example:</p>
<ul>
    <li>The first run looks for any versions older than a year, finds a version a year and two months old, so deletes it</li>
    <li>The second run look for any versions older than a year, and newer than a year and two months</li>
    <li>This continues until a different version is removed and that last processed versions time created is used going foward</li>
</ul>
<p>This reduces the run time and overhead of the task.</p>';
