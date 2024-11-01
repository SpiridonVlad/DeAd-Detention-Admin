<?php

// Author: Vlad Spiridon

declare(strict_types=1);

use MongoDB\BSON\ObjectId;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_response('Method not allowed', 405);
}

$jwt = validate_and_return_jwt();

if (is_null($jwt)) {
    send_response('Unauthorized', 401);
}

$database = get_db_conn();
$inmates_collection = $database->selectCollection('inmates');

$url = $_SERVER['REQUEST_URI'];

$center_id = extract_center_id_from_url();

$cursor = $inmates_collection->find([
    'center' => new ObjectId($center_id)
]);

$inmates = [];

foreach ($cursor as $inmate) {
    $filtered_inmate = [
        'id' => (string)$inmate['_id'],
        'image' => $inmate['image']->getData(),
        'name' => $inmate['fullName'],
        'crimes' => $inmate['crimes'],
        'sentences' => $inmate['sentences'],
        'center' => (string)$inmate['center'],
    ];
    $inmates[] = $filtered_inmate;
}

send_response_with_inmates($inmates);