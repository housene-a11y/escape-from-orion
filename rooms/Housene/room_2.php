<?php
session_start();
if (!isset($_SESSION['start_time'])) {
    $_SESSION['start_time'] = time();
}

require_once '../../dbcon.php';


try {

    $stmt = $db_connection->prepare("SELECT * FROM riddles WHERE roomId = ?");
    $stmt->execute([2]);

    $riddles = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    die("Databasefout: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escape Room</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<h1>Team: ...</h1>

<div class="container">

<?php foreach ($riddles as $index => $riddle): ?>

    <div
        class="box box<?php echo $index + 1; ?>"
        onclick="openModal(<?php echo $index; ?>)"
        data-index="<?php echo $index; ?>"
        data-riddle="<?php echo htmlspecialchars($riddle['riddle']); ?>"
        data-answer="<?php echo htmlspecialchars($riddle['answer']); ?>"
    >
        Box <?php echo $index + 1; ?>
    </div>

<?php endforeach; ?>

</div>

<section class="overlay" id="overlay" onclick="closeModal()"></section>

<section class="modal" id="modal">
    <h2>Escape Room Vraag</h2>

    <p id="riddle"></p>

    <input type="text" id="answer" placeholder="Typ je antwoord">

    <button onclick="checkAnswer()">Verzenden</button>

    <p id="feedback"></p>
</section>

<script src="../js/app.js"></script>

</body>
</html>