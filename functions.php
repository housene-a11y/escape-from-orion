<?php
// auteur: Mohamed
// functie: algemene functies CRUD

require_once 'config.php';

// ---- CONNECTIE ----
function connectDb() {
    try {
        $conn = new PDO(
            "mysql:host=" . SERVERNAME . ";dbname=" . DATABASE . ";charset=utf8mb4",
            USERNAME,
            PASSWORD
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    } catch (PDOException $e) {
        die("Verbinding mislukt: " . $e->getMessage());
    }
}

// ---- RIDDLES ----

// READ - alle riddles van een kamer
function getRiddlesByRoom($roomId) {
    $conn = connectDb();
    $stmt = $conn->prepare("SELECT * FROM riddles WHERE roomId = :roomId");
    $stmt->execute([':roomId' => $roomId]);
    return $stmt->fetchAll();
}

// READ - één riddle op id
function getRiddleById($id) {
    $conn = connectDb();
    $stmt = $conn->prepare("SELECT * FROM riddles WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

// READ - alle riddles
function getAllRiddles() {
    $conn = connectDb();
    $stmt = $conn->prepare("SELECT * FROM riddles");
    $stmt->execute();
    return $stmt->fetchAll();
}

// CREATE - riddle toevoegen
function insertRiddle($post) {
    $conn = connectDb();
    $stmt = $conn->prepare("INSERT INTO riddles (riddle, answer, hint, roomId) 
                            VALUES (:riddle, :answer, :hint, :roomId)");
    $stmt->execute([
        ':riddle' => $post['riddle'],
        ':answer' => $post['answer'],
        ':hint'   => $post['hint'],
        ':roomId' => $post['roomId']
    ]);
    return ($stmt->rowCount() == 1);
}

// UPDATE - riddle wijzigen
function updateRiddle($post) {
    $conn = connectDb();
    $stmt = $conn->prepare("UPDATE riddles 
                            SET riddle = :riddle, answer = :answer, 
                                hint = :hint, roomId = :roomId
                            WHERE id = :id");
    $stmt->execute([
        ':riddle' => $post['riddle'],
        ':answer' => $post['answer'],
        ':hint'   => $post['hint'],
        ':roomId' => $post['roomId'],
        ':id'     => $post['id']
    ]);
    return ($stmt->rowCount() == 1);
}

// DELETE - riddle verwijderen
function deleteRiddle($id) {
    $conn = connectDb();
    $stmt = $conn->prepare("DELETE FROM riddles WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return ($stmt->rowCount() == 1);
}

// ---- TEAMS ----

// READ - alle teams
function getAllTeams() {
    $conn = connectDb();
    $stmt = $conn->prepare("SELECT * FROM teams");
    $stmt->execute();
    return $stmt->fetchAll();
}

// READ - één team op id
function getTeamById($id) {
    $conn = connectDb();
    $stmt = $conn->prepare("SELECT * FROM teams WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

// DELETE - team verwijderen
function deleteTeam($id) {
    $conn = connectDb();
    $stmt = $conn->prepare("DELETE FROM teams WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return ($stmt->rowCount() == 1);
}

// ---- USERS ----

// READ - alle users
function getAllUsers() {
    $conn = connectDb();
    $stmt = $conn->prepare("SELECT u.*, t.team_name FROM users u LEFT JOIN teams t ON u.team_id = t.id");
    $stmt->execute();
    return $stmt->fetchAll();
}

// READ - user login
function getUserByUsername($username) {
    $conn = connectDb();
    $stmt = $conn->prepare("SELECT u.*, t.team_name FROM users u 
                            LEFT JOIN teams t ON u.team_id = t.id 
                            WHERE u.username = :username");
    $stmt->execute([':username' => $username]);
    return $stmt->fetch();
}

// CREATE - user registreren
function insertUser($username, $hashedPassword, $teamId) {
    $conn = connectDb();
    $stmt = $conn->prepare("INSERT INTO users (username, password, role, team_id) 
                            VALUES (:username, :password, 'speler', :team_id)");
    $stmt->execute([
        ':username' => $username,
        ':password' => $hashedPassword,
        ':team_id'  => $teamId
    ]);
    return ($stmt->rowCount() == 1);
}

// CREATE - team aanmaken bij registratie
function insertTeam($teamName) {
    $conn = connectDb();
    $stmt = $conn->prepare("INSERT INTO teams (team_name) VALUES (:team_name)");
    $stmt->execute([':team_name' => $teamName]);
    return $conn->lastInsertId();
}

// CHECK - username al bezet
function usernameExists($username) {
    $conn = connectDb();
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    return $stmt->fetch() ? true : false;
}
?>