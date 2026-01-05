// Cart Management Functions
// temporary for front end only will be migrated using php later on


/**
 * Get all cart items from localStorage
 * @returns {Array} 
 */
function getCart() {
    return JSON.parse(localStorage.getItem('cart')) || [];
}


 // Save cart to localStorage
 
function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
}


//Add item to cart or update quantity if exists

function addToCart(product, quantity) {
    let cart = getCart();
    let existingItem = cart.find(item => item.id === product.id);
    
    if (existingItem) {
        existingItem.quantity += parseInt(quantity);
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.image,
            category: product.category,
            quantity: parseInt(quantity),
            checked: false
        });
    }
    
    saveCart(cart);
    updateCartCountDisplay();
    showCartNotification(`${product.name} added to cart!`);
}


 // Remove item from cart
 
 
function removeFromCart(productId) {
    let cart = getCart();
    cart = cart.filter(item => item.id !== productId);
    saveCart(cart);
    updateCartCountDisplay(); // Update navbar badge
}


// Update quantity of item in cart

function updateQuantity(productId, quantity) {
    let cart = getCart();
    let item = cart.find(p => p.id === productId);
    
    if (item) {
        if (quantity > 0) {
            item.quantity = parseInt(quantity);
            saveCart(cart);
        } else {
            removeFromCart(productId);
        }
    }
}


// Get total number of items in cart

function getCartCount() {
    let cart = getCart();
    return cart.reduce((sum, item) => sum + item.quantity, 0);
}


// Get total count of unique products in cart

function getCartItemCount() {
    return getCart().length;
}


// Get total price of cart

function getCartTotal() {
    let cart = getCart();
    return cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
}


// Get total price of checked items (selected for checkout)

function getCartSubtotal() {
    let cart = getCart();
    return cart
        .filter(item => item.checked)
        .reduce((sum, item) => sum + (item.price * item.quantity), 0);
}


// Get number of checked items

function getCheckedItemCount() {
    let cart = getCart();
    return cart.filter(item => item.checked).length;
}


// Clear entire cart

function clearCart() {
    localStorage.removeItem('cart');
    updateCartCountDisplay(); // Update navbar badge
}


 // Toggle product checked status

function toggleProductCheck(productId) {
    let cart = getCart();
    let item = cart.find(p => p.id === productId);
    
    if (item) {
        item.checked = !item.checked;
        saveCart(cart);
    }
}


// Check if product exists in cart

function isInCart(productId) {
    let cart = getCart();
    return cart.some(item => item.id === productId);
}


// Format price to USD currency

function formatPrice(price) {
    return '$' + parseFloat(price).toLocaleString('en-US', {
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


function updateCartCountDisplay() {
    const cartBadge = document.getElementById('cartBadge');
    const cartCountSpan = document.getElementById('cartCount');
    const count = getCartItemCount();
    
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

// Update cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCountDisplay();
});
