<?php
// auteur: Mohamed
// functie: scorepagina alle teams met eindtijd

session_start();
require_once '../functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$conn = connectDb();
$stmt = $conn->prepare("SELECT * FROM teams ORDER BY end_time ASC");
$stmt->execute();
$teams = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ECLIPS‑7 – Scorepagina</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    .admin-container { max-width: 750px; margin: 40px auto; background: rgba(5,15,30,0.85); border: 1px solid #0288d1; border-radius: 16px; padding: 35px; }
    h1 { color: #4fc3f7; margin-bottom: 24px; }
    table { width: 100%; border-collapse: collapse; }
    th { color: #90caf9; text-align: left; padding: 12px; border-bottom: 1px solid #1565c0; font-size: 0.85rem; text-transform: uppercase; }
    td { padding: 12px; border-bottom: 1px solid rgba(255,255,255,0.05); color: #e0e0e0; }
    tr:hover td { background: rgba(2,136,209,0.08); }
    .rank-1 { color: #ffd700; font-weight: bold; }
    .rank-2 { color: #c0c0c0; font-weight: bold; }
    .rank-3 { color: #cd7f32; font-weight: bold; }
    .no-time { color: #546e7a; font-style: italic; }
    .back-link { color: #90caf9; text-decoration: none; font-size: 0.9rem; display: inline-block; margin-top: 20px; }
    .back-link:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="stars"></div>
  <div class="stars2"></div>
  <div class="stars3"></div>

  <div class="admin-container">
    <h1>🏆 Scorepagina — ECLIPS‑7</h1>

    <table>
      <tr>
        <th>#</th>
        <th>Teamnaam</th>
        <th>Eindtijd</th>
        <th>Tijd gebruikt</th>
      </tr>
      <?php foreach ($teams as $i => $team): ?>
      <tr>
        <td class="<?php echo $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : '')); ?>">
          <?php echo $i === 0 ? '🥇' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : $i + 1)); ?>
        </td>
        <td><?php echo htmlspecialchars($team['team_name']); ?></td>
        <td>
          <?php if ($team['end_time']): ?>
            <?php echo date('d-m-Y H:i:s', $team['end_time']); ?>
          <?php else: ?>
            <span class="no-time">Nog niet voltooid</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($team['end_time'] && isset($team['created_at'])): ?>
            <?php
              $start = strtotime($team['created_at']);
              $diff = $team['end_time'] - $start;
              $mins = floor($diff / 60);
              $secs = $diff % 60;
              echo "{$mins}m {$secs}s";
            ?>
          <?php else: ?>
            <span class="no-time">—</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>

    <a href="index.php" class="back-link">← Terug naar admin panel</a>
  </div>
</body>
</html>