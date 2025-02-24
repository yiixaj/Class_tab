-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-01-2025 a las 01:06:19
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `tutor`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL,
  `due_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `assignments`
--

INSERT INTO `assignments` (`id`, `teacher_id`, `title`, `description`, `due_date`, `created_at`, `file_path`, `due_datetime`) VALUES
(7, 1, 'qqqq', '<figure class=\\\"table\\\"><table><tbody><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></tbody></table></figure><p>gjgjgjgjg</p><p>gjgjgjgjg</p><p><a href=\\\"https://www.youtube.com/watch?v=BwkaeYqbNBk\\\">https://www.youtube.com/watch?v=BwkaeYqbNBk</a></p>', '2025-02-03', '2025-01-20 01:16:25', '', '0000-00-00 00:00:00'),
(8, 1, 'Tarea #3', '<p>Resolver la matriz</p><figure class=\\\"table\\\"><table><tbody><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></tbody></table></figure><p>&nbsp;</p>', '2025-02-08', '2025-01-20 01:17:16', '', '0000-00-00 00:00:00'),
(13, 1, 'Tarea #4', 'Resolver las siguientes preguntas:\\r\\n1) ¿Que sucedio en el año 1810?\\r\\n2) ¿Por que sucedió la guerra con Francia?\\r\\n3) ¿Como se resolvió la disolución de londres?', '2025-02-08', '2025-01-22 15:21:50', '', '0000-00-00 00:00:00'),
(14, 1, 'Que es un Base de datos distribuida', '<p>Resolver el siguiente documento</p><figure class=\\\"table\\\"><table><tbody><tr><td>1</td><td>2</td><td>3</td></tr><tr><td>4</td><td>5</td><td>6</td></tr></tbody></table></figure>', '2025-02-08', '2025-01-22 16:43:23', 'uploads/Ramirez.docx', '0000-00-00 00:00:00'),
(15, 1, 'prueba', 'ffff', '2025-02-07', '2025-01-24 23:35:04', 'uploads/EXTRA DOCUMENTO-FIRMA DEL TUTOR DE TESIS (1).pdf', '0000-00-00 00:00:00'),
(16, 1, 'practica nueva', 'Hola', '2025-02-12', '2025-01-24 23:47:33', 'uploads/EXTRA DOCUMENTO-FIRMA DEL TUTOR DE TESIS (3).pdf', '0000-00-00 00:00:00'),
(17, 0, 'Prueba de tarea por docente', 'Resuelve las preguntas del documento nuevamente', '2025-02-05', '2025-01-25 00:02:59', 'uploads/EXTRA_DOCUMENTOFIRMA_DEL_TUTOR_DE_TESIS_(3)_signed.pdf', '0000-00-00 00:00:00'),
(18, 0, 'TAREA#1', '24 DE ENERO DEL 2025', '2025-02-05', '2025-01-25 00:04:13', 'uploads/EXTRA_DOCUMENTOFIRMA_DEL_TUTOR_DE_TESIS_(3)_signed.pdf', '0000-00-00 00:00:00'),
(27, 6, 'Tarea de ejemplo', 'Descripción de la tarea', '2025-01-31', '2025-01-25 00:24:52', 'documento.pdf', '0000-00-00 00:00:00'),
(61, 4, 'PRUEBA #1', 'resolver el siguiente documento', '2025-01-26', '2025-01-26 16:47:09', 'uploads/Andre Romero CV.pdf', '2025-01-26 12:00:00'),
(62, 4, 'revisar hora', 'revisar hora', '2025-02-08', '2025-01-26 20:58:32', '', '2025-02-08 19:00:00'),
(63, 4, 'vuelve a revisar', '12', '2025-01-26', '2025-01-26 21:04:12', 'uploads/til1.pdf', '2025-01-26 20:10:00'),
(68, 7, 'asd', 'asd', '2025-01-26', '2025-01-27 01:53:53', '', '2025-01-26 20:56:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) DEFAULT NULL,
  `grade` int(11) NOT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grades`
--

INSERT INTO `grades` (`id`, `submission_id`, `grade`, `comments`) VALUES
(1, 3, 5, 'Trabajo mal hecho'),
(3, 4, 8, 'Entendido'),
(5, 6, 3, 'atrasado estudiante'),
(6, 7, 2, 'olv'),
(7, 10, 4, 'sss'),
(8, 11, 10, 'vuelvo a ver el cuadro de revision');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `students`
--

