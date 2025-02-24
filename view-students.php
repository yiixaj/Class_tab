<?php 
session_start();

if (!isset($_SESSION['username'])): 
    header('Location: login.php');     
else :
    include("config/db.php");
    include("include/header.php");

    $username = $_SESSION['username'];

    // Obtener el user_id del maestro desde la tabla users
    $sql_user = "SELECT id FROM users WHERE username = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("s", $username);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows > 0) {
        $user_data = $result_user->fetch_assoc();
        $teacher_user_id = $user_data['id'];

        // Consultar el access_code y subject_id del maestro desde la tabla teachers
        $sql_access_code = "SELECT access_code, subject_id FROM teachers WHERE user_id = ?";
        $stmt_access_code = $conn->prepare($sql_access_code);
        $stmt_access_code->bind_param("i", $teacher_user_id);
        $stmt_access_code->execute();
        $result_access_code = $stmt_access_code->get_result();
    }

    // Procesar la actualización de materia
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject'])) {
        $new_subject_id = $_POST['subject'];
        if (!empty($new_subject_id)) {
            // Actualizar la materia seleccionada
            $sql_update_subject = "UPDATE teachers SET subject_id = ? WHERE user_id = ?";
            $stmt_update_subject = $conn->prepare($sql_update_subject);
            $stmt_update_subject->bind_param("ii", $new_subject_id, $teacher_user_id);
            if ($stmt_update_subject->execute()) {
                // Redirigir para reflejar el cambio inmediatamente
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
    }

    // Procesar aceptación de estudiante
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_student'])) {
        $student_user_id = $_POST['student_id'];
        
        $sql_accept_student = "UPDATE teacher_students ts
                                JOIN students s ON ts.student_id = s.id
                                JOIN teachers t ON ts.teacher_id = t.id
                                SET ts.status = 'accepted'
                                WHERE s.user_id = ? AND t.user_id = ?";
        $stmt_accept_student = $conn->prepare($sql_accept_student);
        $stmt_accept_student->bind_param("ii", $student_user_id, $teacher_user_id);
        
        if ($stmt_accept_student->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    // Procesar eliminación de estudiante
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_student'])) {
        $student_user_id = $_POST['student_id'];
        
        $sql_remove_student = "DELETE ts FROM teacher_students ts
                                JOIN students s ON ts.student_id = s.id
                                JOIN teachers t ON ts.teacher_id = t.id
                                WHERE s.user_id = ? AND t.user_id = ?";
        $stmt_remove_student = $conn->prepare($sql_remove_student);
        $stmt_remove_student->bind_param("ii", $student_user_id, $teacher_user_id);
        
        if ($stmt_remove_student->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
?>
<style>
/* Estilo general */
.container {
    width: 80%;
    margin: 0 auto;
    padding: 20px;
}

h1, h2 {
    text-align: center;
    color: #333;
}

/* Estilo para la tabla de estudiantes */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

th, td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: center;
}

th {
    background-color: #007bff;
    color: white;
    font-size: 18px;
}

td {
    font-size: 16px;
}

/* Estilo para el formulario de materia */
#subject-container {
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    margin: 20px 0;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

#subject-container h3 {
    color: #333;
    font-size: 24px;
    margin-bottom: 15px;
    text-align: center;
}

#subject-container label {
    font-size: 18px;
    font-weight: bold;
    display: block;
    margin-bottom: 10px;
}

#subject-container select, 
#subject-container input[type="submit"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    box-sizing: border-box;
}

#subject-container input[type="submit"] {
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 18px;
}

#subject-container input[type="submit"]:hover {
    background-color: #0056b3;
}

/* Nuevos estilos para botones de acción */
.action-btn {
    padding: 5px 10px;
    margin: 0 5px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 14px;
    cursor: pointer;
}

.accept-btn {
    background-color: #28a745;
    color: white;
    border: none;
}

.remove-btn {
    background-color: #dc3545;
    color: white;
    border: none;
}

.text-warning {
    color: #ffc107;
}

