// Resolve a stable base for cart endpoints regardless of current page depth.
function getCartApiBase() {
    // Prefer the path of the loaded cart.js file so we know where the repo root sits.
    const scripts = document.getElementsByTagName('script');
    for (const s of scripts) {
        if (s.src && s.src.includes('/assets/js/cart.js')) {
            return s.src.replace(/\/assets\/js\/cart\.js.*$/, '/');
        }
    }
    // Fallback: strip off everything after /public/ so /actions is reachable.
    const publicIndex = window.location.pathname.indexOf('/public/');
    if (publicIndex !== -1) {
        return window.location.origin + window.location.pathname.substring(0, publicIndex + 1);
    }
    return window.location.origin + '/';
}

const CART_API_BASE = getCartApiBase();

// Cart Management Functions (server-backed via PHP actions)

async function cartRequest(endpoint, data = null) {
    const url = new URL(`actions/cart/${endpoint}`, CART_API_BASE).toString();
    const options = {
        method: data ? 'POST' : 'GET',
        headers: data ? { 'Content-Type': 'application/x-www-form-urlencoded' } : {},
        body: data ? new URLSearchParams(data) : undefined,
        credentials: 'same-origin',
    };

    let res;
    let text;

    try {
        res = await fetch(url, options);
        text = await res.text();
    } catch (err) {
        console.error('Cart request network error', err);
        throw new Error('Network error while reaching cart');
    }

    let dataJson = {};
    try {
        dataJson = text ? JSON.parse(text) : {};
    } catch (err) {
        console.error('Cart request parse error', { url, text, err });
        throw new Error('Unexpected response from cart');
    }

    if (!res.ok || (dataJson && dataJson.error)) {
        throw new Error((dataJson && dataJson.error) || `Cart request failed (${res.status})`);
    }

    return dataJson;
}

// Add item to cart (server session)
async function addToCart(product, quantity) {
    const res = await cartRequest('add.php', {
        product_id: product.id,
        quantity: quantity
    });
    if (!res || res.error) {
        throw new Error(res ? res.error : 'Unknown error');
    }
    await updateCartCountDisplay();
    showCartNotification(`${product.name} added to cart!`);
}

// Remove item from cart
async function removeFromCart(productId) {
    const res = await cartRequest('remove.php', { product_id: productId });
    if (res && res.error) {
        throw new Error(res.error);
    }
    await updateCartCountDisplay();
}

// Update quantity
async function updateQuantity(productId, quantity) {
    const res = await cartRequest('update.php', { product_id: productId, quantity });
    if (res && res.error) {
        throw new Error(res.error);
    }
    await updateCartCountDisplay();
}

// Get summary (items/unique/subtotal)
async function getCartSummary() {
    const data = await cartRequest('summary.php');
    return data && data.summary ? data.summary : { items: 0, unique: 0, subtotal: 0 };
}

// Update navbar badge from server summary
async function updateCartCountDisplay() {
    const cartBadge = document.getElementById('cartBadge');
    const cartCountSpan = document.getElementById('cartCount');
    const summary = await getCartSummary();
    const count = summary.unique || 0;

    if (cartCountSpan) {
        cartCountSpan.textContent = count;
    }
    if (cartBadge) {
        if (count > 0) {
            cartBadge.style.display = 'inline-block';
        } else {
            cartBadge.style.display = 'none';
        }
    }
}

// Format price to USD currency

function formatPrice(price) {
    return '₱' + parseFloat(price).toLocaleString('en-PH', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    });
}


 // Show notification

function showCartNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'alert alert-info position-fixed bottom-0 end-0 m-3';
    notification.style.zIndex = '9999';
    notification.textContent = message;
    notification.style.minWidth = '300px';
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}


// Update cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCountDisplay();
    attachAddToCartButtons();
});

// Build a product object from data attributes on an add-to-cart button
function buildProductFromDataset(button) {
    const price = parseFloat(button.dataset.productPrice);
    return {
        id: (button.dataset.productId || '').toString().trim(),
        name: button.dataset.productName || '',
        price: Number.isNaN(price) ? 0 : price,
        image: button.dataset.productImage || '',
        category: button.dataset.productCategory || ''
    };
}

// Attach add-to-cart listeners for any button with data-product-* attributes
function attachAddToCartButtons() {
    const buttons = document.querySelectorAll('.add-to-cart-btn[data-product-id]');
    buttons.forEach(btn => {
        btn.addEventListener('click', async function(event) {
            event.preventDefault();
            const product = buildProductFromDataset(btn);
            if (!product.id) {
                return;
            }
            const quantity = parseInt(btn.dataset.productQuantity || '1', 10) || 1;
            try {
                await addToCart(product, quantity);
                const originalLabel = btn.dataset.restoreLabel || btn.textContent || 'ADD TO CART';
                btn.textContent = 'ADDED!';
                btn.classList.add('disabled');
                setTimeout(() => {
                    btn.textContent = originalLabel;
                    btn.classList.remove('disabled');
                }, 2000);
            } catch (err) {
                console.error('Add to cart failed', err);
                showCartNotification('Unable to add to cart right now.');
            }
        });
    });
}
