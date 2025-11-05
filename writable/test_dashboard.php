<?php
// Simple test script to verify data is accessible

// Database connection
$mysqli = new mysqli('127.0.0.1', 'root', '', 'v_track', 3306);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "=== Database Connection Test ===\n";
echo "Connected to MySQL successfully.\n\n";

// Test occupation counts
echo "1. Testing Occupation Counts:\n";
$result = $mysqli->query("SELECT occupation, COUNT(*) AS cnt FROM members GROUP BY occupation ORDER BY cnt DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "   - {$row['occupation']}: {$row['cnt']}\n";
    }
    $result->free();
} else {
    echo "   ERROR: " . $mysqli->error . "\n";
}
echo "\n";

// Test age buckets
echo "2. Testing Age Distribution:\n";
$sql = "SELECT
    SUM(CASE WHEN age BETWEEN 0 AND 5 THEN 1 ELSE 0 END) AS a0_5,
    SUM(CASE WHEN age BETWEEN 6 AND 18 THEN 1 ELSE 0 END) AS a6_18,
    SUM(CASE WHEN age BETWEEN 19 AND 30 THEN 1 ELSE 0 END) AS a19_30,
    SUM(CASE WHEN age BETWEEN 31 AND 50 THEN 1 ELSE 0 END) AS a31_50,
    SUM(CASE WHEN age BETWEEN 51 AND 70 THEN 1 ELSE 0 END) AS a51_70,
    SUM(CASE WHEN age >= 71 THEN 1 ELSE 0 END) AS a71
    FROM members";
$result = $mysqli->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "   - 0-5: {$row['a0_5']}\n";
    echo "   - 6-18: {$row['a6_18']}\n";
    echo "   - 19-30: {$row['a19_30']}\n";
    echo "   - 31-50: {$row['a31_50']}\n";
    echo "   - 51-70: {$row['a51_70']}\n";
    echo "   - 71+: {$row['a71']}\n";
    $result->free();
} else {
    echo "   ERROR: " . $mysqli->error . "\n";
}
echo "\n";

// Test resident types
echo "3. Testing Resident Types:\n";
$result = $mysqli->query("SELECT resident_type, COUNT(*) AS cnt FROM homes GROUP BY resident_type");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "   - {$row['resident_type']}: {$row['cnt']}\n";
    }
    $result->free();
} else {
    echo "   ERROR: " . $mysqli->error . "\n";
}
echo "\n";

// Test waste collectors (FIXED COLUMN NAME)
echo "4. Testing Waste Collectors:\n";
$result = $mysqli->query("SELECT waste_collector, COUNT(*) AS cnt FROM homes GROUP BY waste_collector");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $wc = $row['waste_collector'] ?? 'NULL';
        echo "   - {$wc}: {$row['cnt']}\n";
    }
    $result->free();
} else {
    echo "   ERROR: " . $mysqli->error . "\n";
}
echo "\n";

// Test offers
echo "5. Testing Offers:\n";
$result = $mysqli->query("SELECT offer, COUNT(*) AS cnt FROM member_offers GROUP BY offer");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "   - {$row['offer']}: {$row['cnt']}\n";
    }
    $result->free();
} else {
    echo "   ERROR: " . $mysqli->error . "\n";
}
echo "\n";

// Test disabled status
echo "6. Testing Disabled Status:\n";
$result = $mysqli->query("SELECT disabled, COUNT(*) AS cnt FROM members GROUP BY disabled");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $disabled = $row['disabled'] ?? 'NULL';
        echo "   - {$disabled}: {$row['cnt']}\n";
    }
    $result->free();
} else {
    echo "   ERROR: " . $mysqli->error . "\n";
}
echo "\n";

// Test view details query
echo "7. Testing View Details Query:\n";
$sql = "SELECT h.id AS home_id, h.resident_type, 
        r.name AS road_name, sr.name AS sub_road_name, a.address AS address_line,
        m.id AS member_id, m.full_name, m.occupation, m.nic, m.whatsapp, m.age, m.disabled, m.cv,
        mo.offer
        FROM homes h
        LEFT JOIN members m ON m.home_id = h.id
        LEFT JOIN member_offers mo ON mo.member_id = m.id
        LEFT JOIN roads r ON r.id = h.road_id
        LEFT JOIN sub_roads sr ON sr.id = h.sub_road_id
        LEFT JOIN addresses a ON a.id = h.address_id
        ORDER BY h.id, m.id
        LIMIT 5";
$result = $mysqli->query($sql);
if ($result) {
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $count++;
        echo "   Row $count: Home#{$row['home_id']}, Member: {$row['full_name']}, Location: {$row['road_name']}/{$row['sub_road_name']}\n";
    }
    echo "   Total rows returned: $count\n";
    $result->free();
} else {
    echo "   ERROR: " . $mysqli->error . "\n";
}
echo "\n";

$mysqli->close();
echo "=== All Tests Complete ===\n";

