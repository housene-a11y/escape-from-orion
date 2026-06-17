<?php
// auteur: Mohamed
// functie: CRUD riddles

session_start();
require_once '../functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$success = '';
$error = '';

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if (deleteRiddle($id)) {
        $success = 'Riddle verwijderd.';
    } else {
        $error = 'Riddle kon niet worden verwijderd.';
    }
}

// UPDATE
if (isset($_POST['btn_update'])) {
    if (updateRiddle($_POST)) {
        $success = 'Riddle bijgewerkt.';
    } else {
        $error = 'Riddle kon niet worden bijgewerkt.';
    }
}

// CREATE
if (isset($_POST['btn_insert'])) {
    if (insertRiddle($_POST)) {
        $success = 'Riddle toegevoegd!';
    } else {
        $error = 'Riddle kon niet worden toegevoegd.';
    }
}

$riddles = getAllRiddles();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ECLIPS‑7 – Riddles Beheren</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .admin-container { max-width: 950px; margin: 40px auto; background: rgba(5,15,30,0.85); border: 1px solid #0288d1; border-radius: 16px; padding: 35px; }
    h1 { color: #4fc3f7; margin-bottom: 24px; }
    h2 { color: #90caf9; font-size: 1rem; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #1565c0; padding-bottom: 8px; margin: 24px 0 12px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    th { color: #90caf9; text-align: left; padding: 10px; border-bottom: 1px solid #1565c0; font-size: 0.85rem; text-transform: uppercase; }
    td { padding: 8px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #e0e0e0; vertical-align: middle; }
    td input, td select { background: rgba(255,255,255,0.06); border: 1px solid #1565c0; border-radius: 6px; color: #fff; padding: 6px 10px; width: 100%; box-sizing: border-box; font-size: 0.9rem; }
    td select option { background: #0d1b2a; }
    .btn { padding: 6px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.82rem; font-weight: bold; white-space: nowrap; }
    .btn-save { background: #0288d1; color: white; }
    .btn-save:hover { background: #03a9f4; }
    .btn-delete { background: rgba(255,50,50,0.2); color: #ff6b6b; border: 1px solid #ff4444; text-decoration: none; display: inline-block; }
    .btn-delete:hover { background: rgba(255,50,50,0.35); }
    .actions { display: flex; gap: 6px; }
    .success-msg { background: rgba(0,200,100,0.15); border: 1px solid #4caf50; border-radius: 8px; color: #a5d6a7; padding: 10px 14px; margin-bottom: 16px; }
    .error-msg { background: rgba(255,50,50,0.15); border: 1px solid #ff4444; border-radius: 8px; color: #ff6b6b; padding: 10px 14px; margin-bottom: 16px; }
    .form-group { margin-bottom: 14px; }
    .form-group label { display: block; color: #90caf9; font-size: 0.85rem; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px; }
    .form-group input, .form-group select { width: 100%; padding: 10px 14px; border-radius: 8px; border: 1px solid #1565c0; background: rgba(255,255,255,0.06); color: #fff; font-size: 1rem; outline: none; box-sizing: border-box; }
    .form-group input:focus, .form-group select:focus { border-color: #4fc3f7; }
    .form-group select option { background: #0d1b2a; }
    .btn-add { width: 100%; padding: 12px; background: #0288d1; border: none; border-radius: 8px; color: white; font-size: 1rem; font-weight: bold; cursor: pointer; margin-top: 6px; }
    .btn-add:hover { background: #03a9f4; }
    .back-link { color: #90caf9; text-decoration: none; font-size: 0.9rem; display: inline-block; margin-top: 20px; }
    .back-link:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="stars"></div>
  <div class="stars2"></div>
  <div class="stars3"></div>

  <div class="admin-container">
    <h1>🧩 Riddles Beheren</h1>

    <?php if ($success): ?><div class="success-msg">✅ <?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="error-msg">⚠️ <?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <!-- READ + UPDATE + DELETE -->
    <h2>Alle riddles</h2>
    <table>
      <tr>
        <th>ID</th>
        <th>Riddle</th>
        <th>Antwoord</th>
        <th>Hint</th>
        <th>Kamer</th>
        <th>Acties</th>
      </tr>
      <?php foreach ($riddles as $riddle): ?>
      <tr>
        <form method="POST">
          <input type="hidden" name="id" value="<?php echo $riddle['id']; ?>">
          <td><?php echo $riddle['id']; ?></td>
          <td><input type="text" name="riddle" value="<?php echo htmlspecialchars($riddle['riddle']); ?>"></td>
          <td><input type="text" name="answer" value="<?php echo htmlspecialchars($riddle['answer']); ?>"></td>
          <td><input type="text" name="hint" value="<?php echo htmlspecialchars($riddle['hint']); ?>"></td>
          <td>
            <select name="roomId">
              <option value="1" <?php echo $riddle['roomId'] == 1 ? 'selected' : ''; ?>>Kamer 1</option>
              <option value="2" <?php echo $riddle['roomId'] == 2 ? 'selected' : ''; ?>>Kamer 2</option>
            </select>
          </td>
          <td>
            <div class="actions">
              <button type="submit" name="btn_update" class="btn btn-save">💾</button>
              <a href="?delete=<?php echo $riddle['id']; ?>" class="btn btn-delete" onclick="return confirm('Riddle verwijderen?')">🗑️</a>
            </div>
          </td>
        </form>
      </tr>
      <?php endforeach; ?>
    </table>

    <!-- CREATE -->
    <h2>➕ Nieuwe riddle toevoegen</h2>
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
      <button type="submit" name="btn_insert" class="btn-add">➕ Toevoegen</button>
    </form>

    <a href="index.php" class="back-link">← Terug naar admin panel</a>
  </div>
</body>
</html>