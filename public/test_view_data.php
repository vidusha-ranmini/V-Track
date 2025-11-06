<!DOCTYPE html>
<html>
<head>
    <title>Test View Details Data</title>
</head>
<body>
    <h1>Testing ViewDetails Data Output</h1>
    
    <?php
    require_once __DIR__ . '/../app/Models/ViewDetailsModel.php';
    
    $model = new \App\Models\ViewDetailsModel();
    $families = $model->getAllFamilies();
    $json = json_encode($families, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    
    echo "<h2>PHP Output</h2>";
    echo "<p>Families count: " . count($families) . "</p>";
    echo "<pre>" . print_r($families, true) . "</pre>";
    
    echo "<h2>JSON Output</h2>";
    echo "<pre>" . htmlspecialchars($json) . "</pre>";
    ?>
    
    <h2>JavaScript Test</h2>
    <div id="output"></div>
    
    <script>
    const data = <?= $json ?>;
    console.log('Data:', data);
    
    let html = '<h3>JavaScript parsed data:</h3>';
    html += '<p>Families: ' + data.length + '</p>';
    
    let totalMembers = 0;
    data.forEach((fam, idx) => {
        html += '<div style="border:1px solid #ccc; padding:10px; margin:10px 0;">';
        html += '<strong>Family ' + (idx+1) + ':</strong><br>';
        html += 'Location: ' + (fam.location || 'N/A') + '<br>';
        html += 'Resident Type: ' + fam.resident_type + '<br>';
        html += 'Members: ' + (fam.members ? fam.members.length : 0) + '<br>';
        
        if (fam.members && fam.members.length > 0) {
            html += '<ul>';
            fam.members.forEach((mem, midx) => {
                totalMembers++;
                html += '<li>' + mem.name + ' - ' + mem.occupation + '</li>';
            });
            html += '</ul>';
        }
        html += '</div>';
    });
    
    html += '<p><strong>Total members: ' + totalMembers + '</strong></p>';
    
    document.getElementById('output').innerHTML = html;
    </script>
</body>
</html>
