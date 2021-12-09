<?php

require_once("PrzesuwankaSearch.php");
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');

// get raw data from http request body
$data = json_decode(file_get_contents("php://input"));

// Required fields
if(empty($data->state)) {
    http_response_code(422);
    echo json_encode(["message" => "State required."]);
    exit;
}

$algorytm = match($data->type) {
 'ASTAR' => Algo::ASTAR,
 'DFS' => Algo::DFS,
 DEFAULT => Algo::GREEDY
};

$stan = $data->state;
$ukladanka = new PrzesuwankaSearch(stanpoczatkowy: $stan,algo:$algorytm);
$sol = $ukladanka->search();

if (!empty($sol)) {
    echo json_encode(["message" => $sol]);
} else {
    echo json_encode(['message' => 'Solution not found.']);
}


