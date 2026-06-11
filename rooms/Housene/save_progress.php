<?php
session_start();

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data['riddleId']) && isset($data['room'])){

    $room = $data['room'];
    $riddleId = $data['riddleId'];

    if($room == 1){

        if(!isset($_SESSION['solved_room1'])){
            $_SESSION['solved_room1'] = [];
        }

        if(!in_array($riddleId, $_SESSION['solved_room1'])){
            $_SESSION['solved_room1'][] = $riddleId;
        }

    }

    if($room == 2){

        if(!isset($_SESSION['solved_room2'])){
            $_SESSION['solved_room2'] = [];
        }

        if(!in_array($riddleId, $_SESSION['solved_room2'])){
            $_SESSION['solved_room2'][] = $riddleId;
        }

    }

    echo json_encode(['success'=>true]);
}
?>