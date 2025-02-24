<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$user_role = $_SESSION['user_role']; 

// Consultar datos según el rol del usuario
if ($user_role == 'Teacher') {
    // Obtener el ID del maestro desde la tabla teachers
    $teacher_id = "SELECT id FROM teachers WHERE user_id = (SELECT id FROM users WHERE username = '$username')";
    $result_teacher = mysqli_query($conn, $teacher_id);
    $teacher = mysqli_fetch_assoc($result_teacher);
    $teacher_id = $teacher['id'];

    // Obtener las asignaciones del profesor
    $sql_assignment = "SELECT * FROM assignments WHERE teacher_id = '$teacher_id'";
    $result_assignment = mysqli_query($conn, $sql_assignment);

    // Obtener los estudiantes del profesor
    $sql_students = "SELECT * FROM users WHERE user_role = 'Student'";
    $result_students = mysqli_query($conn, $sql_students);
} elseif ($user_role == 'Student') {
    // Obtener el ID del estudiante desde la tabla students
    $student_id = "SELECT id FROM students WHERE user_id = (SELECT id FROM users WHERE username = '$username')";
    $result_student = mysqli_query($conn, $student_id);
    $student = mysqli_fetch_assoc($result_student);
    $student_id = $student['id'];

    // Obtener las asignaciones del estudiante
    $sql_assignment = "SELECT * FROM assignments WHERE student_id = '$student_id'";
    $result_assignment = mysqli_query($conn, $sql_assignment);
} else {
    $error = "Rol de usuario no reconocido.";
}

include("include/header.php");
?>

<div class="container">
    <h1>Welcom, <?php echo $username; ?></h1>

    <!-- Mostrar información según el rol -->
    <?php if ($user_role == 'Teacher'): ?>
        <h2>Your Students</h2>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($student = mysqli_fetch_assoc($result_students)): ?>
                    <tr>
                        <td><?php echo $student['username']; ?></td>
                        <td><?php echo $student['email']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Your Assignments</h2>
        <table>
            <thead>
                <tr>
                    <th>Assignment Title</th>
                    <th>Description</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($assignment = mysqli_fetch_assoc($result_assignment)): ?>
                    <tr>
                        <td><?php echo $assignment['title']; ?></td>
                        <td><?php echo $assignment['description']; ?></td>
                        <td><?php echo $assignment['due_date']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php elseif ($user_role == 'Student'): ?>
        <h2>Your Assignments</h2>
        <table>
            <thead>
                <tr>
                    <th>Assignment Title</th>
                    <th>Description</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($assignment = mysqli_fetch_assoc($result_assignment)): ?>
                    <tr>
                        <td><?php echo $assignment['title']; ?></td>
                        <td><?php echo $assignment['description']; ?></td>
                        <td><?php echo $assignment['due_date']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include("include/footer.php"); ?>
