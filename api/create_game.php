<?php
/**
 * Vytvoření nové online hry. Vrátí game_code pro sdílení.
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

$player1Name = trim((string) ($in['player1_name'] ?? ''));
if ($player1Name === '') {
    jsonError('Chybí player1_name');
    return;
}

function randomCode(): string
{
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}

try {
    $pdo = getDb();
    for ($attempt = 0; $attempt < 10; $attempt++) {
        $gameCode = randomCode();
        $stmt = $pdo->prepare(
            'INSERT INTO games (game_code, player1_name, status, max_attempts) VALUES (?, ?, ?, ?)'
        );
        try {
            $stmt->execute([$gameCode, $player1Name, 'waiting', 10]);
            $id = (int) $pdo->lastInsertId();
            jsonResponse([
                'ok'         => true,
                'game_id'    => $id,
                'game_code'  => $gameCode,
                'join_url'   => '?online=join&code=' . $gameCode,
            ]);
            return;
        } catch (PDOException $e) {
            if ($e->getCode() !== '23000') {
                throw $e;
            }
        }
    }
    jsonError('Nepodařilo se vygenerovat unikátní kód', 500);
} catch (Throwable $e) {
    jsonError('Chyba DB: ' . $e->getMessage(), 500);
}