INSERT INTO `students` (`id`, `user_id`, `enrollment_date`) VALUES
(1, 2, '2025-01-18 23:59:00'),
(2, 3, '2025-01-19 16:15:00'),
(3, 5, '2025-01-24 00:39:08'),
(4, 6, '2025-01-24 14:25:58'),
(5, 7, '2025-01-24 14:40:00'),
(6, 8, '2025-01-24 14:44:24'),
(7, 10, '2025-01-24 16:22:10'),
(8, 11, '2025-01-24 16:22:33'),
(9, 15, '2025-01-25 12:17:31'),
(10, 16, '2025-01-26 17:43:41'),
(11, 19, '2025-01-26 20:44:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `subjects`
--

INSERT INTO `subjects` (`id`, `name`) VALUES
(1, 'Matemáticas'),
(2, 'Literatura'),
(3, 'Historia'),
(4, 'Ciencias'),
(5, 'Computación'),
(6, 'Inglés'),
(7, 'Química'),
(8, 'Contabilidad'),
(9, 'Educacion Fisica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `assignment_id` int(11) DEFAULT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `content` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `grade` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `submission_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `submissions`
--

INSERT INTO `submissions` (`id`, `student_id`, `assignment_id`, `submission_date`, `content`, `file_path`, `grade`, `feedback`, `submission_datetime`) VALUES
(3, 2, 14, '2025-01-22 16:54:25', 'Resolucion del ejercicio hecho en el documento', 'uploads/submissions/Anexo 5. Carta compromiso - Andre Romero.xlsx', 5, 'Trabajo mal hecho', NULL),
(4, 2, 7, '2025-01-22 23:26:27', 'El video no se visualiza', NULL, 8, 'Entendido', NULL),
(6, 9, 38, '2025-01-25 14:37:33', 'resuelto', NULL, 3, 'atrasado estudiante', NULL),
(7, 2, 52, '2025-01-26 01:03:17', 'Resuelto pero llego tarde', NULL, 2, 'olv', NULL),
(8, 2, 50, '2025-01-26 01:23:19', 'YA RESOLVI', 'uploads/submissions/editar funcional 20-1.txt', 9, 'subiendo notas', NULL),
(9, 2, 41, '2025-01-26 01:49:53', 'perdon por la tardanza', NULL, NULL, NULL, NULL),
(10, 2, 53, '2025-01-26 01:58:51', 'eeeeee', NULL, 4, 'sss', NULL),
(11, 2, 54, '2025-01-26 02:21:02', 'SIIII', NULL, 10, 'vuelvo a ver el cuadro de revision', NULL),
(12, 2, 55, '2025-01-26 04:02:52', 'LEIDO LOS DOCUMENTOS', NULL, NULL, NULL, NULL),
(13, 2, 56, '2025-01-26 15:04:07', 'Resuelto en el documento pdf', 'uploads/submissions/Solicitud_enlinea.pdf', 10, 'Buena resolución', NULL),
(14, 2, 57, '2025-01-26 15:07:18', 'claroquesi', NULL, 4, 'mal', NULL),
(15, 2, 58, '2025-01-26 15:14:58', 'YYYYYYYYYY', NULL, 10, 'ESOOO', NULL),
(16, 2, 59, '2025-01-26 15:24:00', 'IOIOIO', NULL, 6, 'sip', NULL),
(17, 2, 60, '2025-01-26 16:22:51', 'lo hice a las 11:22', NULL, 7, 'por atraso', NULL),
(18, 2, 61, '2025-01-26 16:51:16', 'efectivamente, todo resuelto en el documento enviado', 'uploads/submissions/Solicitud_enlinea.pdf', 9, 'le falta', NULL),
(19, 11, 62, '2025-01-26 20:59:08', 'resuelto', 'uploads/submissions/Andre Romero CV.pdf', NULL, NULL, NULL),
(20, 11, 63, '2025-01-26 21:05:13', 'sip', '/uploads/submissions/6796a389aa4aa_11_63.pdf', 10, 'increible trabajo', NULL),
(21, 2, 64, '2025-01-27 00:39:03', 'hola', NULL, 8, 'si', NULL),
(22, 2, 65, '2025-01-27 00:44:30', 'pppp', NULL, 2, 'si', NULL),
(23, 2, 66, '2025-01-27 01:20:29', 'resuelta', NULL, 6, 'si', NULL),
(24, 2, 67, '2025-01-27 01:25:23', 'sip', NULL, 8, 'we', NULL),
(25, 2, 68, '2025-01-27 01:54:02', 'sssss', NULL, 10, 'si', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `hire_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `access_code` varchar(10) NOT NULL,
  `subject_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `teachers`
--

INSERT INTO `teachers` (`id`, `user_id`, `hire_date`, `access_code`, `subject_id`) VALUES
(1, 1, '2025-01-18 23:51:52', 'AC0001', NULL),
(2, 4, '2025-01-19 16:36:15', 'AC0002', NULL),
(3, 9, '2025-01-24 14:46:05', '', NULL),
(4, 12, '2025-01-24 23:45:51', 'aIT9KyGQ', 2),
(5, 13, '2025-01-25 00:15:28', 'hYnSayEM', NULL),
(6, 14, '2025-01-25 00:21:35', 'KPFYBKYJ', NULL),
(7, 17, '2025-01-26 18:06:13', 'OCig9D4X', 5),
(8, 18, '2025-01-26 19:17:31', 'FGmIaSIJ', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `teacher_students`
--

CREATE TABLE `teacher_students` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','accepted') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `teacher_students`
--

INSERT INTO `teacher_students` (`id`, `teacher_id`, `student_id`, `joined_at`, `status`) VALUES
(1, 2, 4, '2025-01-24 14:39:23', 'pending'),
(2, 1, 5, '2025-01-24 14:40:15', 'pending'),
(3, 1, 6, '2025-01-24 14:45:19', 'pending'),
(4, 1, 8, '2025-01-24 16:26:06', 'pending'),
(8, 4, 2, '2025-01-24 23:47:03', 'accepted'),
(10, 6, 9, '2025-01-25 12:18:14', 'pending'),
(11, 5, 9, '2025-01-25 12:18:25', 'pending'),
(12, 4, 9, '2025-01-25 12:18:35', 'accepted'),
(13, 8, 2, '2025-01-26 19:28:07', 'accepted'),
(14, 4, 11, '2025-01-26 20:49:47', 'accepted'),
(15, 7, 2, '2025-01-27 00:30:33', 'accepted');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_role` enum('Student','Teacher') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `user_role`, `created_at`) VALUES
(1, 'c', 'c@gmail.com', '$2y$10$meM8Tnr6WrtbYHnGoxULE.J8bCrNnJVLQOQGFLuI.uWjVg3gzSCfa', 'Teacher', '2025-01-18 23:51:52'),
(2, 'alfredo', 'alfredo@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Student', '2025-01-18 23:59:00'),
(3, 'andre', 'andre@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Student', '2025-01-19 16:15:00'),
(4, 'andres', 'andres@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Teacher', '2025-01-19 16:36:15'),
(5, 'alex', 'alex@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Student', '2025-01-24 00:39:08'),
(6, 'axel', 'axel@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Student', '2025-01-24 14:25:58'),
(7, 'alfi', 'alfi@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Student', '2025-01-24 14:40:00'),
(8, 'usher', 'usher@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Student', '2025-01-24 14:44:24'),
(9, 'profesor', 'prof@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Teacher', '2025-01-24 14:46:05'),
(10, 'news', 'news@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Student', '2025-01-24 16:22:10'),
(11, 'newss', 'newss@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Student', '2025-01-24 16:22:33'),
(12, 'Dell', 'dell@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Teacher', '2025-01-24 23:45:51'),
(13, 'NUEVO', 'nuevo@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Teacher', '2025-01-25 00:15:28'),
(14, 'DOCENTE', 'DOC@GMAIL.COM', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Teacher', '2025-01-25 00:21:35'),
(15, 'newer', 'newer@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Student', '2025-01-25 12:17:31'),
(16, 'pepe', 'pepe@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Student', '2025-01-26 17:43:41'),
(17, 'newtutor01', 'newtutor01@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Teacher', '2025-01-26 18:06:13'),
(18, 'newtutor02', 'newtutor02@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Teacher', '2025-01-26 19:17:31'),
(19, 'federico', 'fede@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Student', '2025-01-26 20:44:59');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indices de la tabla `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Indices de la tabla `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `assignment_id` (`assignment_id`);

--
-- Indices de la tabla `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `access_code` (`access_code`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `teacher_students`
--
ALTER TABLE `teacher_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT de la tabla `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `teacher_students`
--
ALTER TABLE `teacher_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
