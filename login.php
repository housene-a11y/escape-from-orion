<?php
session_start();
require_once 'dbcon.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Vul alle velden in.';
    } else {
        try {
            $stmt = $db_connection->prepare("SELECT u.*, t.team_name FROM users u LEFT JOIN teams t ON u.team_id = t.id WHERE u.username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Sessie instellen
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['team_name'] = $user['team_name'];
                $_SESSION['team_id'] = $user['team_id'];
                $_SESSION['start_time'] = time(); // Timer starten bij login

                if ($user['role'] === 'admin') {
                    header('Location: admin/index.php');
                } else {
                    header('Location: rooms/Mohamed/room_1.php');
                }
                exit;
            } else {
                $error = 'Gebruikersnaam of wachtwoord is onjuist.';
            }
        } catch (PDOException $e) {
            $error = 'Er ging iets mis: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ECLIPS‑7 – Inloggen</title>
  <link rel="stylesheet" href="./css/style.css">
  <style>
    .auth-container {
      background: rgba(5, 15, 30, 0.85);
      border: 1px solid #0288d1;
      border-radius: 16px;
      padding: 40px 35px;
      max-width: 420px;
      margin: 80px auto;
      text-align: center;
      position: relative;
      z-index: 10;
    }

    .auth-container h1 {
      font-size: 1.6rem;
      margin-bottom: 5px;
    }

    .auth-subtitle {
      color: #78909c;
      font-size: 0.9rem;
      margin-bottom: 25px;
      background: none;
      padding: 0;
    }

    .form-group {
      margin-bottom: 16px;
      text-align: left;
    }

    .form-group label {
      display: block;
      color: #90caf9;
      font-size: 0.85rem;
      margin-bottom: 5px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .form-group input {
      width: 100%;
      padding: 11px 14px;
      border-radius: 8px;
      border: 1px solid #1565c0;
      background: rgba(255,255,255,0.06);
      color: #fff;
      font-size: 1rem;
      outline: none;
      transition: border 0.2s;
      box-sizing: border-box;
    }

    .form-group input:focus {
      border-color: #4fc3f7;
      background: rgba(255,255,255,0.1);
    }

    .auth-btn {
      width: 100%;
      padding: 13px;
      background: #0288d1;
      border: none;
      border-radius: 8px;
      color: white;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      margin-top: 8px;
      transition: 0.3s;
      letter-spacing: 1px;
    }

    .auth-btn:hover {
      background: #03a9f4;
      transform: scale(1.02);
    }

    .error-msg {
      background: rgba(255, 50, 50, 0.15);
      border: 1px solid #ff4444;
      border-radius: 8px;
      color: #ff6b6b;
      padding: 10px 14px;
      margin-bottom: 16px;
      font-size: 0.9rem;
    }

    .auth-link {
      color: #4fc3f7;
      text-decoration: none;
      font-size: 0.9rem;
    }

    .auth-link:hover {
      text-decoration: underline;
    }

    .divider {
      color: #37474f;
      margin: 20px 0 15px;
      font-size: 0.85rem;
      background: none;
      padding: 0;
    }

    .ship-status {
      background: rgba(2, 136, 209, 0.1);
      border: 1px solid #0288d1;
      border-radius: 8px;
      padding: 10px;
      margin-bottom: 20px;
      font-size: 0.85rem;
      color: #90caf9;
    }
  </style>
</head>

<body>

  <div class="stars"></div>
  <div class="stars2"></div>
  <div class="stars3"></div>

  <div class="auth-container">
    <h1>🚀 ECLIPS‑7</h1>
    <p class="auth-subtitle">Identificeer jezelf om aan boord te gaan</p>

    <div class="ship-status">
      ⚠️ Scheepsstatus: <strong>KRITIEK</strong> — 10 minuten tot explosie
    </div>

    <?php if ($error): ?>
      <div class="error-msg">⚠️ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>👤 Gebruikersnaam</label>
        <input type="text" name="username" placeholder="Jouw gebruikersnaam" required autocomplete="off">
      </div>
      <div class="form-group">
        <label>🔒 Wachtwoord</label>
        <input type="password" name="password" placeholder="Jouw wachtwoord" required>
      </div>
      <button type="submit" class="auth-btn">🚀 AAN BOORD GAAN</button>
    </form>

    <p class="divider">Nog geen account?</p>
    <a href="register.php" class="auth-link">→ Registreer je bemanning</a>
  </div>

</body>

</html>