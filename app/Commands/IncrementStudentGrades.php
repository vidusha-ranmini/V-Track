<?php
namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * CLI command to increment grade for students by 1 on promotion (e.g., new year).
 *
 * Usage: php spark students:increment-grades
 */
class IncrementStudentGrades extends BaseCommand
{
    protected $group = 'Maintenance';
    protected $name = 'students:increment-grades';
    protected $description = 'Increment grade by 1 for members with occupation=student (capped at 13).';

    public function run(array $params = [])
    {
        $db = \Config\Database::connect();

        // Fetch students
        try {
            $builder = $db->table('members');
        } catch (\Exception $e) {
            CLI::error('Could not access members table: ' . $e->getMessage());
            return;
        }

        $students = $builder->select('id, grade, full_name')->where('occupation', 'student')->get()->getResultArray();
        if (!$students) {
            CLI::write('No students found to process.');
            return;
        }

        $updated = 0;
        $skipped = 0;

        $db->transStart();
        foreach ($students as $s) {
            // Normalize grade to integer; if missing or non-numeric, skip
            $current = isset($s['grade']) ? (int) $s['grade'] : 0;
            if ($current <= 0) {
                $skipped++;
                continue;
            }
            if ($current >= 13) {
                // already at max grade; skip
                $skipped++;
                continue;
            }

            $new = $current + 1;
            try {
                $db->table('members')->where('id', $s['id'])->update(['grade' => (string)$new]);
                $updated++;
            } catch (\Exception $e) {
                CLI::error('Failed to update member ID ' . $s['id'] . ': ' . $e->getMessage());
            }
        }
        $db->transComplete();

        CLI::write("Student grade increment complete. Updated: {$updated}. Skipped: {$skipped}.");
    }
}
