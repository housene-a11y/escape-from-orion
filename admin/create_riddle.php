<?php
// auteur: Mohamed
// functie: nieuwe riddle toevoegen

session_start();
require_once '../functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$success = '';
$error   = '';

if (isset($_POST['btn_insert'])) {
    if (insertRiddle($_POST)) {
        $success = 'Riddle succesvol toegevoegd!';
    } else {
        $error = 'Riddle kon niet worden toegevoegd.';
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ECLIPS‑7 – Riddle Toevoegen</title>
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
    h1 { color: #4fc3f7; margin-bottom: 24px; }
    .form-group { margin-bottom: 16px; }
    .form-group label {
      display: block;
      color: #90caf9;
      font-size: 0.85rem;
      margin-bottom: 5px;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .form-group input,
    .form-group select {
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
    .form-group input:focus,
    .form-group select:focus {
      border-color: #4fc3f7;
      background: rgba(255,255,255,0.1);
    }
    .form-group select option { background: #0d1b2a; }
    .btn-submit {
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
    }
    .btn-submit:hover { background: #03a9f4; }
    .success-msg {
      background: rgba(0, 200, 100, 0.15);
      border: 1px solid #4caf50;
      border-radius: 8px;
      color: #a5d6a7;
      padding: 10px 14px;
      margin-bottom: 16px;
      font-size: 0.9rem;
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
    .back-link {
      color: #90caf9;
      text-decoration: none;
      font-size: 0.9rem;
      display: inline-block;
      margin-top: 24px;
    }
    .back-link:hover { text-decoration: underline; }
  </style>
</head>
<body>

  <div class="stars"></div>
  <div class="stars2"></div>
  <div class="stars3"></div>

  <div class="admin-container">
    <h1>➕ Riddle Toevoegen</h1>

    <?php if ($success): ?>
      <div class="success-msg">✅ <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
      <div class="error-msg">⚠️ <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Riddle</label>
        <input type="text" name="riddle" placeholder="Typ de vraag..." required>
      </div>
      <div class="form-group">
        <label>Antwoord</label>
        <input type="text" name="answer" placeholder="Typ het antwoord..." required>
      </div>
      <div class="form-group">
        <label>Hint</label>
        <input type="text" name="hint" placeholder="Typ een hint...">
      </div>
      <div class="form-group">
        <label>Kamer</label>
        <select name="roomId" required>
          <option value="1">Kamer 1</option>
          <option value="2">Kamer 2</option>
        </select>
      </div>
      <button type="submit" name="btn_insert" class="btn-submit">➕ Toevoegen</button>
    </form>

    <a href="read_riddles.php" class="back-link">← Terug naar riddles</a>
  </div>

</body>
</html>