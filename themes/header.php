<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="lang-selector">
            <span>US</span>
            <span>&#127482;&#127480;</span>
            <span>EN &#9662;</span>
        </div>
        <button class="login-btn">Login portal &#8594;</button>
    </div>

    <!-- Main Navigation -->
    <nav class="main-nav">
        <a href="<?php echo home_url(); ?>" class="logo">
            <svg class="logo-icon" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="40" height="40" rx="8" fill="#1e40af"/>
                <path d="M12 14h6v3h-6zM22 14h6v3h-6zM12 20h6v3h-6zM22 20h6v3h-6zM17 26h6v3h-6z" fill="#60a5fa"/>
                <circle cx="20" cy="12" r="4" fill="#38bdf8"/>
            </svg>
            <div class="logo-text">
                Best Doctors
                <span>INSURANCE</span>
            </div>
        </a>

        <div class="nav-links">
            <a href="#">Home</a>
            <a href="#" class="has-dropdown">Our plans</a>
            <a href="#" class="has-dropdown">About us</a>
            <a href="#">Blog</a>
            <a href="#" class="has-dropdown">Support</a>
            <span class="nav-search">&#128269;</span>
        </div>
    </nav>
</header>
