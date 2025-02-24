<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" href="./assets/favicon.ico">
    <title>ClassTab</title>
    <link href="https://fonts.googleapis.com/css?family=Varela+Round&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="nav-items">
                <a href="index.php" class="brand">ClassTab</a>
                <ul>
                    <li><a href='index.php'>Hogar</a></li>
                    <li><a href='about.php'>Sobre</a></li>
                    <!-- Botón para activar/desactivar el modo noche -->
                    


                    <!-- Displaying routes depending upon the user session and role -->
                    <?php if (isset($_SESSION['username'])): ?>
                        <?php if ($_SESSION['user_role'] == 'Teacher'): ?>
                            <li><a href='teacher_dashboard.php'>Panel de control</a></li>
                        <?php elseif ($_SESSION['user_role'] == 'Student'): ?>
                            <li><a href='student_dashboard.php'>Panel de control</a></li>
                        <?php endif; ?>
                        <li><a href='logout.php'>Salir</a></li>
                    <?php else: ?>
                        <li><a href='register.php'>Registrar</a></li>
                        <li><a href='login.php'>Iniciar sesión</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </header>