// Global variables
let cart = JSON.parse(localStorage.getItem('cart')) || [];

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// Initialize application
function initializeApp() {
    loadProducts();
    updateCartCount();
    setupEventListeners();
}

// Setup event listeners
function setupEventListeners() {
    // Navigation
    const loginBtn = document.getElementById('loginBtn');
    if (loginBtn) {
        loginBtn.addEventListener('click', function() {
            window.location.href = 'login.php';
        });
    }
    
    // Modal functionality
    const modal = document.getElementById('modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
        
        const closeBtn = document.querySelector('.close-modal');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }
    }
}

// Load products from API
function loadProducts() {
    fetch('api/produk.php')
        .then(response => response.json())
        .then(products => {
            displayProducts(products);
        })
        .catch(error => {
            console.error('Error loading products:', error);
        });
}

// Display products in grid
function displayProducts(products) {
    const productsGrid = document.getElementById('productsGrid');
    if (!productsGrid) return;
    
    productsGrid.innerHTML = '';
    
    products.forEach(product => {
        const productCard = createProductCard(product);
        productsGrid.appendChild(productCard);
    });
}

// Create product card HTML
function createProductCard(product) {
    const card = document.createElement('div');
    card.className = 'product-card';
    
    card.innerHTML = `
        <div class="product-image">
            ${product.gambar ? `<img src="${product.gambar}" alt="${product.nama}" style="width:100%;height:100%;object-fit:cover;">` : 'No Image'}
        </div>
        <div class="product-info">
            <h3 class="product-name">${product.nama}</h3>
            <div class="product-price">Rp ${formatNumber(product.harga)}</div>
            <div class="product-stock">Stok: ${product.stok}</div>
            <button class="add-to-cart-btn" data-id="${product.id}" ${product.stok <= 0 ? 'disabled' : ''}>
                ${product.stok <= 0 ? 'Stok Habis' : 'Tambah ke Keranjang'}
            </button>
        </div>
    `;
    
    // Add event listener to add to cart button
    const addToCartBtn = card.querySelector('.add-to-cart-btn');
    if (addToCartBtn && product.stok > 0) {
        addToCartBtn.addEventListener('click', function() {
            addToCart(product);
        });
    }
    
    return card;
}

// Add product to cart
function addToCart(product) {
    const existingItem = cart.find(item => item.id === product.id);
    
    if (existingItem) {
        if (existingItem.quantity >= product.stok) {
            alert('Stok tidak mencukupi!');
            return;
        }
        existingItem.quantity += 1;
        existingItem.subtotal = existingItem.quantity * existingItem.harga;
    } else {
        cart.push({
            id: product.id,
            nama: product.nama,
            harga: product.harga,
            quantity: 1,
            subtotal: product.harga
        });
    }
    
    saveCart();
    updateCartCount();
    showNotification('Produk ditambahkan ke keranjang');
}

// Remove item from cart
function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    saveCart();
    updateCartCount();
    
    // If we're on the cart page, refresh the display
    if (window.location.pathname.includes('cart.php')) {
        displayCartItems();
    }
}

// Update item quantity in cart
function updateQuantity(productId, newQuantity) {
    if (newQuantity <= 0) {
        removeFromCart(productId);
        return;
    }
    
    const item = cart.find(item => item.id === productId);
    if (item) {
        // Check stock availability (would need to fetch current stock from API in real app)
        item.quantity = newQuantity;
        item.subtotal = item.quantity * item.harga;
        saveCart();
        
        // If we're on the cart page, refresh the display
        if (window.location.pathname.includes('cart.php')) {
            displayCartItems();
        }
    }
}

// Save cart to localStorage
function saveCart() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

// Update cart count in navigation
function updateCartCount() {
    const cartCount = document.getElementById('cartCount');
    if (cartCount) {
        const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
        cartCount.textContent = totalItems;
        cartCount.style.display = totalItems > 0 ? 'inline' : 'none';
    }
}

// Show notification
function showNotification(message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background-color: ${type === 'success' ? '#28a745' : '#dc3545'};
        color: white;
        border-radius: 5px;
        z-index: 1000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    `;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Format number with thousand separators
function formatNumber(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

// Open modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

// Close modal
function closeModal() {
    const modal = document.getElementById('modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}