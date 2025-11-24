<?php
$xmlFile = 'lugumised.xml';
$jsonFile = 'veearvesti.json';

// Kontrolli, kas JSON-fail on olemas
if (!file_exists($jsonFile)) {
    if (!file_exists($xmlFile)) {
        die("XML file not found: $xmlFile");
    }

    $xml = simplexml_load_file($xmlFile);
    if (!$xml) die("Failed to load XML file");

    $data = [];

    foreach ($xml->xpath('//building') as $building) {
        $buildingId = (string)$building['id'];
        $buildingAddr = (string)$building['address'];

        foreach ($building->xpath('period') as $period) {
            $year = (string)$period['year'];
            $month = (string)$period['month'];

            foreach ($period->xpath('lugumised') as $r) {
                $data[] = [
                    'building_id' => $buildingId,
                    'building_address' => $buildingAddr,
                    'year' => $year,
                    'month' => $month,
                    'korter' => (string)$r['korter'],
                    'nimi' => (string)$r['nimi'],
                    'kuupaev' => (string)$r['kuupaev'],
                    'makstud' => (string)$r['makstud'],
                    'kuld_m3' => (string)$r->kuld_m3,
                    'soe_m3' => (string)$r->soe_m3
                ];
            }
        }
    }

    // Salvesta JSON-fail
    file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "JSON file created successfully with " . count($data) . " records.<br>";
} else {
    echo "JSON file already exists.<br>";
}
