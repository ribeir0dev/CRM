<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard | FlowDesk' ?></title>

    <link rel="icon" href="/flowdesk_novo/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.lineicons.com/5.0/lineicons.css" />
    <link rel="stylesheet" href="/flowdesk_novo/assets/css/login.css">
    <link rel="stylesheet" href="/flowdesk_novo/assets/css/painel.css">
</head>
<body class="bg-dark-subtle">

<!-- Topbar mobile -->
<div class="d-flex align-items-center justify-content-between bg-primary px-3 py-2 d-lg-none" id="mobileTopbar">
    <div class="d-flex align-items-center">
        <img src="/flowdesk_novo/assets/img/icon.png" alt="Logo" width="28" class="me-2">
        <span class="fw-bold text-light">FlowDesk</span>
    </div>
    <button class="btn btn-light" id="menuToggle" aria-label="Abrir menu">
        <i class="bi bi-list" style="font-size: 1.4rem; color: #2176fa;"></i>
    </button>
</div>
