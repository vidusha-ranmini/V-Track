<?php
// Debug script to check what ViewDetails controller is passing to the view
require __DIR__ . '/../vendor/autoload.php';

// Manually set up paths and bootstrap
define('ROOTPATH', realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR);
require ROOTPATH . 'app/Config/Paths.php';

$paths = new Config\Paths();

// Bootstrap
require ROOTPATH . 'system/bootstrap.php';

// Initialize services
$app = Config\Services::codeigniter();
$app->initialize();

echo "=== Testing ViewDetails Controller Output ===\n\n";

// Simulate what the controller does
use App\Models\ViewDetailsModel;

$model = new ViewDetailsModel();
$families = $model->getAllFamilies();

echo "Families count: " . count($families) . "\n\n";

foreach ($families as $idx => $family) {
    echo "Family #" . ($idx + 1) . ":\n";
    echo "  Location: " . ($family['location'] ?? 'N/A') . "\n";
    echo "  Address: " . ($family['address'] ?? 'N/A') . "\n";
    echo "  Resident Type: " . ($family['resident_type'] ?? 'N/A') . "\n";
    echo "  Members count: " . count($family['members']) . "\n";
    
    if (count($family['members']) > 0) {
        foreach ($family['members'] as $midx => $member) {
            echo "    Member " . ($midx + 1) . ": " . ($member['name'] ?? 'Unknown') . "\n";
            echo "      Occupation: " . ($member['occupation'] ?? 'N/A') . "\n";
            echo "      NIC: " . ($member['nic'] ?? 'N/A') . "\n";
            echo "      Offers: " . (count($member['offers']) > 0 ? implode(', ', $member['offers']) : 'None') . "\n";
        }
    }
    echo "\n";
}

// Show the JSON that would be passed to the view
$json = json_encode($families, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
echo "\n=== JSON Length ===\n";
echo strlen($json) . " characters\n\n";

echo "=== Sample JSON (first 500 chars) ===\n";
echo substr($json, 0, 500) . "...\n\n";

// Check if there are issues with the members array
$totalMembers = 0;
foreach ($families as $fam) {
    $totalMembers += count($fam['members']);
}

echo "=== Summary ===\n";
echo "Total families: " . count($families) . "\n";
echo "Total members: " . $totalMembers . "\n";

if ($totalMembers === 0) {
    echo "\n⚠️  WARNING: No members found! This is likely why the table is empty.\n";
    echo "Check if members are properly associated with homes in the database.\n";
} else {
    echo "\n✅ Members data is present and should display in the view.\n";
}
