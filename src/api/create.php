<?php
// Allow CORS & JSON headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include_once '../db/Database.php';

$database = new Database();
$db = $database->connect();

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['title']) || !isset($data['link'])) {
    http_response_code(400);
    echo json_encode(["message" => "Missing fields"]);
    exit;
}

$title = $data['title'];
$link = $data['link'];
$date = date("Y-m-d H:i:s");

$query = "INSERT INTO bookmarks (title, link, date_added) VALUES (:title, :link, :date)";
$stmt = $db->prepare($query);
$stmt->bindParam(':title', $title);
$stmt->bindParam(':link', $link);
$stmt->bindParam(':date', $date);

if ($stmt->execute()) {
    echo json_encode(["message" => "Bookmark created"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Failed to add bookmark"]);
}
