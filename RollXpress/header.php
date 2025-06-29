<?php
// header.php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

$page_title = $page_title ?? "RollXpress";
$pagina_actual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - RollXpress</title>
    <meta name="description" content="El mejor sushi a domicilio en La Cisterna. Ingredientes frescos y sabores únicos.">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    
    <style>body {background-color: #1a1a1a;}</style>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Noto+Sans+JP:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/stylesnav.css?v=1.2">
    <link rel="stylesheet" href="css/stylespersonalizado.css">
    <link rel="stylesheet" href="css/styles.css?v=2.6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="is-loading <?php if (isset($_SESSION['user_id'])) echo 'logged-in'; ?>">
    <?php include_once 'nav.php'; ?>