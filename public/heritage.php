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

<body class="bg-black">

    <?php include '../includes/navbar.php'; ?>

    <!-- HERITAGE INTRO -->
    <div class="container-fluid py-5" style="background-color: #171717;">
        <div class="row">
            <div class="col-12">
                <div class="text-center mb-4">
                    <span class="border border-white text-white rounded-pill px-4 py-2 d-inline-block">EST. 1924</span>
                </div>

                <h1 class="display-2 text-white text-center mb-4">A Century of Excellence</h1>

                <p class="fs-5 text-secondary text-center mx-auto" style="max-width: 700px;">For nearly a century, Horologe has been synonymous with the finest traditions of <br class="d-none d-lg-inline">
                    Swiss watchmaking, combining artisanal craftsmanship with modern innovation.</p>
            </div>
        </div>
    </div>

    <!-- THE BEGINNING SECTION -->
    <div class="container py-5 border-bottom border-secondary">
        <div class="row align-items-center g-4">
            <div class="col-lg-6 order-lg-1 order-2">
                <h2 class="display-4 fw-bold text-white mb-4">The Beginning</h2>
                <p class="fs-5 text-secondary mb-3">
                    In 1924, master watchmaker Jean-Pierre Leblanc established a small atelier in the heart of Geneva's watchmaking district. His vision was simple yet ambitious: to craft timepieces that transcended their temporal purposes and became heirlooms passed down through generations.
                </p>
                <p class="fs-5 text-secondary mb-3">
                    Over the decades, Horologe expanded from a modest workshop to one of the world's most renowned independent watch houses, still deeply rooted in the traditions of the artisans who founded it.
                </p>
                <p class="fs-5 text-secondary mb-0">
                    Today, Horologe continues to honor the founder's commitment to excellence, producing fewer than 500 timepieces annually, each one a masterpiece in its own right.
                </p>
            </div>
            <div class="col-lg-6 order-lg-2 order-1 mt-4 mt-lg-0">
                <img src="../assets/images/machine-worker.jpg" alt="The Beginning - Craftsman" class="img-fluid border border-secondary rounded">
            </div>
        </div>
    </div>

    <!-- THE CRAFT SECTION -->
    <div class="container py-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-6 order-lg-1 mb-4 mb-lg-0">
                <img src="../assets/images/watch-machine.jpg" alt="The Craft - Watch Mechanism" class="img-fluid border border-secondary rounded">
            </div>
            <div class="col-lg-6 order-lg-2">
                <h2 class="display-4 fw-bold text-white mb-4">The Craft</h2>
                <p class="fs-5 text-secondary mb-3">
                    Each Horologe timepiece represents 300 hours of meticulous handiwork. Our master watchmakers employ traditional techniques passed down through generations, combined with exacting modern precision standards.
                </p>
                <p class="fs-5 text-secondary mb-3">
                    Every component is crafted in-house by our skilled horologists, from the balance wheel to our custom cases. Each part undergoes rigorous testing to ensure that when assembled, the watch operates with chronometric accuracy and will endure for centuries.
                </p>
                <p class="fs-5 text-secondary mb-0">
                    Our commitment to chronometric standards, time-honored traditions, and uncompromising artisanal excellence defines our brand and separates us from the rest of the horological world.
                </p>
            </div>
        </div>
    </div>

    <!-- PHILOSOPHY SECTION -->
    <section class="bg-black py-5 mt-5">
        <div class="container">
            <h2 class="display-3 fw-bold text-white text-center mb-5">Our Philosophy</h2>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                    <div class="text-center">
                        <div class="border border-2 border-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 100px; height: 100px;">
                            <span class="display-5 fw-bold text-white">I</span>
                        </div>
                        <h3 class="fs-4 fw-bold text-white mb-3">Independence</h3>
                        <p class="fs-6 text-secondary">As an independent luxury watch manufacturer, we remain free from corporate pressures. This independence allows us to maintain our uncompromising standards and create timepieces for collectors, not shareholders.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
                    <div class="text-center">
                        <div class="border border-2 border-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 100px; height: 100px;">
                            <span class="display-5 fw-bold text-white">II</span>
                        </div>
                        <h3 class="fs-4 fw-bold text-white mb-3">Innovation</h3>
                        <p class="fs-6 text-secondary">We honor tradition while pushing the boundaries of possibility. Our craftsmen continuously explore new materials and techniques, positioning Horologe at the forefront of horological achievement.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <div class="border border-2 border-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 100px; height: 100px;">
                            <span class="display-5 fw-bold text-white">III</span>
                        </div>
                        <h3 class="fs-4 fw-bold text-white mb-3">Integrity</h3>
                        <p class="fs-6 text-secondary">Every timepiece that leaves our atelier carries the uncompromising standards that define our legacy. We believe that true luxury is built on a foundation of integrity and transparency.</p>
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