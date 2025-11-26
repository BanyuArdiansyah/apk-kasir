// checkout.js (fixed)
// Pastikan file ini dimuat setelah main.js dan DOM siap
document.addEventListener('DOMContentLoaded', function() {
    // Pastikan cart tersedia (ambil dari localStorage kalau belum ada)
    window.cart = window.cart || JSON.parse(localStorage.getItem('cart') || '[]');

    displayCheckoutSummary();
    setupCheckoutEventListeners();
    calculateInitialTotal();
    updatePayButtonState(); // pastikan state awal benar
});

/* -------------------------
   Utils kecil
   ------------------------- */
function safeNumber(v) {
    const n = typeof v === 'number' ? v : parseFloat(v);
    return isNaN(n) ? 0 : n;
}

function formatNumberId(num, opts = {}) {
    // default tanpa desimal (kamu ingin bulat untuk uang tunai)
    return new Intl.NumberFormat('id-ID', opts).format(Math.round(safeNumber(num)));
}

/* -------------------------
   Render ringkasan checkout
   ------------------------- */
function displayCheckoutSummary() {
    const checkoutItems = document.getElementById('checkoutItems');
    const checkoutTotal = document.getElementById('checkoutTotal');

    if (!checkoutItems) return;

    checkoutItems.innerHTML = '';
    let totalAmount = 0;

    // Pastikan cart adalah array
    window.cart = Array.isArray(window.cart) ? window.cart : [];

    if (window.cart.length === 0) {
        checkoutItems.innerHTML = '<p>Keranjang kosong</p>';
    }

    window.cart.forEach(item => {
        const qty = safeNumber(item.quantity);
        const subtotal = safeNumber(item.subtotal);
        const checkoutItem = document.createElement('div');
        checkoutItem.className = 'summary-row';
        checkoutItem.innerHTML = `
            <span>${item.nama} x ${qty}</span>
            <span>Rp ${formatNumberId(subtotal)}</span>
        `;
        checkoutItems.appendChild(checkoutItem);
        totalAmount += subtotal;
    });

    if (checkoutTotal) {
        checkoutTotal.textContent = `Rp ${formatNumberId(totalAmount)}`;
    }

    // Set total amount in hidden field for form submission (plain number, two decimals optional)
    const totalInput = document.getElementById('totalAmount');
    if (totalInput) {
        // Simpan sebagai angka (tanpa koma karena kita round saat menyimpan di server)
        totalInput.value = Math.round(totalAmount).toString();
    }

    updatePayButtonState();
}

/* -------------------------
   Kalkulasi awal & helper
   ------------------------- */
function calculateInitialTotal() {
    const totalInput = document.getElementById('totalAmount');
    const bayarInput = document.getElementById('bayar');
    const kembalianInput = document.getElementById('kembalian');

    if (!totalInput || !bayarInput || !kembalianInput) return;

    const total = safeNumber(totalInput.value);
    const bayar = Math.round(safeNumber(bayarInput.value));
    const kembalian = bayar - total;

    kembalianInput.value = kembalian >= 0 ? Math.round(kembalian) : '0';
    updatePayButtonState();
}

function updatePayButtonState() {
    const totalInput = document.getElementById('totalAmount');
    const bayarInput = document.getElementById('bayar');
    const payBtn = document.getElementById('payBtn');

    if (!payBtn) return;
    const total = totalInput ? safeNumber(totalInput.value) : 0;
    const bayar = bayarInput ? Math.round(safeNumber(bayarInput.value)) : 0;

    // disable jika bayar < total atau cart kosong
    const cartEmpty = !Array.isArray(window.cart) || window.cart.length === 0;
    payBtn.disabled = (bayar < total) || cartEmpty;

    if (payBtn.disabled) {
        payBtn.title = cartEmpty ? 'Keranjang kosong' : 'Jumlah pembayaran kurang dari total';
    } else {
        payBtn.title = 'Klik untuk memproses pembayaran';
    }
}

/* -------------------------
   Event listeners setup
   ------------------------- */
function setupCheckoutEventListeners() {
    const payBtn = document.getElementById('payBtn');
    if (payBtn) {
        payBtn.addEventListener('click', function(e) {
            e.preventDefault();
            processPayment();
        });
    }

    const bayarInput = document.getElementById('bayar');
    if (bayarInput) {
        bayarInput.addEventListener('input', calculateChange);
        bayarInput.addEventListener('blur', formatPaymentInput);
    }
}

/* -------------------------
   Formatting input pembayaran
   ------------------------- */
function formatPaymentInput() {
    const bayarInput = document.getElementById('bayar');
    if (!bayarInput) return;
    const value = safeNumber(bayarInput.value);
    bayarInput.value = Math.round(value);
    calculateChange();
}

/* -------------------------
   Kalkulasi kembalian
   ------------------------- */
function calculateChange() {
    const totalInput = document.getElementById('totalAmount');
    const bayarInput = document.getElementById('bayar');
    const kembalianInput = document.getElementById('kembalian');

    if (!totalInput || !bayarInput || !kembalianInput) return;

    const total = safeNumber(totalInput.value);
    let bayar = Math.round(safeNumber(bayarInput.value));
    const kembalian = bayar - total;

    kembalianInput.value = kembalian >= 0 ? Math.round(kembalian) : '0';

    // UI feedback
    if (kembalian < 0) {
        bayarInput.classList.add('payment-error');
    } else {
        bayarInput.classList.remove('payment-error');
    }

    updatePayButtonState();
}

/* -------------------------
   Proses pembayaran (AJAX)
   ------------------------- */
