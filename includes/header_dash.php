<!-- includes/header.php -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle ?? 'FlowDesk CRM'; ?></title>
    <link rel="stylesheet" href="/flowdesk/assets/css/style.css">
    <link rel="stylesheet" href="/flowdesk/assets/css/dashboard.css">
    <script src="/flowdesk/assets/js/scripts.js"></script>
    <link rel="icon" href="/flowdesk/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<main>
