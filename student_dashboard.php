<?php 
session_start();
include("config/db.php");

// Verificar si el usuario está logueado y es un estudiante
if (!isset($_SESSION['username']) || $_SESSION['user_role'] !== 'Student') {
    header('Location: login.php');
    exit();
}

if (!isset($_SESSION['student_id']) || empty($_SESSION['student_id'])) {
    echo "Error: No se pudo verificar la identidad del estudiante. Por favor, vuelve a iniciar sesión.";
    exit();
}

$student_id = intval($_SESSION['student_id']);

// Verificar conexión a la base de datos
if (!$conn) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Procesar el formulario para unirse a una nueva clase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['access_code'])) {
    $access_code = trim($_POST['access_code']);

    // Verificar si el código de acceso pertenece a un maestro existente
    $teacher_query = "
        SELECT id 
        FROM teachers 
        WHERE access_code = ?
    ";
    $teacher_stmt = $conn->prepare($teacher_query);
    $teacher_stmt->bind_param("s", $access_code);
    $teacher_stmt->execute();
    $teacher_result = $teacher_stmt->get_result();

    if ($teacher_result && $teacher_result->num_rows > 0) {
        $teacher = $teacher_result->fetch_assoc();
        $teacher_id = $teacher['id'];

        // Verificar si ya está registrado con este maestro
        $check_query = "
            SELECT id 
            FROM teacher_students 
            WHERE student_id = ? AND teacher_id = ?
        ";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("ii", $student_id, $teacher_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result && $check_result->num_rows > 0) {
            $join_class_error = "Ya estás registrado en esta clase.";
        } else {
            // Registrar al estudiante con el maestro
            $insert_query = "
                INSERT INTO teacher_students (teacher_id, student_id, joined_at) 
                VALUES (?, ?, NOW())
            ";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("ii", $teacher_id, $student_id);

            if ($insert_stmt->execute()) {
                $join_class_success = "Te has unido a la clase exitosamente.";
                // Redirigir para refrescar la página y mostrar los cambios
                header("Refresh:0");
                exit();
            } else {
                $join_class_error = "Error al unirte a la clase. Por favor, intenta de nuevo.";
            }
        }
    } else {
        $join_class_error = "El código de acceso ingresado no es válido. Por favor, verifica e intenta de nuevo.";
    }
}

// Procesar la solicitud para salir de una clase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leave_teacher_id'])) {
    $leave_teacher_id = intval($_POST['leave_teacher_id']);

    // Verificar si el estudiante está realmente inscrito con este maestro
    $delete_query = "
        DELETE FROM teacher_students 
        WHERE student_id = ? AND teacher_id = ?
    ";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("ii", $student_id, $leave_teacher_id);

    if ($delete_stmt->execute()) {
        $leave_class_success = "Has salido de la clase exitosamente.";
        // Redirigir para refrescar la página y mostrar los cambios
        header("Refresh:0");
        exit();
    } else {
        $leave_class_error = "Error al salir de la clase. Por favor, intenta de nuevo.";
    }
}

// Consultar los maestros asociados al estudiante
$query = "
    SELECT 
        t.id AS teacher_id, 
        u.username AS teacher_name, 
        u.email AS teacher_email,
        s.name AS subject_name,
        ts.status AS student_status
    FROM teachers t
    INNER JOIN users u ON t.user_id = u.id
    INNER JOIN teacher_students ts ON t.id = ts.teacher_id
    LEFT JOIN subjects s ON t.subject_id = s.id
    WHERE ts.student_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$teachers_result = $stmt->get_result();

