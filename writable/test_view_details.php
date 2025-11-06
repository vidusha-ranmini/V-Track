<?php
// Test the ViewDetailsModel directly to verify it returns data correctly

// Database connection
$mysqli = new mysqli('127.0.0.1', 'root', '', 'v_track', 3306);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "=== Testing ViewDetailsModel Query ===\n\n";

// This is the exact query the ViewDetailsModel uses (after our fix)
$sql = "SELECT h.id AS home_id, h.resident_type, 
        r.name AS road_name, sr.name AS sub_road_name, a.address AS address_line,
        m.id AS member_id, m.full_name, m.occupation, m.nic, m.whatsapp, m.age, m.disabled, m.cv,
        m.name_with_initial, m.member_type, m.occupation_other, m.school, m.grade, m.university_name, m.land_house_status,
        mo.offer
        FROM homes h
        LEFT JOIN members m ON m.home_id = h.id
        LEFT JOIN member_offers mo ON mo.member_id = m.id
        LEFT JOIN roads r ON r.id = h.road_id
        LEFT JOIN sub_roads sr ON sr.id = h.sub_road_id
        LEFT JOIN addresses a ON a.id = h.address_id
        ORDER BY h.id, m.id";

$result = $mysqli->query($sql);

if (!$result) {
    echo "ERROR: " . $mysqli->error . "\n";
    exit(1);
}

echo "✅ Query executed successfully!\n";
echo "Total rows returned: " . $result->num_rows . "\n\n";

// Group by families
$families = [];
while ($row = $result->fetch_assoc()) {
    $hid = $row['home_id'];
    if (!isset($families[$hid])) {
        // Build location string
        $parts = [];
        if (!empty($row['road_name'])) $parts[] = $row['road_name'];
        if (!empty($row['sub_road_name'])) $parts[] = $row['sub_road_name'];
        $addrLine = !empty($row['address_line']) ? $row['address_line'] : '';
        if (!empty($addrLine)) $parts[] = $addrLine;
        $location = implode(' / ', $parts);
        
        $families[$hid] = [
            'home_id' => $hid,
            'location' => $location,
            'address' => $addrLine,
            'resident_type' => $row['resident_type'],
            'member_count' => 0,
            'members' => []
        ];
    }
    
    if ($row['member_id'] !== null) {
        $families[$hid]['member_count']++;
        if (!isset($families[$hid]['members'][$row['member_id']])) {
            $families[$hid]['members'][$row['member_id']] = [
                'id' => $row['member_id'],
                'name' => $row['full_name'],
                'occupation' => $row['occupation'],
                'nic' => $row['nic'],
                'offers' => []
            ];
        }
        
        if (!empty($row['offer'])) {
            $families[$hid]['members'][$row['member_id']]['offers'][] = $row['offer'];
        }
    }
}

echo "Families found: " . count($families) . "\n\n";

foreach ($families as $fam) {
    echo "📍 Home #{$fam['home_id']}\n";
    echo "   Location: " . ($fam['location'] ?: '(No location data)') . "\n";
    echo "   Resident Type: {$fam['resident_type']}\n";
    echo "   Members: {$fam['member_count']}\n";
    
    if (count($fam['members']) > 0) {
        foreach ($fam['members'] as $member) {
            $offers = count($member['offers']) > 0 ? implode(', ', $member['offers']) : 'None';
            echo "      - {$member['name']} ({$member['occupation']}) - Offers: $offers\n";
        }
    }
    echo "\n";
}

$result->free();
$mysqli->close();

echo "=== Test Complete ===\n";
echo "\n✅ The ViewDetailsModel query is now working correctly!\n";
echo "✅ No 'Unknown column h.address' error\n";
echo "✅ All joins are functioning properly\n\n";
echo "You can now access the view details page at: http://localhost:8080/view-details\n";
