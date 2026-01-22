<?php
// api_address.php
header('Content-Type: application/json; charset=utf-8');

// Lấy từ khóa tìm kiếm
$q = isset($_GET['q']) ? urlencode($_GET['q']) : '';

if (empty($q)) {
    echo json_encode([]);
    exit;
}

// URL của OpenStreetMap
$url = "https://nominatim.openstreetmap.org/search?format=json&q={$q}&addressdetails=1&limit=5&countrycodes=vn&accept-language=vi";

// QUAN TRỌNG: Phải giả lập User-Agent, nếu không OSM sẽ chặn request từ Server
$options = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: MilkTeaShopProject/1.0 (contact@email.com)\r\n"
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo json_encode(['error' => 'Không thể kết nối đến bản đồ']);
} else {
    echo $result;
}
?>