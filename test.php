<?php
require 'db.php';

$status  = 'success';
$message = 'Connected successfully!';
$tables  = [];
$error   = '';

if (!$conn) {
    $status  = 'error';
    $message = 'Connection failed: ' . mysqli_connect_error();
    $error   = mysqli_connect_error();
} else {
    // Fetch all tables in the database
    $result = mysqli_query($conn, "SHOW TABLES");
    if ($result) {
        while ($row = mysqli_fetch_array($result)) {
            $tables[] = $row[0];
        }
    }
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PawDiary - DB Connection Test</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #fff0f6 0%, #fdf4fb 50%, #f0e8ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        .card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 20px 60px rgba(214,51,132,0.12);
            border: 1.5px solid #f0d6f5;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo { font-size: 52px; margin-bottom: 10px; }

        h1 {
            font-size: 22px;
            font-weight: 800;
            color: #d63384;
            margin-bottom: 4px;
        }

        .subtitle {
            font-size: 13px;
            color: #aaa;
            font-weight: 500;
        }

        /* ── STATUS BANNER ── */
        .status-banner {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 20px;
            border-radius: 16px;
            margin-bottom: 24px;
            font-weight: 700;
            font-size: 15px;
        }

        .status-banner.success {
            background: linear-gradient(135deg, #e8fff0, #d0f8e4);
            color: #1a7a45;
            border: 2px solid #27ae60;
        }

        .status-banner.error {
            background: linear-gradient(135deg, #fff0f0, #fde2e2);
            color: #c0392b;
            border: 2px solid #e74c3c;
        }

        .status-icon { font-size: 28px; }

        .status-text .label { font-size: 13px; font-weight: 600; opacity: 0.75; margin-bottom: 2px; }
        .status-text .value { font-size: 15px; font-weight: 800; }

        /* ── INFO ROWS ── */
        .info-section {
            background: #fdf4fb;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .info-section h3 {
            font-size: 13px;
            font-weight: 700;
            color: #d63384;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 14px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0e0f5;
            font-size: 14px;
        }

        .info-row:last-child { border-bottom: none; }

        .info-label { color: #888; font-weight: 600; }
        .info-value { color: #333; font-weight: 700; font-family: monospace; font-size: 13px; }
        .info-value.ok   { color: #27ae60; }
        .info-value.fail { color: #e74c3c; }

        /* ── TABLES LIST ── */
        .table-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 4px;
        }

        .table-badge {
            background: #fde8f4;
            color: #d63384;
            font-size: 12px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 20px;
            border: 1.5px solid #f5b8d8;
        }

        .no-tables {
            color: #e67e22;
            font-size: 13px;
            font-weight: 600;
        }

        /* ── ERROR BOX ── */
        .error-box {
            background: #fff5f5;
            border: 1.5px solid #f5b8b8;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 13px;
            color: #c0392b;
            font-family: monospace;
            margin-bottom: 20px;
            word-break: break-all;
        }

        /* ── FOOTER ── */
        .footer {
            text-align: center;
            font-size: 12px;
            color: #ccc;
            margin-top: 10px;
        }

        .footer a {
            color: #d63384;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="card">

    <!-- HEADER -->
    <div class="header">
        <div class="logo">🐾</div>
        <h1>PawDiary — DB Test</h1>
        <p class="subtitle">MySQL Connection Checker</p>
    </div>

    <!-- STATUS BANNER -->
    <div class="status-banner <?= $status ?>">
        <span class="status-icon"><?= $status === 'success' ? '✅' : '❌' ?></span>
        <div class="status-text">
            <div class="label">Connection Status</div>
            <div class="value"><?= htmlspecialchars($message) ?></div>
        </div>
    </div>

    <!-- ERROR DETAIL -->
    <?php if ($status === 'error' && $error): ?>
    <div class="error-box">
        ⚠️ <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- CONNECTION INFO -->
    <div class="info-section">
        <h3>🔌 Connection Details</h3>

        <div class="info-row">
            <span class="info-label">Host</span>
            <span class="info-value">localhost</span>
        </div>
        <div class="info-row">
            <span class="info-label">Database</span>
            <span class="info-value">pawdiary_db</span>
        </div>
        <div class="info-row">
            <span class="info-label">Username</span>
            <span class="info-value">root</span>
        </div>
        <div class="info-row">
            <span class="info-label">PHP Version</span>
            <span class="info-value"><?= phpversion() ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">MySQLi</span>
            <span class="info-value <?= function_exists('mysqli_connect') ? 'ok' : 'fail' ?>">
                <?= function_exists('mysqli_connect') ? '✔ Enabled' : '✘ Not available' ?>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Status</span>
            <span class="info-value <?= $status === 'success' ? 'ok' : 'fail' ?>">
                <?= $status === 'success' ? '✔ Connected' : '✘ Failed' ?>
            </span>
        </div>
    </div>

    <!-- TABLES FOUND -->
    <?php if ($status === 'success'): ?>
    <div class="info-section">
        <h3>📋 Tables in pawdiary_db</h3>
        <?php if (!empty($tables)): ?>
            <div class="table-list">
                <?php foreach ($tables as $t): ?>
                    <span class="table-badge">🗂️ <?= htmlspecialchars($t) ?></span>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-tables">⚠️ No tables found. Run <strong>pawdiary.sql</strong> in phpMyAdmin first.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="footer">
        <a href="index.html">← Back to PawDiary</a> &nbsp;|&nbsp;
        Delete this file before going live!
    </div>

</div>

</body>
</html>
