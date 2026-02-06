<?php
/**
 * Připojení druhého hráče k online hře (podle game_code).
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
$player2Name = trim((string) ($in['player2_name'] ?? ''));
if ($gameCode === '' || $player2Name === '') {
    jsonError('Chybí game_code nebo player2_name');
    return;
}

try {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT id, game_code, player1_name, player2_name, status FROM games WHERE game_code = ? AND status IN (\'waiting\', \'secret_entered\', \'playing\')');
    $stmt->execute([$gameCode]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        jsonError('Hra nenalezena nebo již skončila');
        return;
    }
    if ($row['player2_name'] !== null && $row['player2_name'] !== '') {
        jsonResponse([
            'ok'           => true,
            'game_id'      => (int) $row['id'],
            'game_code'    => $row['game_code'],
            'player1_name' => $row['player1_name'],
            'player2_name' => $row['player2_name'],
            'status'       => $row['status'],
            'joined'       => true,
        ]);
        return;
    }
    $up = $pdo->prepare('UPDATE games SET player2_name = ?, updated_at = NOW() WHERE id = ?');
    $up->execute([$player2Name, $row['id']]);
    jsonResponse([
        'ok'           => true,
        'game_id'      => (int) $row['id'],
        'game_code'    => $row['game_code'],
        'player1_name' => $row['player1_name'],
        'player2_name' => $player2Name,
        'status'       => $row['status'],
        'joined'       => true,
    ]);
} catch (Throwable $e) {
    jsonError('Chyba DB: ' . $e->getMessage(), 500);
}