.text-success {
    color: #28a745;
}
</style>

<div class="container">
    <h1>Lista de estudiantes</h1>

    <!-- Mostrar el access_code del maestro -->
    <div style="text-align: center; margin-bottom: 20px;">
        <?php
        if (isset($result_access_code) && $result_access_code->num_rows > 0) {
            $row = $result_access_code->fetch_assoc();
            $access_code = $row['access_code'];
            $subject_id = $row['subject_id'];

            echo "<h2>Tu código de acceso: <span style='color: #007bff;'>$access_code</span></h2>";

            // Mostrar la materia seleccionada
            if ($subject_id) {
                $sql_subject = "SELECT name FROM subjects WHERE id = ?";
                $stmt_subject = $conn->prepare($sql_subject);
                $stmt_subject->bind_param("i", $subject_id);
                $stmt_subject->execute();
                $result_subject = $stmt_subject->get_result();

                if ($result_subject->num_rows > 0) {
                    $subject_data = $result_subject->fetch_assoc();
                    $subject_name = $subject_data['name'];
                    echo "<h2>Materia seleccionada: <span style='color: #28a745;'>$subject_name</span></h2>";
                } else {
                    echo "<h2>Materia seleccionada: <span style='color: #dc3545;'>No asignada</span></h2>";
                }
            } else {
                echo "<h2>Materia seleccionada: <span style='color: #dc3545;'>No asignada</span></h2>";
            }
        } else {
            echo "<p>No se encontró un código de acceso asociado a este maestro.</p>";
        }
        ?>
    </div>

    <!-- Formulario para elegir materia -->
    <div id="subject-container">
        <h3>Actualizar Materia</h3>
        <form method="POST" action="">
            <label for="subject">Materia:</label>
            <select name="subject" id="subject">
                <option value="">-- Seleccionar Materia --</option>
                <?php
                // Obtener las materias disponibles desde la tabla subjects
                $stmt_subjects = $conn->prepare("SELECT id, name FROM subjects");
                $stmt_subjects->execute();
                $result_subjects = $stmt_subjects->get_result();
                while ($row = $result_subjects->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                }
                ?>
            </select>
            <input type="submit" value="Actualizar Materia">
        </form>
    </div>

    <!-- Tabla para mostrar estudiantes -->
    <table>
        <thead>
            <tr>
                <th>Nombre de Usuario</th>
                <th>Email</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql_students = "SELECT 
                            u.id as user_id,
                            u.username, 
                            u.email,
                            ts.status
                         FROM users u
                         JOIN students s ON u.id = s.user_id
                         JOIN teacher_students ts ON ts.student_id = s.id
                         JOIN teachers t ON t.id = ts.teacher_id
                         WHERE u.user_role = 1 AND t.user_id = ?";
        $stmt_students = $conn->prepare($sql_students);
        $stmt_students->bind_param("i", $teacher_user_id);
        $stmt_students->execute();
        $result_students = $stmt_students->get_result();

        if ($result_students->num_rows > 0) {
            while ($row = $result_students->fetch_assoc()) {
                $status_class = $row['status'] == 'pending' ? 'text-warning' : 'text-success';
                $status_text = $row['status'] == 'pending' ? 'Pendiente' : 'Aceptado';
                
                echo "<tr>
                        <td>" . htmlspecialchars($row['username']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td class='$status_class'>$status_text</td>
                        <td>";
                
                if ($row['status'] == 'pending') {
                    echo "<form method='POST' style='display:inline;'>
                            <input type='hidden' name='student_id' value='" . $row['user_id'] . "'>
                            <button type='submit' name='accept_student' class='action-btn accept-btn'>Aceptar</button>
                          </form>";
                }
                
                echo "<form method='POST' style='display:inline;'>
                        <input type='hidden' name='student_id' value='" . $row['user_id'] . "'>
                        <button type='submit' name='remove_student' class='action-btn remove-btn'>Eliminar</button>
                      </form>
                    </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No se encontraron estudiantes registrados.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
<?php 
include("include/footer.php");
endif;
?>