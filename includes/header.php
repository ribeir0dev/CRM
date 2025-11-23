<!-- includes/header.php -->
<?php if(session_status() !== PHP_SESSION_ACTIVE) session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle ?? 'FlowDesk CRM'; ?></title>
    <link rel="stylesheet" href="/flowdesk/assets/css/style.css">
    <link rel="stylesheet" href="/flowdesk/assets/css/dashboard.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/flowdesk/favicon.ico">
</head>
<body>
<header class="header">
    <div class="header-left">
        <img class="logo-icon" src="/flowdesk/assets/images/icon.png" alt="Logo" />
        <span class="logo-text">FlowDesk</span>
        <span class="logo-alt-text">Sistema de gerenciamento CRM Interno</span>
    </div>
</header>
<main>
