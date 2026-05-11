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

use advanced_testcase;
use core_question_generator;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * Remove unused question version scheduled task test.
 *
 * @package   qbank_history
 * @author    Simon Thornett <simon.thornett@catalyst-eu.net>
 * @copyright Catalyst IT, 2026
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(remove_unused_question_versions::class)]
final class remove_unused_question_versions_test extends advanced_testcase {
    /**
     * @var array|string[] Array of question types to create.
     */
    private array $questiontypes = ['essay', 'numerical', 'truefalse', 'shortanswer'];

    /**
     * @var int Number of question categories to create.
     */
    private int $categorycount = 4;

    /**
     * @var int Number of questions to create in each category.
     */
    private int $questioncount = 2;

    /**
     * @var int Number of versions of each question to create.
     */
    private int $additionalversioncount = 4;

    /**
     * Test the task with config disabled.
     */
    public function test_task_disabled(): void {
        $this->resetAfterTest();

        // Set config to 0 which disables the task from running.
        set_config('versioncleanupperiod', 0, 'qbank_history');

        // Run the task.
        $task = new remove_unused_question_versions();
        $task->execute();

        // Regex check for mtrace.
        $this->expectOutputRegex("/^No upper period set, not running/");
    }

    /**
     * Test the task with no data.
     */
    public function test_task_no_data(): void {
        $this->resetAfterTest();

        // Set the config to 1 which identifies all versions as past the cleanup period.
        set_config('versioncleanupperiod', 1, 'qbank_history');

        // Run the task without creating any versions.
        $task = new remove_unused_question_versions();
        $task->execute();

        // Regex check for mtrace.
        $this->expectOutputRegex("/^No questions to check/");
    }

    /**
     * Test the task with a set of data, all of which are past the version clean-up period.
     */
    public function test_task_all(): void {
        $this->resetAfterTest();

        // Set the config to 1 which tells the task to run.
        set_config('versioncleanupperiod', 1, 'qbank_history');

        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $total = 0;
        $timemodified = time() - 2;

        for ($i = 0; $i < $this->categorycount; $i++) {
            for ($j = 0; $j < $this->questioncount; $j++) {
                $questioncategory = $questiongenerator->create_question_category();
                $question = $questiongenerator->create_question(
                    qtype: $this->questiontypes[rand(0, count($this->questiontypes) - 1)],
                    overrides: ['category' => $questioncategory->id, 'timemodified' => $timemodified],
                );

                // Update the question to create a new version.
                for ($k = 0; $k < $this->additionalversioncount; $k++) {
                    $questiongenerator->update_question(
                        question: $question,
                        overrides: ['timemodified' => $timemodified],
                    );
                }
                // Add all but 1 (the latest) to the total.
                $total += $this->additionalversioncount - 1;
            }
        }

        // Run the task.
        $task = new remove_unused_question_versions();
        $task->execute();

        // Regex check for mtrace.
        $this->expectOutputRegex("/^Checking $total question versions/");
        $this->expectOutputRegex("/Removed $total unused question versions/");
    }

    /**
     * Test the task with a set of data, half of which are past the version clean-up period.
     */
    public function test_task_half(): void {
        global $DB;

        $this->resetAfterTest();

        // Set the config to 86400 which tells the task to process versions over a day old.
        set_config('versioncleanupperiod', DAYSECS, 'qbank_history');

        /** @var core_question_generator $questiongenerator */
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $total = 0;
        $removedversions = [];
        $retainedversions = [];
        for ($i = 0; $i < $this->categorycount; $i++) {
            for ($j = 0; $j < $this->questioncount; $j++) {
                $questioncategory = $questiongenerator->create_question_category();

                // Default timemodified.
                $timemodified = time();

                // Set every other question to 2 days ago.
                if ($j % 2 == 0) {
                    $timemodified -= 2 * DAYSECS;
                    // Add all but 1 (the latest) to the total.
                    $total += $this->additionalversioncount - 1;
                }
                $question = $questiongenerator->create_question(
                    qtype: $this->questiontypes[rand(0, count($this->questiontypes) - 1)],
                    overrides: ['category' => $questioncategory->id, 'timemodified' => $timemodified],
                );
                $retainedversions[] = $question->versionid;

                // Update the question to create a new version.
                for ($k = 0; $k < $this->additionalversioncount; $k++) {
                    $updatedquestion = $questiongenerator->update_question(
                        question: $question,
                        overrides: ['timemodified' => $timemodified],
                    );
                    if ($j % 2 == 0 && $k < ($this->additionalversioncount - 1)) {
                        $removedversions[] = $updatedquestion->versionid;
                    } else {
                        $retainedversions[] = $updatedquestion->versionid;
                    }
                }
            }
        }

        // Run the task.
        $task = new remove_unused_question_versions();
        $task->execute();

        // Regex check for mtrace.
        $this->expectOutputRegex("/^Checking $total question versions/");
        $this->expectOutputRegex("/Removed $total unused question versions/");

        // Get the remaining versions and confirm that the remaining version count matches.
        $remainingversions = array_keys($DB->get_records('question_versions'));
        $this->assertCount(count($retainedversions), $remainingversions);

        // Check that the versions we were expecting to remove were removed.
        foreach ($removedversions as $removedversion) {
            $this->assertNotContains($removedversion, $remainingversions);
        }
        // Check that the versions we weren't expecting to remove weren't.
        foreach ($retainedversions as $retainedversion) {
            $this->assertContains((int) $retainedversion, $remainingversions);
        }
    }
}
