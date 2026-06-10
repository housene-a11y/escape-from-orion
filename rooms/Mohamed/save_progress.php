<?php
session_start();

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['riddleId']) && isset($data['room'])) {
    $riddleId = intval($data['riddleId']);
    $room = intval($data['room']);

    $sessionKey = 'solved_room' . $room;

    if (!isset($_SESSION[$sessionKey])) {
        $_SESSION[$sessionKey] = [];
    }

    if (!in_array($riddleId, $_SESSION[$sessionKey])) {
        $_SESSION[$sessionKey][] = $riddleId;
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}