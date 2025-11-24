<?php
$jsonFile = 'veearvesti.json';

$data = [];

if (file_exists($jsonFile)) {
    $data = json_decode(file_get_contents($jsonFile), true);
}

// Kui vorm saadeti
if (isset($_POST['submit'])) {
    $newReading = [
        'building_id' => $_POST['building_id'],
        'building_address' => $_POST['building_address'],
        'year' => $_POST['year'],
        'month' => $_POST['month'],
        'korter' => $_POST['korter'],
        'nimi' => $_POST['nimi'],
        'kuupaev' => $_POST['kuupaev'],
        'makstud' => $_POST['makstud'],
        'kuld_m3' => $_POST['kuld_m3'],
        'soe_m3' => $_POST['soe_m3']
    ];

    $data[] = $newReading;
    file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo "Uus andmerida lisatud!";
}

// Loe JSON-faili
$jsonFile = 'veearvesti.json';
if (!file_exists($jsonFile)) die("JSON file not found.");

$data = json_decode(file_get_contents($jsonFile), true);

// Käsitseda filtreid
$filterDate = $_GET['date'] ?? '';
$filterKorter = $_GET['korter'] ?? '';
$filterMakstud = $_GET['makstud'] ?? '';

$filtered = array_filter($data, function($row) use ($filterDate, $filterKorter, $filterMakstud) {
    return (!$filterDate || $row['kuupaev'] === $filterDate)
        && (!$filterKorter || $row['korter'] === $filterKorter)
        && (!$filterMakstud || $row['makstud'] === $filterMakstud);
});

// Hangi unikaalsed filtreerimisvõimalused
$dates = array_unique(array_column($data, 'kuupaev'));
$apartments = array_unique(array_column($data, 'korter'));
$paidStatus = array_unique(array_column($data, 'makstud'));
sort($dates); sort($apartments); sort($paidStatus);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Veearvesti näidud</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h2>Lisa uus veearvesti näit</h2>
<form method="post">
    <label>Hoone ID: <input type="text" name="building_id"></label><br>
    <label>Hoone aadress: <input type="text" name="building_address"></label><br>
    <label>Aasta: <input type="text" name="year"></label><br>
    <label>Kuu: <input type="text" name="month"></label><br>
    <label>Korter: <input type="text" name="korter"></label><br>
    <label>Nimi: <input type="text" name="nimi"></label><br>
    <label>Kuupäev: <input type="date" name="kuupaev"></label><br>
    <label>Makstud (jah/ei): <input type="text" name="makstud"></label><br>
    <label>Külm (m³): <input type="text" name="kuld_m3"></label><br>
    <label>Soe (m³): <input type="text" name="soe_m3"></label><br>
    <input type="submit" name="submit" value="Lisa">
</form>

<h2>Veearvesti näidud</h2>

<form method="GET">
    <label>Kuupäev:</label>
    <select name="date" onchange="this.form.submit()">
        <option value="">--Kõik--</option>
        <?php foreach($dates as $d): ?>
            <option value="<?= $d ?>" <?= $filterDate==$d?'selected':'' ?>><?= $d ?></option>
        <?php endforeach; ?>
    </select>

    <label>Korter:</label>
    <select name="korter" onchange="this.form.submit()">
        <option value="">--Kõik--</option>
        <?php foreach($apartments as $k): ?>
            <option value="<?= $k ?>" <?= $filterKorter==$k?'selected':'' ?>><?= $k ?></option>
        <?php endforeach; ?>
    </select>

    <label>Makstud:</label>
    <select name="makstud" onchange="this.form.submit()">
        <option value="">--Kõik--</option>
        <?php foreach($paidStatus as $p): ?>
            <option value="<?= $p ?>" <?= $filterMakstud==$p?'selected':'' ?>><?= $p ?></option>
        <?php endforeach; ?>
    </select>
</form>

<table>
    <thead>
        <tr>
            <th>Hoone</th><th>Aadress</th><th>Aasta</th><th>Kuu</th>
            <th>Korter</th><th>Nimi</th><th>Kuupäev</th><th>Külm (m³)</th>
            <th>Soe (m³)</th><th>Makstud</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($filtered as $row): ?>
            <tr>
                <td><?= $row['building_id'] ?></td>
                <td><?= $row['building_address'] ?></td>
                <td><?= $row['year'] ?></td>
                <td><?= $row['month'] ?></td>
                <td><?= $row['korter'] ?></td>
                <td><?= $row['nimi'] ?></td>
                <td><?= $row['kuupaev'] ?></td>
                <td><?= $row['kuld_m3'] ?></td>
                <td><?= $row['soe_m3'] ?></td>
                <td><?= $row['makstud'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
