<?php
// auteur: Mohamed
// functie: admin panel overzicht

session_start();
require_once '../functions.php';

// Alleen admin mag hier komen
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ECLIPS‑7 – Admin Panel</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .admin-container {
      max-width: 600px;
      margin: 60px auto;
      background: rgba(5, 15, 30, 0.85);
      border: 1px solid #0288d1;
      border-radius: 16px;
      padding: 40px 35px;
    }
    .admin-container h1 {
      color: #4fc3f7;
      margin-bottom: 30px;
    }
    .admin-section {
      margin-bottom: 30px;
    }
    .admin-section h2 {
      color: #90caf9;
      font-size: 1rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      border-bottom: 1px solid #1565c0;
      padding-bottom: 8px;
      margin-bottom: 12px;
    }
    .admin-links {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .admin-links a {
      color: #4fc3f7;
      text-decoration: none;
      padding: 10px 14px;
      border: 1px solid #1565c0;
      border-radius: 8px;
      background: rgba(255,255,255,0.03);
      transition: 0.2s;
    }
    .admin-links a:hover {
      background: rgba(2, 136, 209, 0.15);
      border-color: #4fc3f7;
    }
    .logout {
      margin-top: 30px;
      display: inline-block;
      color: #ff6b6b;
      text-decoration: none;
      font-size: 0.9rem;
    }
    .logout:hover { text-decoration: underline; }
  </style>
</head>
<body>

  <div class="stars"></div>
  <div class="stars2"></div>
  <div class="stars3"></div>

  <div class="admin-container">
    <h1>⚙️ Admin Panel — ECLIPS‑7</h1>

    <div class="admin-section">
      <h2>🧩 Riddles beheren</h2>
      <div class="admin-links">
        <a href="read_riddles.php">📋 Alle riddles bekijken</a>
        <a href="create_riddle.php">➕ Nieuwe riddle toevoegen</a>
      </div>
    </div>

    <div class="admin-section">
      <h2>👥 Teams beheren</h2>
      <div class="admin-links">
        <a href="teams.php">📋 Alle teams bekijken</a>
        <a href="scores.php">🏆 Scorepagina</a>
      </div>
    </div>

    <div class="admin-section">
      <h2>👤 Ingelogd als</h2>
      <div class="admin-links">
        <a href="#">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></a>
      </div>
    </div>

    <a href="../logout.php" class="logout">🚪 Uitloggen</a>
  </div>

</body>
</html>