<?php
namespace App\Models;

use CodeIgniter\Model;

class ViewDetailsModel
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Return an array of families where each family contains members and each member contains offers array.
     * @return array
     */
    public function getAllFamilies()
    {
        // Build a safe select list based on actual columns present in the members table.
        $memberFields = [];
        // Always include these base columns (if members table exists these should exist)
        $memberFields[] = 'm.id AS member_id';
        $memberFields[] = 'm.full_name';
        $memberFields[] = 'm.occupation';
        $memberFields[] = 'm.nic';
        $memberFields[] = 'm.whatsapp';
        $memberFields[] = 'm.age';
        $memberFields[] = 'm.disabled';
        $memberFields[] = 'm.cv';

        // Optional columns that may or may not exist in the schema
        $optional = ['name_with_initial','member_type','occupation_other','school','grade','university_name','land_house_status','exit_date'];
        try {
            $existing = $this->db->getFieldNames('members');
        } catch (\Exception $e) {
            // If table doesn't exist or error, fall back to an empty array to avoid failures
            $existing = [];
        }
        foreach ($optional as $col) {
            if (in_array($col, $existing)) {
                $memberFields[] = 'm.' . $col;
            }
        }

        $select = 'h.id AS home_id, h.home_number AS house_number, h.address, h.resident_type, ' . implode(', ', $memberFields) . ', mo.offer';

        $builder = $this->db->table('homes h')
            ->select($select)
            ->join('members m', 'm.home_id = h.id', 'left')
            ->join('member_offers mo', 'mo.member_id = m.id', 'left')
            ->orderBy('h.id, m.id');

        $results = $builder->get()->getResultArray();

        $families = [];
        foreach ($results as $row) {
            $hid = $row['home_id'];
            if (!isset($families[$hid])) {
                $families[$hid] = [
                    'house_number' => $row['house_number'],
                    'address' => $row['address'],
                    'resident_type' => $row['resident_type'],
                    'members' => [],
                ];
            }

            if ($row['member_id'] === null) {
                // no members for this home
                continue;
            }

            // ensure member entry exists
            if (!isset($families[$hid]['members'][$row['member_id']])) {
                $families[$hid]['members'][$row['member_id']] = [
                    'id' => $row['member_id'],
                    'name' => $row['full_name'],
                    'name_with_initial' => isset($row['name_with_initial']) ? $row['name_with_initial'] : null,
                    'member_type' => isset($row['member_type']) ? $row['member_type'] : null,
                    'occupation' => $row['occupation'],
                    'occupation_other' => isset($row['occupation_other']) ? $row['occupation_other'] : null,
                    'school' => isset($row['school']) ? $row['school'] : null,
                    'grade' => isset($row['grade']) ? $row['grade'] : null,
                    'university_name' => isset($row['university_name']) ? $row['university_name'] : null,
                    'offers' => [],
                    'nic' => $row['nic'],
                    'whatsapp' => $row['whatsapp'],
                    'age' => isset($row['age']) ? $row['age'] : null,
                    'disabled' => isset($row['disabled']) ? $row['disabled'] : null,
                    'cv' => isset($row['cv']) ? $row['cv'] : null,
                    'land_house_status' => isset($row['land_house_status']) ? $row['land_house_status'] : null,
                    'exit_date' => isset($row['exit_date']) ? $row['exit_date'] : null,
                ];
            }

            if (!empty($row['offer'])) {
                $families[$hid]['members'][$row['member_id']]['offers'][] = $row['offer'];
            }
        }

        // Convert members map to indexed arrays
        $out = [];
        foreach ($families as $f) {
            $members = [];
            foreach ($f['members'] as $m) {
                // remove duplicate offers
                $m['offers'] = array_values(array_unique($m['offers']));
                $members[] = $m;
            }
            $out[] = [
                'house_number' => $f['house_number'],
                'address' => $f['address'],
                'resident_type' => $f['resident_type'],
                'members' => $members,
            ];
        }

        return $out;
    }
}
