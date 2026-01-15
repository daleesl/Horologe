<div class="d-none d-md-flex flex-column flex-shrink-0 p-3 text-white bg-dark position-sticky top-0"
     style="width: 280px; height: 100vh; overflow-y: auto;">
    <a href="adminDashboard.php" class="d-flex align-items-center mb-3 me-md-auto text-white text-decoration-none">
        <span class="fs-4">HOROLOGE</span>
    </a>
    <p class="text-secondary small mb-4">Admin Panel</p>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="adminDashboard.php" class="nav-link text-white" aria-current="page">
                <i class="bi bi-speedometer2 me-2"></i> DASHBOARD
            </a>
        </li>
        <li>
            <a href="products.php" class="nav-link text-white">
                <i class="bi bi-box-seam me-2"></i> PRODUCTS
            </a>
        </li>
        <li>
            <a href="users.php" class="nav-link text-white">
                <i class="bi bi-people me-2"></i> CUSTOMERS
            </a>
        </li>
        <li>
            <a href="orders.php" class="nav-link text-white">
                <i class="bi bi-bag-check me-2"></i> ORDERS
            </a>
        </li>
        <li>
            <a href="sms.php" class="nav-link text-white">
                <i class="bi bi-chat-dots me-2"></i> SMS INBOX
            </a>
        </li>
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
           id="dropdownUserDesktop" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle me-2 fs-5"></i>
            <strong>Account</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUserDesktop">
            <li><a href="#" class="dropdown-item" id="logoutBtn">Sign out</a></li>
        </ul>
    </div>
</div>

<div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="adminSidebarOffcanvas"
     aria-labelledby="adminSidebarOffcanvasLabel" style="width: 280px;">
    <div class="offcanvas-header border-bottom border-secondary">
        <h5 class="offcanvas-title" id="adminSidebarOffcanvasLabel">HOROLOGE</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="p-3">
            <p class="text-secondary small mb-3">Admin Panel</p>
            <ul class="nav nav-pills flex-column mb-4">
                <li class="nav-item">
                    <a href="adminDashboard.php" class="nav-link text-white" aria-current="page"
                       data-bs-dismiss="offcanvas">
                        <i class="bi bi-speedometer2 me-2"></i> DASHBOARD
                    </a>
                </li>
                <li>
                    <a href="products.php" class="nav-link text-white" data-bs-dismiss="offcanvas">
                        <i class="bi bi-box-seam me-2"></i> PRODUCTS
                    </a>
                </li>
                <li>
                    <a href="users.php" class="nav-link text-white" data-bs-dismiss="offcanvas">
                        <i class="bi bi-people me-2"></i> CUSTOMERS
                    </a>
                </li>
                <li>
                    <a href="orders.php" class="nav-link text-white" data-bs-dismiss="offcanvas">
                        <i class="bi bi-bag-check me-2"></i> ORDERS
                    </a>
                </li>
                <li>
                    <a href="sms.php" class="nav-link text-white" data-bs-dismiss="offcanvas">
                        <i class="bi bi-chat-dots me-2"></i> SMS INBOX
                    </a>
                </li>
            </ul>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                   id="dropdownUserMobile" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-2 fs-5"></i>
                    <strong>Account</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUserMobile">
                    <li><a href="#" class="dropdown-item" id="logoutBtnMobile">Sign out</a></li>
                </ul>
            </div>

            <form id="logoutForm" method="post" action="../auth/sign-in.php" style="display:none;">
                <input type="hidden" name="logout" value="1">
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">Confirm Sign Out</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to sign out?</p>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmLogoutBtn">Yes, Sign Out</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const logoutForm = document.getElementById('logoutForm');
    const logoutModalEl = document.getElementById('logoutModal');
    const logoutModal = new bootstrap.Modal(logoutModalEl);

    const logoutBtns = [document.getElementById('logoutBtn'), document.getElementById('logoutBtnMobile')];
    const confirmLogoutBtn = document.getElementById('confirmLogoutBtn');

    logoutBtns.forEach(btn => {
        btn?.addEventListener('click', function (e) {
            e.preventDefault();
            logoutModal.show();
        });
    });

    confirmLogoutBtn.addEventListener('click', function () {
        logoutForm.submit();
    });
});
</script>