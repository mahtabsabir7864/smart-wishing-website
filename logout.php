<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
logout_user();
session_regenerate_id(true);
redirect('index.php');
