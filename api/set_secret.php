<?php
/**
 * Zadání tajného kódu (hráč 1 po vytvoření hry).
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
$secret = isset($in['secret']) ? $in['secret'] : null;
if ($gameCode === '' || !is_array($secret) || count($secret) !== 4) {
    jsonError('Chybí game_code nebo platný secret (pole 4 čísel)');
    return;
}
foreach ($secret as $v) {
    if (!is_int($v) && !ctype_digit((string)$v)) {
        jsonError('Secret musí být pole celých čísel 0–5');
        return;
    }
}
$secret = array_map('intval', array_values($secret));

try {
    $pdo = getDb();
    $stmt = $pdo->prepare('SELECT id, status FROM games WHERE game_code = ?');
    $stmt->execute([$gameCode]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        jsonError('Hra nenalezena');
        return;
    }
    if ($row['status'] !== 'waiting' && $row['status'] !== 'secret_entered') {
        jsonError('Tajný kód již byl zadán');
        return;
    }
    $up = $pdo->prepare('UPDATE games SET secret = ?, status = \'secret_entered\', updated_at = NOW() WHERE id = ?');
    $up->execute([json_encode($secret), $row['id']]);
    jsonResponse(['ok' => true, 'status' => 'secret_entered']);
} catch (Throwable $e) {
    jsonError('Chyba DB: ' . $e->getMessage(), 500);
}
