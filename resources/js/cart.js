// === CART.JS ===
// Giỏ hàng lưu tạm ở localStorage
let cart = JSON.parse(localStorage.getItem('cart') || '{}');

// Hàm lưu lại giỏ hàng
function saveCart() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

// Hàm thêm vào giỏ hàng
function addToCart(variant) {
    const variantId = variant.id;

    if (variant.stock <= 0) {
        alert("Biến thể này đã hết hàng!");
        return;
    }

    if (cart[variantId]) {
        cart[variantId].quantity++;
    } else {
        cart[variantId] = {
            id: variant.id,
            color: variant.color,
            size: variant.size,
            price: variant.price,
            stock: variant.stock,
            quantity: 1,
            product_name: variant.product_name
        };
    }

    saveCart();
    alert("Đã thêm vào giỏ hàng!");
    renderCart();
}

// Hàm hiển thị giỏ hàng (trang cart.blade.php)
function renderCart() {
    const container = document.getElementById('cartContainer');
    if (!container) return; // nếu không ở trang giỏ hàng thì bỏ qua

    container.innerHTML = '';
    const items = Object.values(cart);

    if (items.length === 0) {
        container.innerHTML = '<p>Giỏ hàng trống.</p>';
        return;
    }

    items.forEach(item => {
        container.innerHTML += `
            <div class="border-bottom py-2">
                <strong>${item.product_name}</strong><br>
                <span>${item.color} / ${item.size}</span><br>
                <small>Số lượng: ${item.quantity}</small><br>
                <small>Giá: ${item.price.toLocaleString()} đ</small>
            </div>
        `;
    });
}

// Hàm load giỏ hàng khi mở trang cart
document.addEventListener('DOMContentLoaded', renderCart);
