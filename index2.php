<?php
require './includes/db.php';

$query = "SELECT * FROM product_table WHERE card_group='categories'";
$result = $mysqli->query($query);

$query1 = "SELECT * FROM product_table WHERE card_group='top-sellers'";
$result1 = $mysqli->query($query1);

$query2 = "SELECT * FROM product_table WHERE card_group='power-plans'";
$result2 = $mysqli->query($query2);

$query3 = "SELECT * FROM product_table WHERE card_group='for-you'";
$result3 = $mysqli->query($query3);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Power Solutions</title>
    <!-- //Bootstrap CDN// -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

    <!-- bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- custom styles -->
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <!-- HERO SECTION -->
    <section class="hero">

        <!-- NAVBAR -->
        <nav class="navbar navbar-expand-lg px-5" data-bs-theme="dark">
            <div class="container-fluid">

                <!-- LOGO -->
                <a class="navbar-brand logo gap-5" href="./index.html">
                    <img src="./images/nav brand.png" alt="Power Solutions" class="logo">
                </a>

                <!-- MOBILE TOGGLER -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
                    aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- NAVIGATION -->
                <div class="collapse navbar-collapse links" id="navbarScroll">

                    <ul class="navbar-nav me-auto my-2 my-lg-0">

                        <li class="nav-item">
                            <a class="nav-link active" href="./index.html">
                                Home
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Shop
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                About Us
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Service
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                Contact
                            </a>
                        </li>

                    </ul>

                    <!-- RIGHT SIDE ICONS -->
                    <div class="nav-icons d-flex align-items-center gap-3">

                        <!-- SEARCH -->
                        <button class="search-btn" onclick="openSearch()" aria-label="Search">
                            <i class="bi bi-search"></i>
                        </button>

                        <!-- USER -->
                        <a class="person-btn" href="#" aria-label="User Profile">
                            <i class="bi bi-person"></i>
                        </a>

                        <!-- CART -->
                        <a class="cart-btn" href="#" aria-label="Shopping Cart, 0 items">
                            <i class="bi bi-cart"></i>
                            <span class="cart-count" id="cartCount" aria-live="polite" aria-atomic="true">0</span>
                        </a>

                    </div>

                </div>

            </div>
        </nav>


        <!-- WHATSAPP BAR -->
        <div class="whatsapp">

            <a class="whatsapp-icon"
                href="https://wa.me/2348089871083?text=Hello%20Power%20Solutions%2C%20I%20would%20like%20to%20make%20an%20enquiry."
                aria-label="Chat with Business Name on WhatsApp" target="_blank">
                <alt="whatsapp"><i class="bi bi-whatsapp"></i>
            </a>
        </div>


        <!-- HERO CONTENT -->
        <div class="hero-content row my-5 mx-5 gap-3 ">

            <div class="col-md-9 text-light ">
                <h5 class="text-light fs-6 fw-bold" style="font-family: Arial, Helvetica, sans-serif;">Reliable Power
                    Solutions
                </h5>

                <h1 class=" fw-bold">
                    Solar & Power Solutions <br> <span style="color: #25d339;">for Homes, Businesses, <br> and
                        Industries</span>
                </h1>

                <p class="fs-6">
                    From solar panels, inverters to generators, We provide high-quality <br> <span></span> products and
                    spare parts to keep you powered <br> wherever you are.
                </p>



                <div class="row hero-icons g-3">

                    <!-- Genuine Products -->
                    <div class="col-12 col-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-3 icon">
                                <i class="bi bi-shield-check"></i>
                            </span>

                            <span class="lh-sm size">
                                Genuine Products
                            </span>
                        </div>
                    </div>

                    <!-- Fast Delivery -->
                    <div class="col-12 col-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-3 icon">
                                <i class="bi bi-truck"></i>
                            </span>

                            <span class="lh-sm size">
                                Fast & Reliable<br>
                                Delivery
                            </span>
                        </div>
                    </div>

                    <!-- Expert Support -->
                    <div class="col-12 col-md-4">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-3 icon">
                                <i class="bi bi-headset"></i>
                            </span>

                            <span class="lh-sm size">
                                Expert Support
                            </span>
                        </div>
                    </div>

                </div>

                <button class="btn hbutton my-3">Shop Now <i class="bi bi-arrow-right"></i></button>

            </div>
    </section>

    <!-- HERO BOTTOM -->
    <div class="hero-bottom container-fluid">
        <div class="row align-items-center justify-content-center"> <!-- Solar Panels -->
            <div class="col-12 col-sm-6 col-lg item">
                <div class="d-flex align-items-center justify-content-center gap-2"> <span class="solar-icon"> <img
                            src="./images/solarpanel.png" alt="Solar panels" class="solar-image"> </span>
                    <div class="size">
                        <h6 class="h mb-1">Solar Panels</h6>
                        <p class="lh-sm font mb-0">Harness the Sun</p>
                    </div>
                </div>
            </div> <!-- Inverters -->
            <div class="col-12 col-sm-6 col-lg item">
                <div class="d-flex align-items-center justify-content-center gap-2"> <span class="solar-icon"> <img
                            src="./images/inverter.png" alt="Inverters" class="solar-image"> </span>
                    <div class="size">
                        <h6 class="h mb-1">Inverters</h6>
                        <p class="lh-sm font mb-0">Stable Power Always</p>
                    </div>
                </div>
            </div> <!-- DC & AC Breakers -->
            <div class="col-12 col-sm-6 col-lg item">
                <div class="d-flex align-items-center justify-content-center gap-2"> <span class="solar-icon"> <img
                            src="./images/breaker.png" alt="DC and AC breakers" class="breaker-image"> </span>
                    <div class="size">
                        <h6 class="h mb-1">DC & AC Breakers</h6>
                        <p class="lh-sm font mb-0">Safe & Reliable</p>
                    </div>
                </div>
            </div> <!-- Solar Fan -->
            <div class="col-12 col-sm-6 col-lg item">
                <div class="d-flex align-items-center justify-content-center gap-2"> <span class="solar-icon"> <img
                            src="./images/fan.png" alt="Solar fan" class="breaker-image"> </span>
                    <div class="size">
                        <h6 class="h mb-1">Solar Fan</h6>
                        <p class="lh-sm font mb-0">Cooler, greener living</p>
                    </div>
                </div>
            </div> <!-- Solar Generators -->
            <div class="col-12 col-sm-6 col-lg item">
                <div class="d-flex align-items-center justify-content-center gap-2"> <span class="solar-icon"> <img
                            src="./images/sgen.png" alt="Solar generators" class="breaker-image"> </span>
                    <div class="size">
                        <h6 class="h mb-1">Solar Generators</h6>
                        <p class="lh-sm font mb-0">Power on the go</p>
                    </div>
                </div>
            </div> <!-- Generator Parts -->
            <div class="col-12 col-sm-6 col-lg item">
                <div class="d-flex align-items-center justify-content-center gap-2"> <span class="solar-icon"> <img
                            src="./images/pngegg.png" class="breaker-image"> </span>
                    <div class="size">
                        <h6 class="h mb-1">Generator Parts</h6>
                        <p class="lh-sm font mb-0">Keep It Running</p>
                    </div>
                </div>
            </div> <!-- Filters & Spare Parts -->
            <div class="col-12 col-sm-6 col-lg item">
                <div class="d-flex align-items-center justify-content-center gap-2"> <span class="solar-icon"> <img
                            src="./images/cat.png" class="breaker-image"> </span>
                    <div class="size">
                        <h6 class="h mb-1">Filters & Spare Parts</h6>
                        <p class="lh-sm font mb-0">For big Engines</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- cards -->
    <div class="card1 ">
        <div class=" my-5 mx-5 px-4">
            <div class="product-section ">
                <div class="row row-cols-1 row-cols-md-3 g-4 mb-3">

                    <?php while ($user = $result->fetch_assoc()) { ?>
                        <div class="col">
                            <div class="card h-100">
                                <img src="<?php echo $user['image']; ?>" class="card-img-top1" alt="...">
                                <div class="card-body custom-bg">
                                    <h5 class="card-title"><?php echo $user['product_name']; ?></h5>
                                    <p class="card-text"><?php echo $user['description']; ?></p>
                                </div>
                                <div class="card-footer custom-bg">
                                    <button class="btn btn-success">Shop <i class="bi bi-arrow-right"></i></button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
    <br> <br> <br>






    <!-- top sellers -->
    <div class="bg-primary topsellers  rounded-5">

        <div class="top-sellers-heading">
            <h2 class="text-light fw-bold">TOP SELLERS</h2>
            <h6 class="text-light">Up to 50% Off</h6>
        </div>

        <div class="card-group gap-3">

            <!-- CARD 1 -->
            <?php while ($user = $result1->fetch_assoc()) { ?>
                <div class="card cardbox1 seller-card">
                    <img src="<?php echo $user['image']; ?>" class="card-img-top2 seller-image" alt="Sun King solar fan">

                    <div class="card-body">
                        <h5 class="card-title"><?php echo $user['product_name']; ?></h5>
                        <p class="card-text"><?php echo $user['description']; ?></p>

                        <?php if ((float) $user['price'] > 0) { ?>
                            <h5 class="fw-bold h5 price1">₦<?php echo number_format((float) $user['price']); ?></h5>
                        <?php } ?>
                        <?php if ((float) $user['old_price'] > 0) { ?>
                            <p class="h6 old-price"><s>₦<?php echo number_format((float) $user['old_price']); ?></s></p>
                        <?php } ?>
                    </div>

                    <div class="card-footer py-2">
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>

    <!-- Solar section -->
    <div class=" solarfan border rounded-5 mx-5 py-3 px-4 my-4">
        <div class="parent">
            <h2 class="text-light fw-bold">Power Plans</h2>
            <h6 class="text-light">Up to 60% Off</h6>
            <div class="child">
                <button class="btn see-all-blue">
                    <span>
                        See All
                        <i class="bi bi-arrow-right"></i>
                    </span>
                </button>
            </div>
        </div>

        <div class="card-group gap-3 topsellerscard">
            <?php while ($user = $result2->fetch_assoc()) { ?>
                <div class="card cardbox"> <a href="#"></a>
                    <img src="<?php echo $user['image']; ?>" class="card-img-top2" alt="...">
                    <div class="card-body2">
                        <h5 class="card-title"><?php echo $user['product_name']; ?></h5>
                        <br>
                        <h5 class="fw-bold h2">₦<?php echo number_format((float) $user['price']); ?></h5>
                        <p class=" old-price2"><s>₦<?php echo number_format((float) $user['old_price']); ?></s></p>
                    </div>
                </div>
            <?php } ?>



        </div>
    </div>
    </div>


    <!-- For You section -->
    <div class=" outdoor border rounded-5 mx-5 py-3 px-4 my-4">
        <div class="parent">
            <h2 class="text-light fw-bold">For You</h2>
            <h6 class="text-light">Up to 60% Off</h6>
            <div class="child">
                <button class="see-all-btn">
                    <span>See All</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>

        <div class="card-group gap-3 topsellerscard  ">
            <?php while ($user = $result3->fetch_assoc()) { ?>
                <div class="card cardbox"> <a href="#"></a>
                    <img src="<?php echo $user['image']; ?>" class="card-img-top2" alt="...">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $user['product_name']; ?></h5>
                        <br>
                        <h5 class="fw-bold h3">₦<?php echo number_format((float) $user['price']); ?></h5>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    </div>


    <!-- Search Overlay -->
    <div id="searchOverlay" class="search-overlay">

        <div class="search-box">

            <form action="#" method="get">
                <i class="bi bi-search search-inside-icon"></i>

                <input type="search" name="search" placeholder="Search for products..." autofocus>

                <button type="button" class="close-search" onclick="closeSearch()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </form>

        </div>

    </div>

    <!-- =========================
     FOOTER
========================= -->

    <div id="managedCatalog" aria-live="polite"></div>

    <footer class="site-footer">

        <div class="footer-container">

            <!-- Brand -->
            <div class="footer-column footer-brand">

                <h2 class="footer-logo">
                    <img src="./images/nav brand.png" alt="" class="w-50">
                </h2>

                <p>
                    Reliable solar and power solutions designed to keep
                    your home, business and lifestyle powered.
                </p>

                <div class="footer-socials">
                    <a href="https://web.facebook.com/otaroghene.favour.odafejirorosua/" aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>

                    <a href="https://www.instagram.com/the_ot_keys/" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>

                    <a href="https://wa.me/2348089871083?text=Hello%20Power%20Solutions%2C%20I%20would%20like%20to%20make%20an%20enquiry."
                        aria-label="WhatsApp">
                        <i class="bi bi-whatsapp"></i>
                    </a>

                    <a href="https://www.tiktok.com/@theotkeys" aria-label="TikTok">
                        <i class="bi bi-tiktok"></i>
                    </a>
                </div>

            </div>


            <!-- Quick Links -->
            <div class="footer-column">

                <h4>Quick Links</h4>

                <a href="./index.html">Home</a>
                <a href="#">Shop</a>
                <a href="#">Power Plans</a>
                <a href="#">Solar Panels</a>
                <a href="#">Generators</a>
                <a href="#">Contact Us</a>

            </div>


            <!-- Categories -->
            <div class="footer-column">

                <h4>Categories</h4>

                <a href="#">Solar Panels</a>
                <a href="#">Power Stations</a>
                <a href="#">Solar Generators</a>
                <a href="#">Inverters</a>
                <a href="#">Batteries</a>
                <a href="#">Accessories</a>

            </div>


            <!-- Contact -->
            <div class="footer-column">

                <h4>Contact Us</h4>

                <div class="footer-contact">
                    <i class="bi bi-geo-alt"></i>
                    <span>Delta State, Nigeria</span>
                </div>

                <div class="footer-contact">
                    <i class="bi bi-telephone"></i>
                    <span>+234 808 987 1083</span>
                </div>

                <div class="footer-contact">
                    <i class="bi bi-envelope"></i>
                    <span>favour.efado@gmail.com</span>
                </div>

                <div class="footer-contact">
                    <i class="bi bi-clock"></i>
                    <span>Mon – Sat: 8:00 AM – 6:00 PM</span>
                </div>

            </div>

        </div>


        <!-- Newsletter -->
        <div class="footer-newsletter">

            <div>
                <h4>Stay Powered ⚡</h4>
                <p>
                    Get updates on new products, deals and power solutions.
                </p>
            </div>

            <form class="newsletter-form">

                <input type="email" placeholder="Enter your email" required>

                <button type="submit">
                    Subscribe
                    <i class="bi bi-arrow-right"></i>
                </button>

            </form>

        </div>


        <!-- Bottom -->
        <div class="footer-bottom">

            <p>
                © 2026 Power Solutions. All Rights Reserved.
            </p>

            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms & Conditions</a>
            </div>

        </div>

    </footer>

    <script>
        function openSearch() {
            document.getElementById("searchOverlay").style.display = "block";
        }

        function closeSearch() {
            document.getElementById("searchOverlay").style.display = "none";
        }
    </script>

    <script>
        const navLinks = document.querySelectorAll('.navbar .nav-link');

        navLinks.forEach(link => {
            link.addEventListener('click', function () {

                navLinks.forEach(item => {
                    item.classList.remove('active');
                });

                this.classList.add('active');
            });
        });
    </script>

    <script>
        (function () {
            const cartCount = document.getElementById('cartCount');
            const cartLink = document.querySelector('.cart-btn');
            const storageKey = 'powerSolutionsCartCount';
            let itemCount = Number(localStorage.getItem(storageKey)) || 0;

            function updateCartCount() {
                cartCount.textContent = String(itemCount);
                cartLink.setAttribute('aria-label', 'Shopping Cart, ' + itemCount + (itemCount === 1 ? ' item' : ' items'));
            }

            document.querySelectorAll('.seller-card, .cardbox').forEach(card => {
                const buttonContainer = card.classList.contains('seller-card')
                    ? card.querySelector('.card-footer')
                    : card.querySelector('.card-body, .card-body2');
                if (buttonContainer && !buttonContainer.querySelector('.add-to-cart-btn')) {
                    buttonContainer.insertAdjacentHTML('beforeend', '<button type="button" class="btn btn-success add-to-cart-btn"><i class="bi bi-cart-plus" aria-hidden="true"></i> Add to cart</button>');
                }
            });

            document.addEventListener('click', event => {
                const button = event.target.closest('.add-to-cart-btn');
                if (!button) return;

                itemCount += 1;
                localStorage.setItem(storageKey, String(itemCount));
                updateCartCount();
            });

            updateCartCount();
        }());
    </script>

    <script>
        (function () {
            const money = value => '₦' + Number(value).toLocaleString('en-NG', { maximumFractionDigits: 0 });
            const escapeHtml = value => String(value).replace(/[&<>"']/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[character]));

            Promise.all([
                fetch('data/site.json', { cache: 'no-store' }).then(response => {
                    if (!response.ok) throw new Error('Site content could not be loaded.');
                    return response.json();
                }),
                fetch('api/product-cards.php', { cache: 'no-store' }).then(response => {
                    if (!response.ok) throw new Error('Product cards could not be loaded from the database.');
                    return response.json();
                })
            ])
                .then(([site, cardsByGroup]) => {
                    const hero = site.hero || {};
                    const heroSection = document.querySelector('.hero');
                    if (heroSection && hero.image) {
                        heroSection.style.backgroundImage = 'linear-gradient(rgba(12, 16, 47, 0.715), rgba(3, 7, 30, 0.683)), url("' + encodeURI(hero.image) + '")';
                    }
                    const eyebrow = document.querySelector('.hero-content h5');
                    const title = document.querySelector('.hero-content h1');
                    const description = document.querySelector('.hero-content > div > p');
                    if (eyebrow && hero.eyebrow) eyebrow.textContent = hero.eyebrow;
                    if (title && hero.title) title.innerHTML = escapeHtml(hero.title) + ' <br><span style="color:#25d339">' + escapeHtml(hero.highlight || '') + '</span>';
                    if (description && hero.description) description.textContent = hero.description;

                    const catalog = document.getElementById('managedCatalog');
                    (site.containers || []).filter(container => (container.placement || 'featured') === 'featured').forEach(container => {
                        const cards = (cardsByGroup.featured || []).map(product => {
                            const price = Number(product.price) || 0;
                            const oldPrice = Number(product.old_price) || 0;
                            return '<article class="managed-product card">' +
                                '<img src="' + encodeURI(product.image) + '" class="card-img-top2 managed-product-image" alt="' + escapeHtml(product.name) + '">' +
                                '<div class="card-body"><h5 class="card-title">' + escapeHtml(product.name) + '</h5><p class="card-text">' + escapeHtml(product.description) + '</p>' +
                                (price > 0 ? '<h5 class="fw-bold h3 managed-price">' + money(price) + '</h5>' : '') +
                                (oldPrice > price ? '<p class="managed-old-price"><s>' + money(oldPrice) + '</s></p>' : '') +
                                '<button type="button" class="btn btn-success add-to-cart-btn"><i class="bi bi-cart-plus" aria-hidden="true"></i> Add to cart</button>' +
                                '</div></article>';
                        }).join('');
                        if (cards) {
                            catalog.insertAdjacentHTML('beforeend', '<section class="managed-container"><div class="parent"><h2 class="text-light fw-bold">' + escapeHtml(container.title) + '</h2><h6 class="text-light">' + escapeHtml(container.subtitle) + '</h6></div><div class="managed-products">' + cards + '</div></section>');
                        }
                    });

                    function updateCardGroup(group, cardNodes) {
                        const products = cardsByGroup[group] || [];
                        cardNodes.forEach((card, index) => {
                            const product = products[index];
                            if (!product) return;
                            const image = card.querySelector('img');
                            const title = card.querySelector('.card-title');
                            const body = card.querySelector('.card-body, .card-body2');
                            if (image) {
                                image.src = encodeURI(product.image);
                                image.alt = product.name;
                            }
                            if (title) title.textContent = product.name;
                            if (!body) return;
                            let description = Array.from(body.querySelectorAll('p')).find(paragraph =>
                                !paragraph.matches('.old-price, .old-price2, .managed-old-price')
                            );
                            if (product.description && !description) {
                                description = document.createElement('p');
                                description.className = 'card-text';
                                const price = body.querySelector('.price1, .h2, .h3, .managed-price');
                                body.insertBefore(description, price || null);
                            }
                            if (description && !description.matches('.old-price, .old-price2, .managed-old-price')) {
                                description.textContent = product.description;
                                description.hidden = !product.description;
                            }
                        });
                    }

                    updateCardGroup('categories', document.querySelectorAll('.product-section .row > .col'));
                    updateCardGroup('top-sellers', document.querySelectorAll('.topsellers > .card-group > .seller-card'));
                    updateCardGroup('power-plans', document.querySelectorAll('.solarfan .cardbox'));
                    updateCardGroup('for-you', document.querySelectorAll('.outdoor .cardbox'));
                    updateCardGroup('featured', document.querySelectorAll('.managed-product'));
                })
                .catch(error => console.error(error));
        }());
    </script>

</body>

</html>