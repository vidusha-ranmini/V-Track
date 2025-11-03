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

        // Select road/sub-road/address names where available and fall back to home's address column
        $select = 'h.id AS home_id, h.address AS home_address, h.resident_type, '
            . 'r.name AS road_name, sr.name AS sub_road_name, a.address AS address_line, '
            . implode(', ', $memberFields) . ', mo.offer';

        $builder = $this->db->table('homes h')
            ->select($select)
            ->join('members m', 'm.home_id = h.id', 'left')
            ->join('member_offers mo', 'mo.member_id = m.id', 'left')
            ->join('roads r', 'r.id = h.road_id', 'left')
            ->join('sub_roads sr', 'sr.id = h.sub_road_id', 'left')
            ->join('addresses a', 'a.id = h.address_id', 'left')
            ->orderBy('h.id, m.id');

        $results = $builder->get()->getResultArray();

        $families = [];
        foreach ($results as $row) {
            $hid = $row['home_id'];
            if (!isset($families[$hid])) {
                // Build a human-readable location string from road/sub-road/address
                $parts = [];
                if (!empty($row['road_name'])) $parts[] = $row['road_name'];
                if (!empty($row['sub_road_name'])) $parts[] = $row['sub_road_name'];
                // prefer the normalized address line when available, otherwise home's address
                $addrLine = !empty($row['address_line']) ? $row['address_line'] : (!empty($row['home_address']) ? $row['home_address'] : '');
                if (!empty($addrLine)) $parts[] = $addrLine;
                $location = implode(' / ', $parts);

                $families[$hid] = [
                    'location' => $location,
                    'address' => $addrLine,
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
                    // Ensure NIC is returned as a string to avoid JavaScript numeric overflow
                    'nic' => isset($row['nic']) && $row['nic'] !== null ? (string) $row['nic'] : '',
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
                'location' => $f['location'],
                'address' => $f['address'],
                'resident_type' => $f['resident_type'],
                'members' => $members,
            ];
        }

        return $out;
    }
}
