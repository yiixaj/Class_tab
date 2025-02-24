<?php 
    session_start();
    include("config/db.php"); // Conectar a la base de datos

    // Función para generar un código único
    function generateAccessCode($length = 8) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $charactersLength = strlen($characters);
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, $charactersLength - 1)];
        }
        return $code;
    }

    // Verificar si se envió el formulario
    if (isset($_POST['register'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user_role = $_POST['user_role']; // Puede ser 'Student' o 'Teacher'

        // Validar que los campos no estén vacíos
        if ($username != '' && $email != '' && $password != '') {
            // Encriptar la contraseña
            $pwd_hash = sha1($password);

            // Verificar si el usuario ya existe
            $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                $error = 'El nombre de usuario o el correo electrónico ya están registrados.';
            } else {
                // Insertar en la tabla 'users'
                $sql = "INSERT INTO users (username, email, password, user_role) VALUES ('$username', '$email', '$pwd_hash', '$user_role')";
                $query = $conn->query($sql);

                if ($query) {
                    // Obtener el ID del usuario recién creado
                    $user_id = $conn->insert_id;

                    // Insertar en la tabla correspondiente (students o teachers)
                    if ($user_role == 'Student') {
                        $sql_student = "INSERT INTO students (user_id) VALUES ('$user_id')";
                        $conn->query($sql_student);
                    } elseif ($user_role == 'Teacher') {
                        // Generar un código único para el maestro
                        $access_code = generateAccessCode();

                        // Verificar que el código sea único
                        $is_unique = false;
                        while (!$is_unique) {
                            $check_code_sql = "SELECT * FROM teachers WHERE access_code = '$access_code'";
                            $check_code_result = $conn->query($check_code_sql);
                            if ($check_code_result->num_rows == 0) {
                                $is_unique = true;
                            } else {
                                $access_code = generateAccessCode(); // Generar un nuevo código si no es único
                            }
                        }

                        // Insertar al maestro en la tabla 'teachers' con su código
                        $sql_teacher = "INSERT INTO teachers (user_id, access_code) VALUES ('$user_id', '$access_code')";
                        $conn->query($sql_teacher);
                    }

                    // Redirigir a la página de login
                    header('Location: login.php');
                    exit();
                } else {
                    $error = 'Hubo un error al registrar el usuario. Intenta nuevamente.';
                }
            }
        } else {
            $error = 'Por favor, completa todos los campos.';
        }
    }
?>

<?php 
    if (isset($_SESSION['username'])): 
        header('Location: dashboard.php');        
    else:
        include("include/header.php");
?>
    <div class="container">
        <div class="forms">
            <div class="info-box">
                <img id="asset" src="./assets/03.svg" alt="">
            </div>
            <div id="data" class="info-box">
                <h1>Crear una cuenta</h1>
                <form action="register.php" method="POST">
                    <div class="input-group">
                        <div class="input-wrapper">
                            <input type="text" name="username" placeholder="Nombre de usuario" required>
                            <i class="icon-user"></i>
                        </div>
                        <div class="input-wrapper">
                            <input type="email" name="email" placeholder="Correo electrónico" required>
                            <i class="icon-email"></i>
                        </div>
                        <div class="input-wrapper">
                            <input type="password" name="password" placeholder="Contraseña" required>
                            <i class="icon-password"></i>
                        </div>
                    </div>
                    
                    <div class="role-selection">
                        <div class="role-option">
                            <input type="radio" id="student" name="user_role" value="Student" required>
                            <label for="student">
                                <i class="icon-student"></i>
                                Estudiante
                            </label>
                        </div>
                        <div class="role-option">
                            <input type="radio" id="teacher" name="user_role" value="Teacher" required>
                            <label for="teacher">
                                <i class="icon-teacher"></i>
                                Maestro
                            </label>
                        </div>
                    </div>

                    <input type="submit" name="register" value="Registrarse">   

                    <div class="alert">
                        <?php 
                            if (isset($error)) {
                                echo $error;
                            }
                        ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php 
    include("include/footer.php");
    endif
?>

<style>
.input-group {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 20px;
}

.input-wrapper {
    position: relative;
}

.input-wrapper input {
    width: 100%;
    padding: 15px 15px 15px 40px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.input-wrapper i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #888;
    font-size: 20px;
}

.input-wrapper input:focus {
    outline: none;
    border-color: #ff4200;
    box-shadow: 0 0 5px rgba(255,66,0,0.3);
}

.input-wrapper input:focus + i {
    color: #ff4200;
}

.role-selection {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}

.role-option {
    flex: 1;
    margin: 0 10px;
    position: relative;
}

.role-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.role-option label {
    display: block;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 15px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.role-option label i {
    display: block;
    margin-bottom: 10px;
    font-size: 30px;
}

.role-option input[type="radio"]:checked + label {
    background-color: #ff4200;
    color: white;
    border-color: #ff4200;
}

.role-option input[type="radio"]:checked + label i {
    color: white;
}

/* Estilos para el botón de envío */
input[type="submit"] {
    width: 100%;
    padding: 15px;
    background-color: #ff4200;
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

input[type="submit"]:hover {
    background-color: #cc3500;
}
</style>