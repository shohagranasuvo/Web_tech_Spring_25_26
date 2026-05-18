<?php
// dashboard/employer-applications.php  (public entry point)
// URL: /dashboard/employer-applications.php

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../controllers/DashboardController.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$controller = new DashboardController();
$controller->employerDashboard();
