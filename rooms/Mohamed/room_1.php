<?php
session_start();

// Timer starten bij eerste bezoek room 1
if (!isset($_SESSION['start_time'])) {
    $_SESSION['start_time'] = time();
}

// Voortgang bijhouden: welke riddles zijn al opgelost in room 1
if (!isset($_SESSION['solved_room1'])) {
    $_SESSION['solved_room1'] = [];
}

require_once '../../dbcon.php';

try {
    $stmt = $db_connection->query("SELECT * FROM riddles WHERE roomId = 1");
    $riddles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Databasefout: " . $e->getMessage());
}

$teamName = isset($_SESSION['team_name']) ? $_SESSION['team_name'] : 'Onbekend Team';
$totalRiddles = count($riddles);
$timeLimit = 600; // 10 minuten in seconden
$elapsed = time() - $_SESSION['start_time'];
$remaining = max(0, $timeLimit - $elapsed);
?>

<!DOCTYPE html>
<html lang="nl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ECLIPS‑7 – Kamer 1</title>
  <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

  <div class="stars"></div>
  <div class="stars2"></div>
  <div class="stars3"></div>

  <div class="room-header">
    <h1>⚠️ Kamer 1 – Controlesystemen</h1>
    <p class="team-label">Team: <strong><?php echo htmlspecialchars($teamName); ?></strong></p>
    <div class="timer-box">
      <span>⏱️ Tijd over: </span>
      <span id="timer" class="timer-display"><?php echo gmdate("i:s", $remaining); ?></span>
    </div>
    <p class="progress-label">Opgelost: <span id="progress">0</span> / <?php echo $totalRiddles; ?></p>
  </div>

  <p class="room-intro">
    De controlesystemen zijn beschadigd. Los alle puzzels op om door te gaan naar de volgende kamer.
  </p>

  <div class="container">
    <?php foreach ($riddles as $index => $riddle) : 
      $solved = in_array($riddle['id'], $_SESSION['solved_room1']);
    ?>
    <div 
      class="box box<?php echo $index + 1; ?> <?php echo $solved ? 'solved' : ''; ?>" 
      onclick="<?php echo $solved ? '' : "openModal($index)"; ?>"
      data-index="<?php echo $index; ?>"
      data-id="<?php echo $riddle['id']; ?>"
      data-riddle="<?php echo htmlspecialchars($riddle['riddle']); ?>"
      data-answer="<?php echo htmlspecialchars(strtolower($riddle['answer'])); ?>"
      data-hint="<?php echo htmlspecialchars($riddle['hint']); ?>"
    >
      <?php if ($solved): ?>
        ✅ Puzzel <?php echo $index + 1; ?>
      <?php else: ?>
        🔒 Puzzel <?php echo $index + 1; ?>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Overlay -->
  <div class="overlay" id="overlay" onclick="closeModal()"></div>

  <!-- Modal -->
  <div class="modal" id="modal">
    <button class="modal-close" onclick="closeModal()">✕</button>
    <h2>🛸 Puzzel</h2>
    <p id="modal-riddle"></p>
    <input type="text" id="answer-input" placeholder="Typ je antwoord..." onkeydown="if(event.key==='Enter') checkAnswer()">
    <div class="modal-buttons">
      <button onclick="checkAnswer()">Verzenden</button>
      <button class="hint-btn" onclick="showHint()">💡 Hint</button>
    </div>
    <p id="hint-text" class="hint-text" style="display:none;"></p>
    <p id="feedback" class="feedback"></p>
  </div>

  <script>
    // Variabelen
    const riddles = <?php echo json_encode($riddles); ?>;
    const solvedIds = <?php echo json_encode($_SESSION['solved_room1']); ?>;
    const timeLimit = <?php echo $timeLimit; ?>;
    const startTime = <?php echo $_SESSION['start_time']; ?>;
    const totalRiddles = <?php echo $totalRiddles; ?>;

    let currentIndex = null;
    let solvedCount = solvedIds.length;

    // Progress bijwerken bij laden
    document.getElementById('progress').textContent = solvedCount;

    // ---- TIMER ----
    function updateTimer() {
      const elapsed = Math.floor(Date.now() / 1000) - startTime;
      const remaining = Math.max(0, timeLimit - elapsed);

      const minutes = Math.floor(remaining / 60).toString().padStart(2, '0');
      const seconds = (remaining % 60).toString().padStart(2, '0');
      const timerEl = document.getElementById('timer');
      timerEl.textContent = `${minutes}:${seconds}`;

      if (remaining <= 60) {
        timerEl.style.color = '#ff4444';
      }

      if (remaining <= 0) {
        clearInterval(timerInterval);
        window.location.href = '../../verlies.php';
      }
    }

    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer();

    // ---- MODAL ----
    function openModal(index) {
      currentIndex = index;
      const riddle = riddles[index];

      document.getElementById('modal-riddle').textContent = riddle.riddle;
      document.getElementById('answer-input').value = '';
      document.getElementById('feedback').textContent = '';
      document.getElementById('hint-text').style.display = 'none';
      document.getElementById('hint-text').textContent = '';

      document.getElementById('modal').classList.add('active');
      document.getElementById('overlay').classList.add('active');
      document.getElementById('answer-input').focus();
    }

    function closeModal() {
      document.getElementById('modal').classList.remove('active');
      document.getElementById('overlay').classList.remove('active');
      currentIndex = null;
    }

    // ---- ANTWOORD CONTROLEREN ----
    function checkAnswer() {
      if (currentIndex === null) return;

      const riddle = riddles[currentIndex];
      const userAnswer = document.getElementById('answer-input').value.trim().toLowerCase();
      const correctAnswer = riddle.answer.trim().toLowerCase();
      const feedback = document.getElementById('feedback');

      if (userAnswer === correctAnswer) {
        feedback.textContent = '✅ Correct! Systeem hersteld.';
        feedback.style.color = '#4fc3f7';

        // Stuur naar server om op te slaan
        fetch('save_progress.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ riddleId: riddle.id, room: 1 })
        }).then(() => {
          solvedCount++;
          document.getElementById('progress').textContent = solvedCount;

          // Box als opgelost markeren
          const boxes = document.querySelectorAll('.box');
          boxes[currentIndex].classList.add('solved');
          boxes[currentIndex].textContent = `✅ Puzzel ${currentIndex + 1}`;
          boxes[currentIndex].onclick = null;

          setTimeout(() => {
            closeModal();
            // Als alle puzzels opgelost zijn, ga naar room 2
            if (solvedCount >= totalRiddles) {
              setTimeout(() => {
                window.location.href = '../Mohamed/room_2.php';
              }, 800);
            }
          }, 1000);
        });
      } else {
        feedback.textContent = '❌ Fout antwoord. Probeer opnieuw.';
        feedback.style.color = '#ff6b6b';
        document.getElementById('answer-input').select();
      }
    }

    // ---- HINT ----
    function showHint() {
      if (currentIndex === null) return;
      const hint = riddles[currentIndex].hint;
      const hintEl = document.getElementById('hint-text');
      hintEl.textContent = `💡 Hint: ${hint}`;
      hintEl.style.display = 'block';
    }
  </script>

</body>

</html>