<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveHomeNumberFromHomes extends Migration
{
    public function up()
    {
        // SQLite does not support DROP COLUMN directly; recreate the table without `home_number`.
        $sql = <<<'SQL'
BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS homes_new (
    id INTEGER PRIMARY KEY,
    address VARCHAR(255) NOT NULL,
    road_id INTEGER NULL,
    sub_road_id INTEGER NULL,
    address_id INTEGER NULL,
    no_of_members INTEGER,
    has_assessment VARCHAR(10),
    assessment_number VARCHAR(255),
    resident_type VARCHAR(50),
    waste_disposal VARCHAR(50)
);
INSERT INTO homes_new (id, address, road_id, sub_road_id, address_id, no_of_members, has_assessment, assessment_number, resident_type, waste_disposal)
    SELECT id, address, road_id, sub_road_id, address_id, no_of_members, has_assessment, assessment_number, resident_type, waste_disposal FROM homes;
DROP TABLE homes;
ALTER TABLE homes_new RENAME TO homes;
COMMIT;
SQL;

        try {
            $this->db->query($sql);
        } catch (\Exception $e) {
            // If something goes wrong, log the error and rethrow
            log_message('error', 'RemoveHomeNumberFromHomes migration failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function down()
    {
        // Hard to add the column back safely in SQLite without recreating table again.
        // We'll keep down() empty to avoid accidental data loss.
    }
}
