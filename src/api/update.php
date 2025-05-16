<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

include_once '../db/Database.php';

$database = new Database();
$db = $database->connect();

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id']) || !isset($data['title']) || !isset($data['link'])) {
    http_response_code(400);
    echo json_encode(["message" => "Missing required fields"]);
    exit;
}

$id = $data['id'];
$title = $data['title'];
$link = $data['link'];

$query = "UPDATE bookmarks SET title = :title, link = :link WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':title', $title);
$stmt->bindParam(':link', $link);
$stmt->bindParam(':id', $id);

if ($stmt->execute()) {
    echo json_encode(["message" => "Bookmark updated"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Update failed"]);
}
