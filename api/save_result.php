<?php
/**
 * Uložení výsledku hry (1p / 2p lokálně).
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

$playerName = trim((string) ($in['player_name'] ?? ''));
$mode = (string) ($in['mode'] ?? '1p');
$won = !empty($in['won']);
$attempts = (int) ($in['attempts'] ?? 0);
$difficulty = isset($in['difficulty']) ? trim((string) $in['difficulty']) : null;
$gameCode = isset($in['game_code']) ? trim((string) $in['game_code']) : null;

if ($playerName === '' || $attempts < 1) {
    jsonError('Chybí player_name nebo attempts');
    return;
}

if (!in_array($mode, ['1p', '2p', 'online'], true)) {
    jsonError('Neplatný mode');
    return;
}

try {
    $pdo = getDb();
    $stmt = $pdo->prepare(
        'INSERT INTO results (player_name, mode, won, attempts, difficulty, game_code) VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$playerName, $mode, $won ? 1 : 0, $attempts, $difficulty, $gameCode]);
    $id = (int) $pdo->lastInsertId();
    jsonResponse(['ok' => true, 'id' => $id]);
} catch (Throwable $e) {
    jsonError('Chyba DB: ' . $e->getMessage(), 500);
}
