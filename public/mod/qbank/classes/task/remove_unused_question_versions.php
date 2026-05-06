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

namespace mod_qbank\task;

use core\task\scheduled_task;

/**
 * Scheduled task to identify and clean up unused question versions.
 *
 * @package   mod_qbank
 * @author    Simon Thornett <simon.thornett@catalyst-eu.net>
 * @copyright Catalyst IT, 2026
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remove_unused_question_versions extends scheduled_task {
    /**
     * Get a descriptive name for the task (shown to admins).
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('removeunusedquestionversions', 'qbank');
    }

    /**
     * Do the job.
     *
     * @return void
     */
    public function execute(): void {
        global $CFG, $DB;
        require_once($CFG->libdir . '/questionlib.php');

        // Load the config.
        $period = get_config('mod_qbank', 'versioncleanupperiod');
        if (empty($period)) {
            mtrace('No upper period set, not running');
        }
        $periodtocheck = time() - $period;
        $lastcheckedmodified = get_config('mod_qbank', 'lastcheckedmodified');

        // Get question versions created before the defined period excluding the first version.
        $sql = '
            SELECT q.id, qv.version, qv.questionbankentryid, q.timemodified
            FROM {question_versions} qv
            JOIN {question} q
            ON q.id = qv.questionid
            WHERE q.timemodified <= :createdbefore
            AND qv.version != 1
        ';
        $params = ['createdbefore' => $periodtocheck];

        // Further restrict the SQL if previously run.
        if ($lastcheckedmodified) {
            $sql .= ' AND q.timemodified > :lastcheckedmodified';
            $params['lastcheckedmodified'] = $lastcheckedmodified;
        }

        $questions = $DB->get_records_sql($sql, $params);

        if (empty($questions)) {
            mtrace('No questions to check');
            return;
        }

        mtrace('Checking ' . count($questions) . ' question versions');

        // Iterate over and remove if unused and not the latest.
        // Additionally calculate the highest timemodified we checked to prevent needing to check all previous ones
        // again next time.
        $unusedversions = 0;
        $maxtimemodified = 0;
        foreach ($questions as $question) {
            if (!questions_in_use([$question->id]) && !is_latest($question->version, $question->questionbankentryid)) {
                question_delete_question($question->id);
                $unusedversions++;
                $maxtimemodified = max($maxtimemodified, $question->timemodified);
            }
        }
        set_config('lastcheckedmodified', $maxtimemodified, 'mod_qbank');
        mtrace('Removed ' . $unusedversions . ' unused question versions');
    }
}
