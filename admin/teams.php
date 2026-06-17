<?php
// auteur: Mohamed
// functie: CRUD teams

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
    if (deleteTeam($id)) {
        $success = 'Team verwijderd.';
    } else {
        $error = 'Team kon niet worden verwijderd.';
    }
}

// UPDATE
if (isset($_POST['btn_update'])) {
    $conn = connectDb();
    $stmt = $conn->prepare("UPDATE teams SET team_name = :team_name WHERE id = :id");
    $stmt->execute([':team_name' => $_POST['team_name'], ':id' => $_POST['id']]);
    $success = 'Team bijgewerkt.';
}

// CREATE
if (isset($_POST['btn_insert'])) {
    $conn = connectDb();
    $stmt = $conn->prepare("INSERT INTO teams (team_name) VALUES (:team_name)");
    $stmt->execute([':team_name' => $_POST['new_team_name']]);
    $success = 'Team aangemaakt.';
}

$teams = getAllTeams();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ECLIPS‑7 – Teams Beheren</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .admin-container { max-width: 750px; margin: 40px auto; background: rgba(5,15,30,0.85); border: 1px solid #0288d1; border-radius: 16px; padding: 35px; }
    h1 { color: #4fc3f7; margin-bottom: 24px; }
    h2 { color: #90caf9; font-size: 1rem; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #1565c0; padding-bottom: 8px; margin: 24px 0 12px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th { color: #90caf9; text-align: left; padding: 10px; border-bottom: 1px solid #1565c0; font-size: 0.85rem; text-transform: uppercase; }
    td { padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #e0e0e0; }
    td input { background: rgba(255,255,255,0.06); border: 1px solid #1565c0; border-radius: 6px; color: #fff; padding: 6px 10px; width: 100%; box-sizing: border-box; }
    .btn { padding: 6px 14px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: bold; }
    .btn-save { background: #0288d1; color: white; }
    .btn-save:hover { background: #03a9f4; }
    .btn-delete { background: rgba(255,50,50,0.2); color: #ff6b6b; border: 1px solid #ff4444; }
    .btn-delete:hover { background: rgba(255,50,50,0.35); }
    .form-inline { display: flex; gap: 10px; margin-top: 10px; }
    .form-inline input { flex: 1; background: rgba(255,255,255,0.06); border: 1px solid #1565c0; border-radius: 8px; color: #fff; padding: 10px 14px; font-size: 1rem; }
    .btn-add { background: #0288d1; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
    .btn-add:hover { background: #03a9f4; }
    .success-msg { background: rgba(0,200,100,0.15); border: 1px solid #4caf50; border-radius: 8px; color: #a5d6a7; padding: 10px 14px; margin-bottom: 16px; }
    .error-msg { background: rgba(255,50,50,0.15); border: 1px solid #ff4444; border-radius: 8px; color: #ff6b6b; padding: 10px 14px; margin-bottom: 16px; }
    .back-link { color: #90caf9; text-decoration: none; font-size: 0.9rem; display: inline-block; margin-top: 20px; }
    .back-link:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="stars"></div>
  <div class="stars2"></div>
  <div class="stars3"></div>

  <div class="admin-container">
    <h1>👥 Teams Beheren</h1>

    <?php if ($success): ?><div class="success-msg">✅ <?php echo htmlspecialchars($success); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="error-msg">⚠️ <?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <h2>Alle teams</h2>
    <table>
      <tr><th>ID</th><th>Teamnaam</th><th>Acties</th></tr>
      <?php foreach ($teams as $team): ?>
      <tr>
        <form method="POST">
          <input type="hidden" name="id" value="<?php echo $team['id']; ?>">
          <td><?php echo $team['id']; ?></td>
          <td><input type="text" name="team_name" value="<?php echo htmlspecialchars($team['team_name']); ?>"></td>
          <td style="display:flex;gap:8px;align-items:center;">
            <button type="submit" name="btn_update" class="btn btn-save">💾 Opslaan</button>
            <a href="?delete=<?php echo $team['id']; ?>" class="btn btn-delete" onclick="return confirm('Team verwijderen?')">🗑️ Verwijderen</a>
          </td>
        </form>
      </tr>
      <?php endforeach; ?>
    </table>

    <h2>Nieuw team toevoegen</h2>
    <form method="POST">
      <div class="form-inline">
        <input type="text" name="new_team_name" placeholder="Naam van het nieuwe team" required>
        <button type="submit" name="btn_insert" class="btn-add">➕ Toevoegen</button>
      </div>
    </form>

    <a href="index.php" class="back-link">← Terug naar admin panel</a>
  </div>
</body>
</html>