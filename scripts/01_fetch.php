<?php
$basePath = dirname(__DIR__);
include $basePath . '/config.php';

$offset = 0;
$sort = urlencode('TRANSGRESS_DATE desc');
$url = 'https://data.epa.gov.tw/api/v1/ems_p_46?format=json&offset=' . $offset . '&limit=1000&sort=' . $sort . '&api_key=' . $apiKey;
$json = json_decode(file_get_contents($url), true);
$fhPool = array();
while(!empty($json['records'])) {
    foreach($json['records'] AS $record) {
        $parts = explode('-', $record['TRANSGRESS_DATE']);
        if(!isset($fhPool[$parts[0]])) {
            $fhPool[$parts[0]] = fopen($basePath . '/data/' . $parts[0] . '.csv', 'w');
            fputcsv($fhPool[$parts[0]], array_keys($record));
        }
        fputcsv($fhPool[$parts[0]], $record);
    }

    $offset += 1000;
    error_log('done - ' . $offset);
    $url = 'https://data.epa.gov.tw/api/v1/ems_p_46?format=json&offset=' . $offset . '&limit=1000&sort=' . $sort . '&api_key=' . $apiKey;
    $json = json_decode(file_get_contents($url), true);
}