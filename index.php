<?php 
    session_start();
    include("include/header.php");
    require_once 'config.php';
?>
    <section class="header">
        <div class="container">
            <div class="info-box">
                <img id="asset" src="./assets/01.svg" alt="" />
            </div>
            <div id="data" class="info-box">
                <h1 class="hero-heading">ClassTab</h1>
                <div class="dash"></div>
                <p>Optimiza tu aprendizaje con nosotros. Como instituión moderna, nos enfocamos en cada aspecto de tu educación, desde la preparación de exámenes hasta el desarrollo de habilidades clave. ¡Invierte en tu futuro y elige los mejores resultados</p>
                <a href="#services" class="link-btn">Conoce más &#8594;</a>                
            </div>
        </div>
    </section>
    <section class="services" id="services">
        <div class="container" id="data">
            <h2>Los mejores resultados vienen de las buenas practicas</h2>
            <div class="dash" style="margin: 10px auto 10px auto !important"></div>
            <div class="cards">
                <div class="service-card">
                    <img src="./assets/08.svg" alt="">
                    <h3>Actividades</h3>
                    <p>Worried about exams ahead or not ready to take it? We are a one stop solution for your problem. Choose us to enter your exam with high confidence!</p>
                </div>
                <div class="service-card">
                    <img src="./assets/06.svg" alt="">
                    <h3>Sistema de calificacion</h3>
                    <p>Having problem with a course assignment? we have group of instructors to troubleshoot your shortcomings and help you to achieve your target results.</p>
                </div>
                <div class="service-card">
                    <img src="./assets/09.svg" alt="">
                    <h3>Sistema de verificacion</h3>
                    <p>Skills bring confidence to face the world. If you feel you have guts to learn a skill and need guideline, just knock us, get best assistance besides you.</p>
                </div>
            </div> 
        </div>
    </section>
<?php 
    include("include/footer.php");
?>