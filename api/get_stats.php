<?php
/**
 * Statistiky hráče (z DB). Volitelně filtr player_name.
 */
require_once __DIR__ . '/db.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$playerName = isset($_GET['player_name']) ? trim((string) $_GET['player_name']) : null;

try {
    $pdo = getDb();
    if ($playerName !== null && $playerName !== '') {
        $stmt = $pdo->prepare(
            'SELECT id, player_name, mode, won, attempts, difficulty, game_code, created_at FROM results WHERE player_name = ? ORDER BY created_at DESC LIMIT 200'
        );
        $stmt->execute([$playerName]);
    } else {
        $stmt = $pdo->query(
            'SELECT id, player_name, mode, won, attempts, difficulty, game_code, created_at FROM results ORDER BY created_at DESC LIMIT 200'
        );
    }
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stats = [
        'total'   => count($rows),
        'wins'    => count(array_filter($rows, fn($r) => (int)$r['won'] === 1)),
        'best'    => null,
        'avg'     => null,
        'items'   => $rows,
    ];
    $wins = array_filter($rows, fn($r) => (int)$r['won'] === 1);
    if (count($wins) > 0) {
        $stats['best'] = (int) min(array_column($wins, 'attempts'));
        $stats['avg'] = round(array_sum(array_column($wins, 'attempts')) / count($wins), 1);
    }
    jsonResponse(['ok' => true, 'stats' => $stats]);
} catch (Throwable $e) {
    jsonError('Chyba DB: ' . $e->getMessage(), 500);
}
