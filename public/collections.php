<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>The Collections - Horologe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body class="bg-black text-secondary">

    <?php include '../includes/navbar.php'; ?>

    <section class="py-5" style="padding-top: 100px;">
        <div class="container-fluid px-4 px-md-5">
            <!-- Header -->
            <div class="row mb-5 text-center">
                <div class="col-12">
                    <h1 class="display-3 fw-normal text-white mb-3">The Collections</h1>
                    <p class="fs-5 text-secondary">Explore our range of exceptional timepieces, from timeless classics to avant-garde complications.</p>
                </div>
            </div>

            <!-- Filters & Sort -->
            <div class="row mb-5 align-items-center border-bottom border-secondary pb-4">
                <div class="col-lg-8">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <span class="text-secondary fw-bold small" style="letter-spacing: 0.1rem;">
                            <i class="bi bi-funnel"></i> FILTERS
                        </span>
                        <button class="btn btn-sm btn-outline-secondary px-3">ALL</button>
                        <button class="btn btn-sm btn-outline-secondary px-3">ROLEX</button>
                        <button class="btn btn-sm btn-outline-secondary px-3">CARTIER</button>
                        <button class="btn btn-sm btn-outline-secondary px-3">AUDEMARS PIGUET</button>
                        <button class="btn btn-sm btn-outline-secondary px-3">PATEK PHILIPPE</button>

                    </div>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <div class="d-flex justify-content-lg-end align-items-center gap-2">
                        <span class="text-secondary small fw-bold" style="letter-spacing: 0.05rem;">SORT BY</span>
                        <select class="form-select form-select-sm bg-black border-secondary text-white" style="width: auto;">
                            <option selected>Featured</option>
                            <option>Price: Low to High</option>
                            <option>Price: High to Low</option>
                            <option>Newest</option>
                        </select>
                    </div>
                </div>
            </div>

        </div>


     <!-- Products Grid -->
        <div class="container">
            <div id="productsRow" class="row g-3 g-lg-5">
                <!-- Products will be loaded dynamically -->
            </div>
        </div>
    
    </section>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    
    <script src="../assets/js/sample-products.js"></script>
    <script>
        var productsRow = document.getElementById('productsRow');

        for (var i = 0; i < products.length; i++) {
          productsRow.innerHTML += `
           <div class="col-12 col-sm-6 col-lg-3">
                    <div class="text-center border border-secondary rounded-3 p-3">
                        <div class="mb-3 overflow-hidden rounded ratio ratio-1x1">
                            <img src="${products[i].image}" alt="${products[i].name}" class="w-100 h-100 object-fit-contain p-3">
                        </div>
                        <p class="text-secondary small mb-2 fw-bold text-uppercase">${products[i].category}</p>
                        <h5 class="text-white mb-3 fw-normal">${products[i].name}</h5>
                        <p class="text-white fw-bold">$${products[i].price.toLocaleString()}</p>
                        <button class="btn btn-sm btn-outline-light px-4 mb-3">ADD TO CART</button>
                    </div>
                </div>`;
        }
    </script>
</body>

</html>
