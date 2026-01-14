</nav>
<!-- AOS Animation Library -->
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
        AOS.init({
            duration: 1000,
            easing: 'ease-out-quart',
            offset: 120,
            once: true,
            mirror: false
        });
    </script>
<nav class="navbar navbar-dark bg-black sticky-top" style="z-index: 1030;">
    <div class="container-fluid position-relative px-4 py-3 border-bottom border-secondary d-flex align-items-center">
        <button class="navbar-toggler d-md-none me-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarLinks"
            aria-controls="navbarLinks" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Mobile brand -->
        <a class="navbar-brand d-lg-none me-auto text-white font-primary" href="index.php">HOROLOGE</a>

        <!-- Desktop centered brand -->
        <a class="navbar-brand position-absolute top-50 start-50 translate-middle d-none d-lg-block" href="index.php">
            <span class="display-6 header font-primary text-white">HOROLOGE</span>
        </a>

        <!-- Right icons -->
        <div class="d-flex align-items-center ms-auto">
<<<<<<< HEAD
=======
            </a>
>>>>>>> 405430d (remove search)
            <a href="cart.php" class="text-decoration-none icon-link px-3 position-relative" aria-label="Bag">
                <i class="bi bi-bag text-white"></i>
                <span id="cartBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                    <span id="cartCount">0</span>
                </span>
            </a>
            <a href="account.php" class="text-decoration-none icon-link px-3" aria-label="Account">
                <i class="bi bi-person text-white"></i>
            </a>
        </div>
    </div>

    <!-- Bottom row: desktop/tablet always visible -->
    <div class="w-100 border-secondary d-none d-md-block align-items-center">
        <div class="container-fluid px-4 py-2 d-flex justify-content-center">
            <a class="nav-link text-secondary  p-0 mx-4" href="index.php">HOME</a>
            <a class="nav-link text-secondary  p-0 mx-4" href="collections.php">COLLECTIONS</a>
            <a class="nav-link text-secondary  p-0 mx-4" href="heritage.php">HERITAGE</a>
        </div>
    </div>

    <!--mobile collapsible -->
    <div class="w-100 border-top border-secondary d-md-none">
        <div class="container-fluid">
            <div class="collapse" id="navbarLinks">
                <ul class="navbar-nav justify-content-center py-2 mx-auto">
                    <li class="nav-item mx-2">
                        <a class="nav-link text-secondary small" href="index.php">HOME</a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link text-secondary small" href="collections.php">COLLECTIONS</a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link text-secondary small" href="heritage.php">HERITAGE</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>