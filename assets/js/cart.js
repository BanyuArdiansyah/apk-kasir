// Cart page functionality
document.addEventListener('DOMContentLoaded', function() {
    displayCartItems();
    setupCartEventListeners();
});

// Display cart items
function displayCartItems() {
    const cartItemsContainer = document.getElementById('cartItems');
    const cartSummary = document.getElementById('cartSummary');
    
    if (!cartItemsContainer) return;
    
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p>Keranjang belanja kosong</p>';
        if (cartSummary) cartSummary.style.display = 'none';
        return;
    }
    
    // --- FIX: Show the cart summary when there are items ---
    if (cartSummary) cartSummary.style.display = 'block';
    
    cartItemsContainer.innerHTML = '';
    
    let totalAmount = 0;
    
    cart.forEach(item => {
        const cartItem = createCartItemElement(item);
        cartItemsContainer.appendChild(cartItem);
        totalAmount += item.subtotal;
    });
    
    updateCartSummary(totalAmount);
}

// Create cart item element
function createCartItemElement(item) {
    const cartItem = document.createElement('div');
    cartItem.className = 'cart-item';
    cartItem.innerHTML = `
        <div class="item-info">
            <div class="item-name">${item.nama}</div>
            <div class="item-price">Rp ${formatNumber(item.harga)}</div>
        </div>
        <div class="item-controls">
            <div class="quantity-controls">
                <button class="quantity-btn minus" data-id="${item.id}">-</button>
                <span class="quantity">${item.quantity}</span>
                <button class="quantity-btn plus" data-id="${item.id}">+</button>
            </div>
            <div class="item-subtotal">Rp ${formatNumber(item.subtotal)}</div>
            <button class="remove-btn" data-id="${item.id}">Hapus</button>
        </div>
    `;
    
    return cartItem;
}

// Update cart summary
function updateCartSummary(totalAmount) {
    const totalElement = document.getElementById('cartTotal');
    if (totalElement) {
        totalElement.textContent = `Rp ${formatNumber(totalAmount)}`;
    }
}

// Setup cart event listeners
function setupCartEventListeners() {
    // Quantity buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('quantity-btn')) {
            const productId = parseInt(e.target.getAttribute('data-id'));
            const isPlus = e.target.classList.contains('plus');
            const item = cart.find(item => item.id === productId);
            
            if (item) {
                const newQuantity = isPlus ? item.quantity + 1 : item.quantity - 1;
                updateQuantity(productId, newQuantity);
            }
        }
        
        // Remove buttons
        if (e.target.classList.contains('remove-btn')) {
            const productId = parseInt(e.target.getAttribute('data-id'));
            removeFromCart(productId);
        }
    });
    
    // Checkout button
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function() {
            if (cart.length === 0) {
                alert('Keranjang belanja kosong!');
                return;
            }
            window.location.href = 'checkout.php';
        });
    }
}