include("include/header.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Estudiante</title>
    <style>
        /* Estilos base */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            width: 95%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px;
            box-sizing: border-box;
        }

        h1, h2 {
            color: #333;
            word-wrap: break-word;
            margin-bottom: 1rem;
        }

        /* Alertas mejoradas */
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            width: 100%;
            box-sizing: border-box;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Tabla responsive mejorada */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 20px 0;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        .table {
            width: 100%;
            min-width: 800px;
            background-color: white;
            border-collapse: collapse;
        }

        .table th, 
        .table td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #e9ecef;
        }

        .table th {
            background-color: #007bff;
            color: white;
            font-weight: 600;
            white-space: nowrap;
        }

        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .table tr:hover {
            background-color: #f2f2f2;
        }

        /* Botones mejorados */
        .btn {
            display: inline-block;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
            margin: 2px;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        /* Formulario mejorado */
        .form-inline {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 20px 0;
            align-items: center;
        }

        .form-control {
            flex: 1;
            min-width: 200px;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ced4da;
            box-sizing: border-box;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        .badge {
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #000;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .text-muted {
            color: #6c757d;
        }

        /* Media Queries mejorados */
        @media screen and (max-width: 768px) {
            .container {
                width: 100%;
                padding: 10px;
            }

            .form-inline {
                flex-direction: column;
                gap: 15px;
            }

            .form-control {
                width: 100%;
                min-width: unset;
            }

            .btn {
                width: 100%;
                margin: 5px 0;
            }

            h1 {
                font-size: 24px;
            }

            h2 {
                font-size: 20px;
            }

            .table td, 
            .table th {
                padding: 8px;
                font-size: 14px;
            }

            .btn-sm {
                width: auto;
            }

            /* Mejora para tablas en móviles */
            .table-responsive {
                margin: 10px -10px;
                border-radius: 0;
            }

            .table {
                font-size: 14px;
            }

            /* Ajuste de botones en la tabla para móviles */
            .table td:last-child {
                white-space: nowrap;
                min-width: 120px;
            }
        }

        @media screen and (max-width: 480px) {
            body {
                font-size: 14px;
            }

            .alert {
                padding: 10px;
                margin: 10px 0;
            }

            .badge {
                font-size: 11px;
                padding: 4px 8px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
    <p class="lead">Tu ID de estudiante: <?php echo $student_id; ?></p>

    <h2>Unirse a una Nueva Clase</h2>
    <?php if (isset($join_class_success)): ?>
        <div class="alert alert-success"><?php echo $join_class_success; ?></div>
    <?php endif; ?>
    <?php if (isset($join_class_error)): ?>
        <div class="alert alert-danger"><?php echo $join_class_error; ?></div>
    <?php endif; ?>
    <form method="POST" action="" class="form-inline">
        <input type="text" name="access_code" id="access_code" class="form-control" placeholder="Código de Acceso" required>
        <button type="submit" class="btn btn-success">Unirse</button>
    </form>

    <h2>Maestros Registrados</h2>
    <?php if (isset($leave_class_success)): ?>
        <div class="alert alert-success"><?php echo $leave_class_success; ?></div>
    <?php endif; ?>
    <?php if (isset($leave_class_error)): ?>
        <div class="alert alert-danger"><?php echo $leave_class_error; ?></div>
    <?php endif; ?>
    
    <?php if ($teachers_result && $teachers_result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre del Maestro</th>
                        <th>Correo Electrónico</th>
                        <th>Materia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($teacher = $teachers_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($teacher['teacher_name']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['teacher_email']); ?></td>
                            <td><?php echo htmlspecialchars($teacher['subject_name'] ?? 'No asignada'); ?></td>
                            <td>
                                <?php 
                                if ($teacher['student_status'] == 'pending') {
                                    echo '<span class="badge badge-warning">Pendiente</span>';
                                } elseif ($teacher['student_status'] == 'accepted') {
                                    echo '<span class="badge badge-success">Aceptado</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($teacher['student_status'] == 'accepted'): ?>
                                    <a href="teacher_activities.php?teacher_id=<?php echo $teacher['teacher_id']; ?>" class="btn btn-primary btn-sm">Ver Actividades</a>
                                <?php else: ?>
                                    <span class="text-muted">Esperando aprobación</span>
                                <?php endif; ?>
                                
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="leave_teacher_id" value="<?php echo $teacher['teacher_id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres salir de esta clase?');">Salir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No estás registrado con ningún maestro. Por favor, únete a una clase para continuar.</p>
    <?php endif; ?>