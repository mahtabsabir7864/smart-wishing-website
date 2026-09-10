<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$code = preg_replace('/[^A-Za-z0-9]/', '', (string) ($_GET['code'] ?? ''));
if ($code === '') {
    redirect('create-wish.php');
}
redirect('wish.php?code=' . urlencode($code));
