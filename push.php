<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include 'config/conn.php';

$ids = [18,19];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['ids'])) {
        $ids = array_map('intval', explode(',', $_POST['ids']));
    }
}

if (empty($ids)) {
    echo json_encode(['error' => 'No shipments specified']);
    exit;
}

$results = [];
foreach ($ids as $id) {
    $stmt = $conn->prepare("SELECT * FROM fedex_shipments WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if (!$res) {
        $results[$id] = ['error' => 'Not found'];
        continue;
    }

    // Simulate API
    $apiReq = json_encode(['order_id'=>$res['order_id'], 'country'=>$res['country'], 'weight'=>$res['weight']]);
    $apiRes = json_encode(['success'=>true, 'tracking'=>'FDX'.str_pad($id,6,'0',STR_PAD_LEFT)]);
    $status = 'pushed';

    $update = $conn->prepare("UPDATE fedex_shipments SET api_request=?, api_response=?, status=? WHERE id=?");
    $update->bind_param("sssi", $apiReq, $apiRes, $status, $id);
    $update->execute();

    $results[$id] = ['success'=>true,'tracking'=>'FDX'.str_pad($id,6,'0',STR_PAD_LEFT)];
}

echo json_encode(['results'=>$results]);
