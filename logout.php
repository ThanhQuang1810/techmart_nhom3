<?php
require_once __DIR__ . '/config.php';
unset($_SESSION['user_id']);
set_flash('success', 'Bạn đã đăng xuất.');
redirect('index.php');
