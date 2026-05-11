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
$string['available_history'] = '<p>This page shows all available versions of this question. It may not show all versions due to one of the following:</p>
<ul>
    <li>Versions older than {$a} that are unused are deleted automatically (excluding the earliest and latest versions).</li>
    <li>Versions can be manually deleted by users</li>
<ul>
';
$string['close_history'] = 'Close';
$string['history_action'] = 'Available history';
$string['history_header'] = 'Question\'s available history';
$string['pluginname'] = 'Question history';
$string['privacy:metadata'] = 'The Question history question bank plugin does not store any personal data.';
$string['questionversionnumber'] = 'Version';
$string['questionversiondata'] = 'v{$a}';
$string['removeunusedquestionversions'] = 'Remove unused question versions';
$string['versioncleanupperiod'] = 'Delete unused question versions older than';
$string['versioncleanupperiod_desc'] = 'Unused versions created before this time will be deleted. The oldest and latest versions will always be kept. Setting to 0 disables the task.
<p>With each run of the task, it only checks questions newer than those which have previously been deleted and this setting, for example:</p>
<ul>
    <li>The first run looks for any versions older than a year, finds a version a year and two months old, so deletes it</li>
    <li>The second run look for any versions older than a year, and newer than a year and two months</li>
    <li>Each run will record the last version it deleted and use this as the lower threshold for the next run</li>
    <li>Once the task has processed 5000 items it stops running to prevent blocking other tasks.</li>
</ul>
<p>This reduces the run time and overhead of the task.</p>';
