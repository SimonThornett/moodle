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
 * Strings for component qbank_history, language 'en'.
 *
 * @package    qbank_history
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['allquestionversionsdeleted'] = 'All versions of this question have been deleted.';
$string['available_history'] = 'This page shows all available versions of this question. Versions older than {$a} will be deleted automatically (excluding the earliest and latest versions).';
$string['close_history'] = 'Close';
$string['history_action'] = 'Available history';
$string['history_header'] = 'Question\'s available history';
$string['pluginname'] = 'Question history';
$string['privacy:metadata'] = 'The Question history question bank plugin does not store any personal data.';
$string['questionversionnumber'] = 'Version';
$string['questionversiondata'] = 'v{$a}';
$string['removeunusedquestionversions'] = 'Remove unused question versions';
$string['versioncleanupperiod'] = 'Creation time cleanup period';
$string['versioncleanupperiod_desc'] = 'Minimum time since the version was created to be removed if unused (excluding version 1 and latest). Setting to 0 disables the task.
<p>When subsequent tasks run it only checks between the last processed versions time created and this setting, for example:</p>
<ul>
    <li>The first run looks for any versions older than a year, finds a version a year and two months old, so deletes it</li>
    <li>The second run look for any versions older than a year, and newer than a year and two months</li>
    <li>This continues until a different version is removed and that last processed versions time created is used going foward</li>
</ul>
<p>This reduces the run time and overhead of the task.</p>';
