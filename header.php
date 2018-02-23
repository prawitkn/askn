
<?php
        $menu_active = basename($_SERVER['REQUEST_URI']);
?> 

<header class="navbar navbar-inverse navbar-fixed-top wet-asphalt" role="banner">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php"><img src="images/Good48.png" alt="logo"></a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <?php
                    switch ($menu_active)    {
                        case "index.php":
                        echo '<li class="active"><a href="index.php">Home</a></li>
                    <li><a href="about-us.php">About Us</a></li>
                    <li><a href="services.php">Products and Services</a></li>
                    <li><a href="portfolio.php">Portfolio</a></li>';
                            break;
                        case "about-us.php":
                        echo '<li><a href="index.php">Home</a></li>
                    <li class="active"><a href="about-us.php">About Us</a></li>
                    <li><a href="services.php">Products and Services</a></li>
                    <li><a href="portfolio.php">Portfolio</a></li>';
                            break;
                        case "services.php":
                        echo '<li><a href="index.php">Home</a></li>
                    <li><a href="about-us.php">About Us</a></li>
                    <li class="active"><a href="services.php">Products and Services</a></li>
                    <li><a href="portfolio.php">Portfolio</a></li>';
                            break;
                        case "portfolio.php":
                        echo '<li><a href="index.php">Home</a></li>
                    <li><a href="about-us.php">About Us</a></li>
                    <li><a href="services.php">Products and Services</a></li>
                    <li class="active"><a href="portfolio.php">Portfolio</a></li>';
                            break;
                        
                    }
                    ?>
                    
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Others<i class="icon-angle-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="career.html">Career</a></li>
                            <li><a href="blog-item.html">Blog Single</a></li>
                            <li><a href="pricing.html">Pricing</a></li>
                            <li><a href="404.html">404</a></li>
                            <li><a href="registration.html">Registration</a></li>
                            <li class="divider"></li>
                            <li><a href="privacy.html">Privacy Policy</a></li>
                            <li><a href="terms.html">Terms of Use</a></li>
                        </ul>
                    </li>
                    <li><a href="blog.php">News</a></li> 
                    <li><a href="contact-us.php">Contact Us</a></li>
                </ul>
            </div>
        </div>
    </header><!--/header-->

