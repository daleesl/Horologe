<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Horologe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>

    <?php include '../includes/navbar.php'; ?>

    <!-- HERO SECTION -->
    <section class="text-white min-vh-100 d-flex align-items-center" style="background-color: #181818">
        <div class="container-fluid p-3 p-md-5">
            <div class="row align-items-center g-0 flex-column-reverse flex-lg-row">
                <!-- Left Content -->
                <div class="col-lg-6 py-5 ps-lg-4 ps-3">
                    <div class="mb-5">
                        <span class="border border-white text-white px-3 px-md-5 py-2 d-inline-block small" style="letter-spacing: .2rem;">ESTABLISH 1924</span>
                    </div>

                    <div class="mb-5">
                        <h1 class="display-1 fw-normal text-white mb-md-5">Timeless</h1>
                        <h1 class="display-1 fw-light text-white mb-md-5"><em>Excellence</em> on Your</h1>
                        <h1 class="display-1 fw-normal text-white mb-md-5">Wrist.</h1>
                    </div>

                    <p class="text-secondary fs-5 mb-md-5 mb-3 pe-lg-5">
                        Discover the art of horology with our curated collection of the world's most prestigious timepieces.
                    </p>

                    <div class="d-flex flex-wrap gap-3">
                        <button class="btn btn-light text-dark px-4 py-2 fw-semibold">EXPLORE COLLECTIONS</button>
                        <button class="btn btn-outline-light px-4 py-2">OUR HERITAGE</button>
                    </div>
                </div>

                <!-- Right Image -->
                <div class="col-lg-6">
                    <img src="assets/images/heritageImages/black-men.png" alt="Luxury Watch" class="img-fluid w-100 h-100 object-fit-cover d-none d-lg-block">
                    <img src="assets/images/heritageImages/black-men.png" alt="Luxury Watch" class="img-fluid w-100 object-fit-cover d-lg-none" style="max-height: 500px;">
                </div>
            </div>
        </div>
    </section>


    <!-- FEATURED COLLECTION SECTION -->
    <section class="bg-black text-white ">
        <div class="container py-lg-5 py-md-3 py-2">
            <!-- Section Header -->
            <div class="row mb-5">
                <div class="col-lg-8">
                    <h2 class="display-3 fw-normal mb-3">The Featured Collection</h2>
                    <p class="fs-5 text-secondary">A selection of our most sought-after timepieces, representing the pinnacle of precision and style.</p>
                </div>
                <div class="col-lg-4 d-flex align-items-end justify-content-lg-end">
                    <a href="#" class="text-white text-decoration-none fs-6">VIEW ALL COLLECTIONS <span>→</span></a>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row g-3 g-md-4">
                <!-- Product 1 -->
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="bg-secondary">
                        <img src="assets/images/heritageImages/watchMaker-Heritage.jpg" alt="Aether Chronograph" class="img-fluid w-100" style="aspect-ratio: 3/4; object-fit: cover;">
                    </div>
                    <div class="pt-3">
                        <p class="small text-secondary mb-2" style="letter-spacing: .1rem;">CLASSIC HERITAGE</p>
                        <h3 class="h5 fw-normal mb-2">Aether Chronograph</h3>
                        <p class="text-white fw-semibold">$12,400</p>
                    </div>
                </div>

                <!-- Product 2 -->
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="bg-secondary">
                        <img src="assets/images/heritageImages/luxuryWatch-Heritage.jpg" alt="Deep Sea Navigator" class="img-fluid w-100" style="aspect-ratio: 3/4; object-fit: cover;">
                    </div>
                    <div class="pt-3">
                        <p class="small text-secondary mb-2" style="letter-spacing: .1rem;">GRAND SPORT</p>
                        <h3 class="h5 fw-normal mb-2">Deep Sea Navigator</h3>
                        <p class="text-white fw-semibold">$8,900</p>
                    </div>
                </div>

                <!-- Product 3 -->
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="bg-secondary">
                        <img src="assets/images/heritageImages/watchMaker-Heritage.jpg" alt="Vanguard GMT" class="img-fluid w-100" style="aspect-ratio: 3/4; object-fit: cover;">
                    </div>
                    <div class="pt-3">
                        <p class="small text-secondary mb-2" style="letter-spacing: .1rem;">CLASSIC HERITAGE</p>
                        <h3 class="h5 fw-normal mb-2">Vanguard GMT</h3>
                        <p class="text-white fw-semibold">$15,600</p>
                    </div>
                </div>

                <!-- Product 4 -->
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="bg-secondary">
                        <img src="assets/images/heritageImages/luxuryWatch-Heritage.jpg" alt="Midnight Tourbillion" class="img-fluid w-100" style="aspect-ratio: 3/4; object-fit: cover;">
                    </div>
                    <div class="pt-3">
                        <p class="small text-secondary mb-2" style="letter-spacing: .1rem;">LIMITED EDITIONS</p>
                        <h3 class="h5 fw-normal mb-2">Midnight Tourbillion</h3>
                        <p class="text-white fw-semibold">$112,000</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- HOROLOGICAL MASTERY SECTION -->
    <section class="bg-black text-white" style="background-color: #181818">
        <div class="container-fluid p-5 p-md-5" style="background-color: #171717;">
            <div class="row align-items-center g-4 flex-column-reverse flex-lg-row">
                <!-- Left Content -->
                <div class="col-lg-6">
                    <h2 class="display-3 fw-normal text-white mb-4">A Century of Horological Mastery</h2>

                    <p class="fs-5 text-secondary mb-4">
                        Founded in the heart of the Swiss Alps, Horologe has been dedicated to the pursuit of perfection for over a hundred years. Each timepiece is a testament to our commitment to craftsmanship and innovation.
                    </p>

                    <p class="fs-5 text-secondary mb-4">
                        Our master watchmakers spend hundreds of hours on a single movement, ensuring that every tick is a synchronization of art and engineering.
                    </p>

                    <a href="#" class="text-white text-decoration-none fs-6">LEARN MORE <span>→</span></a>
                </div>

                <!-- Right Images -->
                <div class="col-lg-6">
                    <div class="row g-3">
                        <div class="col-6 mb-5">
                            <img src="../assets/images/watch-machine.jpg" alt="Watch Mechanism" class="img-fluid w-100 h-100 object-fit-cover">
                        </div>
                        <div class="col-6 mt-5">
                            <img src="../assets/images/machine-worker.jpg" alt="Master Watchmaker" class="img-fluid w-100 h-100 object-fit-cover">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIAL SECTION -->
    <section class="bg-black text-white py-5">
        <div class="container-fluid p-3 p-md-5">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <!-- Stars -->
                    <div class="mb-4">
                        <span class="fs-4">★ ★ ★ ★ ★</span>
                    </div>

                    <!-- Quote -->
                    <blockquote class="mb-4">
                        <p class="display-6 fw-normal text-white" style="font-style: italic;">
                            "Horologe doesn't just sell watches; they provide a legacy. The attention to detail in my Celestial Moonphase is unlike anything else in my collection."
                        </p>
                    </blockquote>

                    <!-- Author -->
                    <div>
                        <p class="text-white fw-semibold mb-1">JOHN JESTER</p>
                        <p class="text-secondary small" style="letter-spacing: .1rem;">COLLECTOR & ENTHUSIAST</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>