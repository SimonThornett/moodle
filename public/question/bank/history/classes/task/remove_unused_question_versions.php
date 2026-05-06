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

namespace qbank_history\task;

use core\task\scheduled_task;

/**
 * Scheduled task to identify and clean up unused question versions.
 *
 * @package   qbank_history
 * @author    Simon Thornett <simon.thornett@catalyst-eu.net>
 * @copyright Catalyst IT, 2026
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class remove_unused_question_versions extends scheduled_task {
    /** @var int The number of deletions we'll process per run. */
    const TASK_LIMIT = 5000;

    /**
     * Get a descriptive name for the task (shown to admins).
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('removeunusedquestionversions', 'qbank_history');
    }

    /**
     * Execute the task.
     *
     * We first look for question versions that aren't the latest or earliest and were either:
     * * Last modified before the configs version clean-up period.
     * * Last modified before the configs version clean-up period and after the highest timemodified on the last run.
     * We then check to see if the question version is in use, and if not we remove it.
     */
    public function execute(): void {
        global $CFG, $DB;
        require_once($CFG->libdir . '/questionlib.php');

        // Load the config.
        $period = get_config('qbank_history', 'versioncleanupperiod');
        if (empty($period)) {
            mtrace('No upper period set, not running');
        }
        $periodtocheck = time() - $period;
        $lastcheckedmodified = get_config('qbank_history', 'lastcheckedmodified');

        // Get question versions created before the defined period.
        // The additional joins on question_versions (qv2 & qv3) are to allow us to exclude the earliest
        // and latest versions of a question bank entry.
        $sql = '
            SELECT q.id, qv.version, qv.questionbankentryid, q.timemodified
              FROM {question_versions} qv
              JOIN {question} q
                   ON q.id = qv.questionid
         LEFT JOIN {question_versions} qv2
                   ON qv.questionbankentryid = qv2.questionbankentryid
                   AND qv.version > qv2.version
         LEFT JOIN {question_versions} qv3
                   ON qv.questionbankentryid = qv3.questionbankentryid
                   AND qv.version < qv3.version
             WHERE q.timemodified <= :createdbefore
                   AND qv2.id IS NOT NULL
                   AND qv3.id IS NOT NULL
        ';
        $params = ['createdbefore' => $periodtocheck];

        // Further restrict the SQL if previously run.
        if ($lastcheckedmodified) {
            $sql .= ' AND q.timemodified > :lastcheckedmodified';
            $params['lastcheckedmodified'] = $lastcheckedmodified;
        }

        $questions = $DB->get_records_sql(sql: $sql, params: $params, limitnum: 5000);

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
            if (!questions_in_use([$question->id])) {
                question_delete_question($question->id);
                $unusedversions++;
                $maxtimemodified = max($maxtimemodified, $question->timemodified);
                // Break out after we've processed 5000 items.
                if ($unusedversions >= self::TASK_LIMIT) {
                    break;
                }
            }
        }
        set_config('lastcheckedmodified', $maxtimemodified, 'qbank_history');
        mtrace('Removed ' . $unusedversions . ' unused question versions');
    }
}
