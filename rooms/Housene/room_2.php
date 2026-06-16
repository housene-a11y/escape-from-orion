<?php

session_start();
if (!isset($_SESSION['start_time'])) {
$_SESSION['start_time'] = time();
}

require_once '../../dbcon.php';


try {
    $stmt = $db_connection->prepare("SELECT * FROM riddles WHERE roomId = ?");
    $stmt->execute([2]);
    $riddles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Databasefout: " . $e->getMessage());
}

$teamName = $_SESSION['team_name'] ?? 'Team Orion';
$totalRiddles = count($riddles);

if (!isset($_SESSION['room2_solved'])) {
    $_SESSION['room2_solved'] = [];
}

if (!isset($_SESSION['room2_start'])) {
    $_SESSION['room2_start'] = time();
}

$timeLimit = 600;

// Koppel hier jouw afbeeldingen aan de puzzels (Zorg dat deze bestanden in je 'img' map staan)
$images = [
    '../../img/mars.jpg',      // Voor puzzel 1: "Welke planeet zie je?"
    '../../img/radio.jpg',     // Voor puzzel 2: "Welke frequentie staat ingesteld?"
    '../../img/paneel2.jpg'    // Voor puzzel 3: "Hoeveel knoppen zie je?"
];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamer 2 - Noodcapsule</title>
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        /* Extra styling voor de afbeelding en knoppen in de modal */
        .modal-image {
            max-width: 100%;
            max-height: 250px;
            border-radius: 8px;
            display: none; /* Standaard verborgen tot er een foto is */
            margin: 0 auto 15px auto;
            border: 2px solid #4fc3f7;
        }
        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 15px;
        }
        .hint-btn {
            background-color: #ff9800;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
        }
        .hint-btn:hover {
            background-color: #f57c00;
        }
        .hint-text {
            color: #ffb74d;
            margin-top: 15px;
            font-style: italic;
            display: none;
        }
    </style>
</head>
<body>

<div class="room-header">
    <h1>🚀 Kamer 2 - Noodcapsule</h1>
    <p class="team-label">Team: <strong><?php echo htmlspecialchars($teamName); ?></strong></p>
    <div class="timer-box">
        ⏱️ Tijd over: <span id="timer">10:00</span>
    </div>
    <p class="progress-label">Puzzels oplossen om de ontsnappingscapsule te starten.</p>
</div>

<div class="container">
    <?php foreach ($riddles as $index => $riddle): 
        // Haal de afbeelding en hint op (als er geen hint in de DB staat, geven we een standaard tekst)
        $img = $images[$index] ?? '';
        $hint = $riddle['hint'] ?? 'Kijk heel goed naar de details op de afbeelding!';
    ?>
    <div
        class="box box<?php echo $index + 1; ?>"
        onclick="openModal(<?php echo $index; ?>)"
        data-index="<?php echo $index; ?>"
        data-riddle="<?php echo htmlspecialchars($riddle['riddle']); ?>"
        data-answer="<?php echo strtolower(htmlspecialchars($riddle['answer'])); ?>"
        data-img="<?php echo $img; ?>"
        data-hint="<?php echo htmlspecialchars($hint); ?>"
    >
        🔒 Puzzel <?php echo $index + 1; ?>
    </div>
    <?php endforeach; ?>
</div>

<section class="overlay" id="overlay" onclick="closeModal()"></section>

<section class="modal" id="modal">
    <h2>🚀 Escape Room Vraag</h2>
    
    <img id="modal-img" class="modal-image" src="" alt="Puzzel Afbeelding">
    
    <p id="riddle"></p>
    
    <input type="text" id="answer" placeholder="Typ je antwoord..." onkeydown="if(event.key==='Enter') checkAnswer()">
    
    <div class="modal-buttons">
        <button onclick="checkAnswer()">Verzenden</button>
        <button class="hint-btn" onclick="showHint()">💡 Hint</button>
    </div>
    
    <p id="hint-text" class="hint-text"></p>
    <p id="feedback"></p>
</section>

<script>
const totalRiddles = <?php echo $totalRiddles; ?>;
let solved = 0;

function openModal(index){
    let box = document.querySelector(`.box[data-index='${index}']`);
    
    // Zet de tekst en data goed
    document.getElementById('riddle').innerText = box.dataset.riddle;
    document.getElementById('modal').dataset.answer = box.dataset.answer;
    document.getElementById('modal').dataset.index = index;
    document.getElementById('modal').dataset.hint = box.dataset.hint;
    
    // Check of er een afbeelding is voor deze puzzel
    let imgSrc = box.dataset.img;
    let imgEl = document.getElementById('modal-img');
    if(imgSrc && imgSrc !== '') {
        imgEl.src = imgSrc;
        imgEl.style.display = 'block'; // Laat de afbeelding zien
    } else {
        imgEl.style.display = 'none'; // Verberg als er geen afbeelding is
    }

    // Reset velden
    document.getElementById('answer').value = '';
    document.getElementById('feedback').innerText = '';
    document.getElementById('hint-text').style.display = 'none';

    document.getElementById('overlay').style.display = 'block';
    document.getElementById('modal').style.display = 'block';
    document.getElementById('answer').focus();
}

function closeModal(){
    document.getElementById('overlay').style.display = 'none';
    document.getElementById('modal').style.display = 'none';
}

function showHint() {
    let hintEl = document.getElementById('hint-text');
    let hintText = document.getElementById('modal').dataset.hint;
    hintEl.innerText = "💡 Hint: " + hintText;
    hintEl.style.display = 'block';
}

function checkAnswer(){
    let userAnswer = document.getElementById('answer').value.trim().toLowerCase();
    let correctAnswer = document.getElementById('modal').dataset.answer.toLowerCase();
    let feedback = document.getElementById('feedback');

    if(userAnswer === correctAnswer){
        feedback.innerText = '✅ Correct!';
        feedback.style.color = 'lime';

        let index = document.getElementById('modal').dataset.index;
        let box = document.querySelector(`.box[data-index='${index}']`);

        box.innerHTML = '✅ Opgelost';
        box.style.pointerEvents = 'none';
        box.style.opacity = '0.7';

        solved++;

        setTimeout(() => {
            closeModal();
            if(solved >= totalRiddles){
                window.location.href='../../win.php';
            }
        }, 1000);
    } else {
        feedback.innerText = '❌ Fout antwoord. Probeer het opnieuw.';
        feedback.style.color = 'red';
    }
}

// Timer logica
let seconds = 600;
function updateTimer(){
    seconds--;
    let m = String(Math.floor(seconds / 60)).padStart(2,'0');
    let s = String(seconds % 60).padStart(2,'0');
    document.getElementById('timer').innerText = m + ':' + s;

    if(seconds <= 0){
        window.location.href='../../verlies.php';
    }
}
setInterval(updateTimer, 1000);
</script>

</body>
</html>