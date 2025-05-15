<?php
session_start();

require 'vendor/autoload.php';
use MongoDB\Client as MongoClient;

// Connect to MongoDB
$mongo = new MongoClient("mongodb://mongodb_container:27017");
$collection = $mongo->phone_db->numbers;

// list of country
$countryFormats = [
    '+27'  => ['length' => 9,  'pattern' => '/^\+27[6-8]\d{8}$/'],   // South Africa
    '+263' => ['length' => 9,  'pattern' => '/^\+2637\d{8}$/'],      // Zimbabwe
    '+234' => ['length' => 10, 'pattern' => '/^\+234[7-9]\d{9}$/'],  // Nigeria
    '+254' => ['length' => 9,  'pattern' => '/^\+254[7]\d{8}$/'],    // Kenya
    '+212' => ['length' => 9,  'pattern' => '/^\+212[6-7]\d{8}$/'],  // Morocco
];

$results = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = (int)($_POST['quantity'] ?? 1);
    $countryCode = $_POST['country_code'] ?? '+1';

    if (!isset($countryFormats[$countryCode])) {
        die("Unsupported country code selected.");
    }
    $phoneNumbers = [];

    for ($i = 0; $i < $quantity; $i++) {
        $localLength = $countryFormats[$countryCode]['length'];
        $randomLocal = str_pad((string)rand(0, pow(10, $localLength) - 1), $localLength, '0', STR_PAD_LEFT);
        $number = $countryCode . $randomLocal;

        $phoneNumbers[] = $number;
    }

    // Save generated numbers to  MongoDB
    foreach ($phoneNumbers as $number) {
        $collection->insertOne([
            'number' => $number,
            'country_code' => $countryCode,
            'created_at' => new DateTime()
        ]);
    }

    // Send numbers to validation microservice
    $ch = curl_init('http://microservice:9000/validate');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['numbers' => $phoneNumbers]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);

    $results = json_decode($response, true) ?? [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Phone Number Generator</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select, button { padding: 8px; width: 200px; }
        button { background: #28a745; color: white; border: none; cursor: pointer; }
        .results { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #f2f2f2; }
        td, th { text-align: left; padding: 8px; }
    </style>
</head>
<body>
<h1>Generate Phone Numbers</h1>

<form method="POST">
    <div class="form-group">
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" min="1" max="100" value="1" required>
    </div>
    <div class="form-group">
        <label for="country_code">Country Code:</label>
        <select id="country_code" name="country_code" required>
            <option value="+27">+27 (South Africa)</option>
            <option value="+234">+234 (Nigeria)</option>
            <option value="+254">+254 (Kenya)</option>
            <option value="+263">+263 (Zimbabwe)</option>
            <option value="+212">+212 (Morocco)</option>
        </select>
    </div>
    <button type="submit">Generate</button>
</form>

<?php if (!empty($results)): ?>
    <div class="results">
        <h2>Validation Results</h2>
        <?php
        $validCount = $results['valid_count'] ?? 0;
        $total = count($results['numbers'] ?? []);
        $percentage = $total > 0 ? ($validCount / $total) * 100 : 0;
        echo "<p>Out of the $total numbers generated, $validCount were valid. That's " . number_format($percentage, 2) . "% valid.</p>";
        ?>

        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
            <tr>
                <th>#</th>
                <th>Phone Number</th>
                <th>Country Code</th>
                <th>Type</th>
                <th>Is Valid</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($results['numbers'] as $index => $info): ?>
                <tr style="background-color: <?= isset($info['is_valid']) && $info['is_valid'] ? '#d4edda' : '#f8d7da'; ?>;">
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($info['number']) ?></td>
                    <td><?= htmlspecialchars($info['country_code'] ?? 'N/A') ?></td>
                    <td><?= $info['type'] ?? 'N/A' ?></td>
                    <td><?= isset($info['is_valid']) && $info['is_valid'] ? '✅ Yes' : '❌ No' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
</body>
</html>
