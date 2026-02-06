<?php
/**
 * Odeslání tipu v online hře. Server vrátí feedback (b, w) a aktualizuje history.
 */
require_once __DIR__ . '/db.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Metoda musí být POST', 405);
    return;
}

$raw = file_get_contents('php://input');
$in = json_decode($raw, true);
if (!is_array($in)) {
    jsonError('Neplatné JSON');
    return;
}

$gameCode = isset($in['game_code']) ? strtoupper(trim((string) $in['game_code'])) : '';
$guess = isset($in['guess']) ? $in['guess'] : null;
if ($gameCode === '' || !is_array($guess) || count($guess) !== 4) {
    jsonError('Chybí game_code nebo platný guess (pole 4 čísel)');
    return;
}
$guess = array_map('intval', array_values($guess));

function calcFeedback(array $secret, array $guess): array
{
    $b = 0;
    $w = 0;
    $sR = [];
    $gR = [];
    for ($i = 0; $i < 4; $i++) {
        if ($secret[$i] === $guess[$i]) {
            $b++;
        } else {
            $sR[] = $secret[$i];
            $gR[] = $guess[$i];
        }
    }
    foreach ($gR as $g) {
        $idx = array_search($g, $sR, true);
        if ($idx !== false) {
            $w++;
            array_splice($sR, $idx, 1);
        }
    }
    return ['b' => $b, 'w' => $w];
}

try {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT id, secret, status, history, max_attempts FROM games WHERE game_code = ?');
    $stmt->execute([$gameCode]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        jsonError('Hra nenalezena');
        return;
    }
    if ($row['status'] !== 'secret_entered' && $row['status'] !== 'playing') {
        jsonError('Hra není ve fázi hádání');
        return;
    }
    $secret = json_decode($row['secret'], true);
    $history = $row['history'] !== null ? json_decode($row['history'], true) : [];
    if (count($history) >= (int) $row['max_attempts']) {
        jsonError('Překročen počet pokusů');
        return;
    }
    $fb = calcFeedback($secret, $guess);
    $history[] = ['guess' => $guess, 'fb' => $fb];
    $newStatus = $row['status'] === 'secret_entered' ? 'playing' : $row['status'];
    if ($fb['b'] === 4) {
        $newStatus = 'won';
    } elseif (count($history) >= (int) $row['max_attempts']) {
        $newStatus = 'lost';
    }
    $up = $pdo->prepare('UPDATE games SET history = ?, status = ?, updated_at = NOW() WHERE id = ?');
    $up->execute([json_encode($history), $newStatus, $row['id']]);
    $out = [
        'ok'       => true,
        'feedback' => $fb,
        'history'  => $history,
        'status'   => $newStatus,
        'won'      => $newStatus === 'won',
        'lost'     => $newStatus === 'lost',
    ];
    if ($newStatus === 'won' || $newStatus === 'lost') {
        $out['secret'] = $secret;
    }
    jsonResponse($out);
} catch (Throwable $e) {
    jsonError('Chyba DB: ' . $e->getMessage(), 500);
}
