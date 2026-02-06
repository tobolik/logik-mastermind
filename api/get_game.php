<?php
/**
 * Stav online hry (pro polling).
 */
require_once __DIR__ . '/db.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$gameCode = isset($_GET['game_code']) ? strtoupper(trim((string) $_GET['game_code'])) : '';
if ($gameCode === '') {
    jsonError('Chybí game_code');
    return;
}

try {
    $pdo = getDb();
    $stmt = $pdo->prepare(
        'SELECT id, game_code, player1_name, player2_name, secret, status, history, max_attempts, created_at, updated_at FROM games WHERE game_code = ?'
    );
    $stmt->execute([$gameCode]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        jsonError('Hra nenalezena');
        return;
    }
    $secret = $row['secret'] !== null ? json_decode($row['secret'], true) : null;
    $history = $row['history'] !== null ? json_decode($row['history'], true) : [];
    jsonResponse([
        'ok'           => true,
        'game_id'      => (int) $row['id'],
        'game_code'    => $row['game_code'],
        'player1_name' => $row['player1_name'],
        'player2_name' => $row['player2_name'],
        'secret'       => $secret,
        'status'       => $row['status'],
        'history'      => $history,
        'max_attempts' => (int) $row['max_attempts'],
        'updated_at'   => $row['updated_at'],
    ]);
} catch (Throwable $e) {
    jsonError('Chyba DB: ' . $e->getMessage(), 500);
}
