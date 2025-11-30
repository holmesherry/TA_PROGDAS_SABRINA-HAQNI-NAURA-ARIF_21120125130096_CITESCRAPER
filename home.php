<?php
// home.php – hero simple
?>
<div class="home-page">
    <div class="home-hero-card">

        <!-- Logo PNG (2 versi: logo-light.png & logo-dark.png) -->
        <img src="<?php echo e($logoPath); ?>" alt="CiteScraper Logo" class="home-logo-main">

        <p class="home-hero-text">
            CiteScraper is a modern, intuitive citation platform designed to help students, researchers, and
            academic writers generate and convert references effortlessly. Built to support multiple
            international citation styles including IEEE, APA, MLA, Chicago, Harvard, AMA, CSE, Bluebook,
            and Mendeley format, CiteScraper makes academic formatting fast, accurate, and stress-free.
            CiteScraper is built for productivity, precision, and creative academic workflow, allowing you
            to build your citations high and strong like a skyscraper.
        </p>

        <div class="home-cta-wrapper">
            <a href="<?php echo mklink(['page' => 'citationtools']); ?>" class="btn-cta">
                Citate Now
            </a>
        </div>
    </div>
</div>
