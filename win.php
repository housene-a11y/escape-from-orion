<?php
session_start();
require_once 'functions.php';

// Eindtijd opslaan in database
if (isset($_SESSION['team_id']) && isset($_SESSION['start_time'])) {
    $conn = connectDb();
    $stmt = $conn->prepare("UPDATE teams SET end_time = :end_time WHERE id = :id");
    $stmt->execute([
        ':end_time' => time(),
        ':id' => $_SESSION['team_id']
    ]);
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ECLIPS‑7 – Missie Geslaagd</title>
  <link rel="stylesheet" href="./css/style.css">
</head>

<body>

  <div class="stars"></div>
  <div class="stars2"></div>
  <div class="stars3"></div>

  <h1>🎉 Missie Geslaagd!</h1>

  <p>
    Je hebt de noodcapsule weten te openen en bent ontsnapt voordat de
    <strong>ECLIPS‑7</strong> ontplofte.
  </p>

  <p>
    Dankzij jouw snelle denken en teamwork is de missie succesvol afgerond.
  </p>

  <?php if (isset($_SESSION['team_name'])): ?>
  <p>🛸 Team: <strong><?php echo htmlspecialchars($_SESSION['team_name']); ?></strong></p>
  <?php endif; ?>

  <h2>Je hebt gewonnen! 🏆</h2>

  <button>
    <a href="reset.php">🔄 Opnieuw spelen</a>
  </button>

</body>

</html>