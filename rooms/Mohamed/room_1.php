<?php
session_start();

if (!isset($_SESSION['start_time'])) {
    $_SESSION['start_time'] = time();
}

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
$timeLimit = 600;
$elapsed = time() - $_SESSION['start_time'];
$remaining = max(0, $timeLimit - $elapsed);

// Afbeelding per riddle (op volgorde van roomId=1 riddles)
$images = [
    '../../img/panel.png',
    '../../img/radar.png',
    '../../img/noodkast.png'
];
?>

<!DOCTYPE html>
<html lang="nl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ECLIPS‑7 – Kamer 1</title>
  <link rel="stylesheet" href="../../css/style.css">
  <style>
    body {
    background: url('../../img/kamer1.jpg') no-repeat center center fixed;
    background-size: cover;
    }

    /* Puzzel box hover — icoon zichtbaar */
    .box {
      position: relative;
      flex-direction: column;
      gap: 10px;
    }

    .box .img-icon {
      display: none;
      font-size: 1.4rem;
      background: rgba(0,0,0,0.5);
      border-radius: 50%;
      padding: 4px 8px;
      cursor: pointer;
      transition: transform 0.2s;
      z-index: 10;
    }

    .box:hover .img-icon {
      display: inline-block;
    }

    .box .img-icon:hover {
      transform: scale(1.2);
    }

    /* Lightbox */
    .lightbox {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.92);
      z-index: 500;
      align-items: center;
      justify-content: center;
      flex-direction: column;
    }

    .lightbox.active {
      display: flex;
    }

    .lightbox img {
      max-width: 90vw;
      max-height: 80vh;
      border-radius: 12px;
      border: 2px solid #0288d1;
      box-shadow: 0 0 40px rgba(79, 195, 247, 0.4);
    }

    .lightbox-close {
      position: absolute;
      top: 20px;
      right: 30px;
      font-size: 2rem;
      color: white;
      cursor: pointer;
      background: rgba(0,0,0,0.5);
      border: none;
      border-radius: 50%;
      width: 44px;
      height: 44px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.2s;
    }

    .lightbox-close:hover {
      background: rgba(255,0,0,0.5);
      transform: none;
    }

    .lightbox-caption {
      color: #90caf9;
      margin-top: 12px;
      font-size: 0.9rem;
    }
  </style>

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
    <p class="progress-label">Opgelost: <span id="progress"><?php echo count($_SESSION['solved_room1']); ?></span> / <?php echo $totalRiddles; ?></p>
  </div>

  <p class="room-intro">
    De controlesystemen zijn beschadigd. Bekijk de afbeeldingen 🔍 en los alle puzzels op!
  </p>

  <div class="container">
    <?php foreach ($riddles as $index => $riddle) : 
      $solved = in_array($riddle['id'], $_SESSION['solved_room1']);
      $img = $images[$index] ?? null;
    ?>
    <div 
      class="box box<?php echo $index + 1; ?> <?php echo $solved ? 'solved' : ''; ?>" 
      data-index="<?php echo $index; ?>"
      data-id="<?php echo $riddle['id']; ?>"
      data-riddle="<?php echo htmlspecialchars($riddle['riddle']); ?>"
      data-answer="<?php echo htmlspecialchars(strtolower($riddle['answer'])); ?>"
      data-hint="<?php echo htmlspecialchars($riddle['hint']); ?>"
      data-img="<?php echo $img; ?>"
      onclick="handleBoxClick(event, <?php echo $index; ?>, <?php echo $solved ? 'true' : 'false'; ?>)"
    >
      <?php if ($solved): ?>
        ✅ Puzzel <?php echo $index + 1; ?>
      <?php else: ?>
        🔒 Puzzel <?php echo $index + 1; ?>
        <?php if ($img): ?>
          <span class="img-icon" title="Bekijk afbeelding">🔍</span>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Overlay modal -->
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

  <!-- Lightbox -->
  <div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="closeLightbox()">✕</button>
    <img id="lightbox-img" src="" alt="Puzzel afbeelding">
    <p class="lightbox-caption">Klik ergens om te sluiten</p>
  </div>

  <script>
    const riddles = <?php echo json_encode($riddles); ?>;
    const solvedIds = <?php echo json_encode($_SESSION['solved_room1']); ?>;
    const timeLimit = <?php echo $timeLimit; ?>;
    const startTime = <?php echo $_SESSION['start_time']; ?>;
    const totalRiddles = <?php echo $totalRiddles; ?>;

    let currentIndex = null;
    let solvedCount = solvedIds.length;

    document.getElementById('progress').textContent = solvedCount;

    // ---- TIMER ----
    function updateTimer() {
      const elapsed = Math.floor(Date.now() / 1000) - startTime;
      const remaining = Math.max(0, timeLimit - elapsed);
      const minutes = Math.floor(remaining / 60).toString().padStart(2, '0');
      const seconds = (remaining % 60).toString().padStart(2, '0');
      const timerEl = document.getElementById('timer');
      timerEl.textContent = `${minutes}:${seconds}`;
      if (remaining <= 60) timerEl.style.color = '#ff4444';
      if (remaining <= 0) {
        clearInterval(timerInterval);
        window.location.href = '../../verlies.php';
      }
    }
    const timerInterval = setInterval(updateTimer, 1000);
    updateTimer();

    // ---- BOX CLICK ----
    function handleBoxClick(event, index, solved) {
      if (solved) return;
      // Als op het 🔍 icoon geklikt
      if (event.target.classList.contains('img-icon')) {
        event.stopPropagation();
        const box = event.target.closest('.box');
        openLightbox(box.dataset.img);
      } else {
        openModal(index);
      }
    }

    // ---- MODAL ----
    function openModal(index) {
      currentIndex = index;
      const riddle = riddles[index];
      document.getElementById('modal-riddle').textContent = riddle.riddle;
      document.getElementById('answer-input').value = '';
      document.getElementById('feedback').textContent = '';
      document.getElementById('hint-text').style.display = 'none';
      document.getElementById('modal').classList.add('active');
      document.getElementById('overlay').classList.add('active');
      document.getElementById('answer-input').focus();
    }

    function closeModal() {
      document.getElementById('modal').classList.remove('active');
      document.getElementById('overlay').classList.remove('active');
      currentIndex = null;
    }

    // ---- LIGHTBOX ----
    function openLightbox(src) {
      document.getElementById('lightbox-img').src = src;
      document.getElementById('lightbox').classList.add('active');
    }

    function closeLightbox() {
      document.getElementById('lightbox').classList.remove('active');
    }

    // ---- ANTWOORD ----
    function checkAnswer() {
      if (currentIndex === null) return;
      const riddle = riddles[currentIndex];
      const userAnswer = document.getElementById('answer-input').value.trim().toLowerCase();
      const correctAnswer = riddle.answer.trim().toLowerCase();
      const feedback = document.getElementById('feedback');

      if (userAnswer === correctAnswer) {
        feedback.textContent = '✅ Correct! Systeem hersteld.';
        feedback.style.color = '#4fc3f7';

        fetch('save_progress.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ riddleId: riddle.id, room: 1 })
        }).then(() => {
          solvedCount++;
          document.getElementById('progress').textContent = solvedCount;

          const boxes = document.querySelectorAll('.box');
          boxes[currentIndex].classList.add('solved');
          boxes[currentIndex].innerHTML = `✅ Puzzel ${currentIndex + 1}`;
          boxes[currentIndex].onclick = null;

          setTimeout(() => {
            closeModal();
            if (solvedCount >= totalRiddles) {
              setTimeout(() => {
                window.location.href = '../Housene/room_2.php';
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
      const hintEl = document.getElementById('hint-text');
      hintEl.textContent = `💡 Hint: ${riddles[currentIndex].hint}`;
      hintEl.style.display = 'block';
    }
  </script>

</body>
</html>