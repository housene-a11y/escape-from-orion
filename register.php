<?php
// auteur: Mohamed
// functie: registratiepagina

session_start();
require_once 'functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username         = trim($_POST['username']);
    $password         = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);
    $team_name        = trim($_POST['team_name']);

    if (empty($username) || empty($password) || empty($team_name)) {
        $error = 'Vul alle velden in.';
    } elseif ($password !== $password_confirm) {
        $error = 'Wachtwoorden komen niet overeen.';
    } elseif (strlen($password) < 4) {
        $error = 'Wachtwoord moet minimaal 4 tekens zijn.';
    } elseif (usernameExists($username)) {
        $error = 'Deze gebruikersnaam is al bezet.';
    } else {
        $teamId = insertTeam($team_name);
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        if (insertUser($username, $hashed, $teamId)) {
            $success = 'Account aangemaakt! Je wordt doorgestuurd naar de login...';
            header('refresh:2;url=login.php');
        } else {
            $error = 'Er ging iets mis bij het aanmaken van je account.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ECLIPS‑7 – Registreren</title>
  <link rel="stylesheet" href="./css/style.css">
  <style>
    .auth-container {
      background: rgba(5, 15, 30, 0.85);
      border: 1px solid #0288d1;
      border-radius: 16px;
      padding: 40px 35px;
      max-width: 420px;
      margin: 60px auto;
      text-align: center;
      position: relative;
      z-index: 10;
    }
    .auth-container h1 { font-size: 1.6rem; margin-bottom: 5px; }
    .auth-subtitle {
      color: #78909c; font-size: 0.9rem;
      margin-bottom: 25px; background: none; padding: 0;
    }
    .form-group { margin-bottom: 16px; text-align: left; }
    .form-group label {
      display: block; color: #90caf9; font-size: 0.85rem;
      margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px;
    }
    .form-group input {
      width: 100%; padding: 11px 14px; border-radius: 8px;
      border: 1px solid #1565c0; background: rgba(255,255,255,0.06);
      color: #fff; font-size: 1rem; outline: none;
      transition: border 0.2s; box-sizing: border-box;
    }
    .form-group input:focus { border-color: #4fc3f7; background: rgba(255,255,255,0.1); }
    .auth-btn {
      width: 100%; padding: 13px; background: #0288d1;
      border: none; border-radius: 8px; color: white;
      font-size: 1rem; font-weight: bold; cursor: pointer;
      margin-top: 8px; transition: 0.3s; letter-spacing: 1px;
    }
    .auth-btn:hover { background: #03a9f4; transform: scale(1.02); }
    .error-msg {
      background: rgba(255, 50, 50, 0.15); border: 1px solid #ff4444;
      border-radius: 8px; color: #ff6b6b;
      padding: 10px 14px; margin-bottom: 16px; font-size: 0.9rem;
    }
    .success-msg {
      background: rgba(0, 200, 100, 0.15); border: 1px solid #4caf50;
      border-radius: 8px; color: #a5d6a7;
      padding: 10px 14px; margin-bottom: 16px; font-size: 0.9rem;
    }
    .auth-link { color: #4fc3f7; text-decoration: none; font-size: 0.9rem; }
    .auth-link:hover { text-decoration: underline; }
    .divider { color: #37474f; margin: 20px 0 15px; font-size: 0.85rem; background: none; padding: 0; }
  </style>
</head>
<body>

  <div class="stars"></div>
  <div class="stars2"></div>
  <div class="stars3"></div>

  <div class="auth-container">
    <h1>🚀 ECLIPS‑7</h1>
    <p class="auth-subtitle">Registreer je bemanning om te beginnen</p>

    <?php if ($error): ?>
      <div class="error-msg">⚠️ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success-msg">✅ <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>👤 Gebruikersnaam</label>
        <input type="text" name="username" placeholder="Kies een gebruikersnaam" required autocomplete="off">
      </div>
      <div class="form-group">
        <label>🔒 Wachtwoord</label>
        <input type="password" name="password" placeholder="Kies een wachtwoord" required>
      </div>
      <div class="form-group">
        <label>🔒 Bevestig wachtwoord</label>
        <input type="password" name="password_confirm" placeholder="Herhaal je wachtwoord" required>
      </div>
      <div class="form-group">
        <label>🛸 Teamnaam</label>
        <input type="text" name="team_name" placeholder="Geef je team een naam" required autocomplete="off">
      </div>
      <button type="submit" class="auth-btn">REGISTREER BEMANNING</button>
    </form>

    <p class="divider">Al een account?</p>
    <a href="login.php" class="auth-link">→ Inloggen</a>
  </div>

</body>
</html>