function processPayment() {
    const form = document.getElementById('checkoutForm');
    if (!form) return;

    const total = safeNumber(document.getElementById('totalAmount').value);
    const bayar = Math.round(safeNumber(document.getElementById('bayar').value));
    const kembalian = Math.round(safeNumber(document.getElementById('kembalian').value));

    if (total <= 0) {
        alert('Total tidak valid!');
        return;
    }
    if (bayar < total) {
        alert('Jumlah pembayaran kurang dari total!');
        return;
    }
    if (!Array.isArray(window.cart) || window.cart.length === 0) {
        alert('Keranjang kosong!');
        return;
    }

    const formData = new FormData(form);
    formData.set('total', total); // pastikan total dikirim
    formData.set('bayar', bayar);
    formData.set('kembalian', kembalian);
    formData.set('cart', JSON.stringify(window.cart));

    const payBtn = document.getElementById('payBtn');
    const originalText = payBtn ? payBtn.textContent : '';
    if (payBtn) {
        payBtn.textContent = 'Memproses...';
        payBtn.disabled = true;
    }

    fetch('api/checkout.php', {
        method: 'POST',
        body: formData
    })
    .then(async response => {
        const text = await response.text();
        // coba parse JSON, kalau gagal tampilkan error server (berguna saat server balikin HTML)
        try {
            const data = JSON.parse(text);
            if (!response.ok) throw new Error(data.message || 'Server error');
            return data;
        } catch (err) {
            throw new Error('Invalid JSON from server: ' + text);
        }
    })
    .then(data => {
        if (data.success) {
            // clear cart
            window.cart = [];
            localStorage.removeItem('cart');
            if (typeof updateCartCount === 'function') updateCartCount();
            showReceipt(data.transaksi);
        } else {
            alert('Terjadi kesalahan: ' + (data.message || 'unknown'));
            if (payBtn) {
                payBtn.textContent = originalText;
                payBtn.disabled = false;
            }
        }
    })
    .catch(error => {
        console.error('Checkout error:', error);
        alert('Terjadi kesalahan saat memproses pembayaran: ' + error.message);
        if (payBtn) {
            payBtn.textContent = originalText;
            payBtn.disabled = false;
        }
    });
}

/* -------------------------
   Receipt modal (tetap gunakan function yang sudah ada di project)
   ------------------------- */
function showReceipt(transaksi) {
    const modalContent = document.querySelector('.modal-content');
    if (!modalContent) return;

    const formatReceiptNumber = (num) => new Intl.NumberFormat('id-ID').format(Math.round(safeNumber(num)));

    modalContent.innerHTML = `
        <div class="modal-header">
            <h2>Struk Pembayaran</h2>
            <button class="close-modal">&times;</button>
        </div>
        <div class="struk">
            <div class="struk-header">
                <h3>MIE GACOAN</h3>
                <p>Jl. Contoh No. 123</p>
            </div>
            <div class="struk-info">
                <p>No. Transaksi: ${transaksi.no_transaksi || '-'}</p>
                <p>Tanggal: ${new Date(transaksi.created_at || Date.now()).toLocaleString('id-ID')}</p>
                <p>Kasir: ${transaksi.nama_kasir || '-'}</p>
            </div>
            <div class="struk-items">
                ${(transaksi.items || []).map(item => `
                    <div class="struk-item">
                        <span>${item.nama} x ${item.qty}</span>
                        <span>Rp ${formatReceiptNumber(item.subtotal)}</span>
                    </div>
                `).join('')}
            </div>
            <div class="struk-total">
                <div class="struk-item"><span>Total:</span><span>Rp ${formatReceiptNumber(transaksi.total)}</span></div>
                <div class="struk-item"><span>Bayar:</span><span>Rp ${formatReceiptNumber(transaksi.bayar)}</span></div>
                <div class="struk-item"><span>Kembali:</span><span>Rp ${formatReceiptNumber(transaksi.kembalian)}</span></div>
            </div>
            <div class="struk-footer"><p>Terima kasih atas kunjungan Anda</p></div>
        </div>
        <div class="print-controls">
            <button class="print-btn" id="printStrukBtn">Print</button>
            <button class="cancel-btn" id="closeStrukBtn">Tutup</button>
        </div>
    `;

    // buka modal (asumsikan ada openModal)
    if (typeof openModal === 'function') openModal('modal');

    // setup tombol
    document.getElementById('closeStrukBtn')?.addEventListener('click', closeModalAndRedirect);
   document.getElementById('printStrukBtn')?.addEventListener('click', function () {

    const modalHtml = document.querySelector('.modal-content').innerHTML;

    const printWindow = window.open('', '_blank');

    printWindow.document.write(`
        <html>
        <head>
            <title>Struk Pembayaran</title>
            <style>
                body {
                    font-family: 'Courier New', monospace;
                    font-size: 12px;
                    width: 260px;
                    margin: 0 auto;
                }
                .center { text-align: center; }
                hr { border: none; border-top: 1px dashed #000; margin: 6px 0; }
                .item-row { display: flex; justify-content: space-between; }
                @page { size: auto; margin: 5mm; }
            </style>
        </head>
        <body>
            ${modalHtml}
            <script>
                window.onload = function () {
                    window.print();
                    window.close();
                };
            <\/script>
        </body>
        </html>
    `);

    printWindow.document.close();
});

}

function closeModalAndRedirect() {
    const modal = document.getElementById('modal');
    if (modal) modal.style.display = 'none';

    // Redirect kembali ke halaman kasir
    window.location.href = 'index.php'; 
}

