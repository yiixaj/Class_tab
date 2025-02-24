<?php 
session_start();
include("config/db.php");

// Verificar si el usuario ya está logueado y redirigir según el rol
if (isset($_SESSION['username'])) {
    if ($_SESSION['user_role'] == 'Student') {
        header('Location: student_dashboard.php');
    } elseif ($_SESSION['user_role'] == 'Teacher') {
        header('Location: teacher_dashboard.php');
    }
    exit();
}

// Verificar si se ha enviado el formulario
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validar que los campos no estén vacíos
    if ($username != '' && $password != '') {
        // Encriptar la contraseña
        $pwd_hash = sha1($password);

        // Buscar al usuario en la base de datos
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$pwd_hash'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['user_role'];
            $_SESSION['id'] = $user['id']; // Almacena el id del usuario en la sesión

            // Si el usuario es un estudiante, obtener el student_id
            if ($user['user_role'] == 'Student') {
                $student_sql = "SELECT id FROM students WHERE user_id = ?";
                $stmt = $conn->prepare($student_sql);
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();
                $student_result = $stmt->get_result();

                if ($student_result->num_rows > 0) {
                    $student = $student_result->fetch_assoc();
                    $_SESSION['student_id'] = $student['id']; // Almacena el student_id en la sesión
                }

                header('Location: student_dashboard.php');
                exit();
            } 
            // Si el usuario es un maestro, obtener el teacher_id
            elseif ($user['user_role'] == 'Teacher') {
                $teacher_sql = "SELECT id FROM teachers WHERE user_id = ?";
                $stmt = $conn->prepare($teacher_sql);
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();
                $teacher_result = $stmt->get_result();

                if ($teacher_result->num_rows > 0) {
                    $teacher = $teacher_result->fetch_assoc();
                    $_SESSION['teacher_id'] = $teacher['id']; // Almacena el teacher_id en la sesión
                }

                header('Location: teacher_dashboard.php');
                exit();
            }
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    } else {
        $error = 'Por favor, completa todos los campos.';
    }
}
?>

<?php include("include/header.php"); ?>

<div class="container">
    <div class="forms">
        <div class="card info-box">
            <img id="asset" src="./assets/03.svg" alt="Login Icon">
        </div>
        <div id="data" class="card info-box">
            <h1>Iniciar sesión</h1>
            <form action="login.php" method="POST">
                <input type="text" name="username" placeholder="Nombre de usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <input type="submit" name="login" value="Iniciar sesión">   
                <div class="alert">
                    <?php if (isset($error)) echo $error; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include("include/footer.php"); ?>
