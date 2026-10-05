<?php
/**
 * Production MySQL Database Optimization & Cleanup Script
 * 
 * Safely applies the audited database cleanup and indexing to the Hostinger live MySQL database.
 * NOTE: Full backup is already preserved at: backups/production_live_backup_*.sql
 */

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: 3308; // Connected via SSH tunnel
$db   = getenv('DB_DATABASE') ?: 'u841409365_french';
$user = getenv('DB_USERNAME') ?: 'u841409365_admin';
$pass = getenv('DB_PASSWORD') ?: (file_exists(__DIR__ . '/../.env') ? (preg_match('/DB_PASSWORD=(.*)/', file_get_contents(__DIR__ . '/../.env'), $m) ? trim($m[1], " \t\n\r\0\x0B\"'") : '') : '');

echo "=== Connecting to Hostinger Production MySQL via SSH Tunnel (port {$port}) ===\n";

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "✓ Connected successfully.\n\n";
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

// 1. Purge Orphaned Permissions
echo "1. Cleaning orphaned student_content_permissions...\n";
$delFolders = $pdo->exec("DELETE FROM student_content_permissions WHERE content_type='folder' AND content_id NOT IN (SELECT id FROM course_folders)");
$delTests   = $pdo->exec("DELETE FROM student_content_permissions WHERE content_type='test' AND content_id NOT IN (SELECT id FROM tests)");
$delFiles   = $pdo->exec("DELETE FROM student_content_permissions WHERE content_type='file' AND content_id NOT IN (SELECT id FROM course_files)");
echo "✓ Removed {$delFolders} orphaned folder permissions, {$delTests} test permissions, {$delFiles} file permissions.\n\n";

// 2. Drop dead ghost tables
$deadTables = ['settings', 'admin_logs', 'test_drag_drop_items', 'user_achievements', 'achievements'];
echo "2. Purging empty / dead ghost tables...\n";
foreach ($deadTables as $table) {
    $count = $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("DROP TABLE IF EXISTS `{$table}`");
        echo "✓ Dropped dead table: `{$table}`\n";
    } else {
        echo "! Skipped `{$table}` because it contains {$count} rows\n";
    }
}
echo "\n";

// 3. Add High-Performance Compound Indexes (if not already existing)
echo "3. Creating performance indexes...\n";

function addIndexIfNotExists(PDO $pdo, string $table, string $indexName, string $columns, bool $unique = false) {
    $check = $pdo->query("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$indexName}'")->fetchAll();
    if (empty($check)) {
        $type = $unique ? "UNIQUE INDEX" : "INDEX";
        $pdo->exec("ALTER TABLE `{$table}` ADD {$type} `{$indexName}` ({$columns})");
        echo "✓ Added index `{$indexName}` on `{$table}`\n";
    } else {
        echo "- Index `{$indexName}` already exists on `{$table}`\n";
    }
}

addIndexIfNotExists($pdo, 'student_content_permissions', 'idx_perm_student_course_content', 'student_id, course_id, content_type, content_id', true);
addIndexIfNotExists($pdo, 'student_content_permissions', 'idx_perm_student_type_id', 'student_id, content_type, content_id');
addIndexIfNotExists($pdo, 'test_questions', 'idx_questions_test_order', 'test_id, `order`');
addIndexIfNotExists($pdo, 'test_questions', 'idx_questions_type', 'type');
addIndexIfNotExists($pdo, 'student_test_attempts', 'idx_attempts_student_test', 'student_id, test_id');
addIndexIfNotExists($pdo, 'student_daily_topics', 'idx_daily_topics_status', 'student_daily_status_id');
addIndexIfNotExists($pdo, 'lessons', 'idx_lessons_course_folder', 'course_id, folder_id');
addIndexIfNotExists($pdo, 'tests', 'idx_tests_course_folder', 'course_id, folder_id');
addIndexIfNotExists($pdo, 'enrollments', 'idx_enrollments_user_course', 'user_id, course_id');

echo "\n=======================================================\n";
echo "✓ Production Database Optimization Completed Successfully!\n";
echo "=======================================================\n";
