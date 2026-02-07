<!-- Carrinho Offcanvas (Gaveta Lateral) -->
<div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel" style="border-left: 1px solid var(--border-color); width: 400px; backdrop-filter: blur(10px); background: rgba(10, 10, 15, 0.95);">
  <div class="offcanvas-header border-bottom border-white border-opacity-10">
    <h5 class="offcanvas-title archivofont text-uppercase" id="cartOffcanvasLabel" style="letter-spacing: 1px; color: var(--color-accent);">
      🛒 Seu Arsenal
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  
  <div class="offcanvas-body d-flex flex-column p-0">
    <!-- Lista de Itens -->
    <div id="cartItemsContainer" class="flex-grow-1 overflow-auto p-3 d-flex flex-column gap-3">
        <!-- Renderizado via JS -->
    </div>

    <!-- Rodapé do Carrinho -->
    <div class="p-3 border-top border-white border-opacity-10 bg-black bg-opacity-25">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-white-50">Total Estimado</span>
            <span class="h4 archivofont text-white mb-0" id="cartTotalMain">R$ 0,00</span>
        </div>
        <button onclick="window.location.href='checkout.php'" id="btnFinalizar" class="btn btn-custom w-100 py-3 fw-bold text-uppercase d-flex justify-content-between align-items-center mb-2">
            <span>Finalizar Compra</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </button>
        <button onclick="cartManager.clear()" class="btn btn-sm text-white-50 w-100 hover-white">
            Esvaziar Carrinho
        </button>
    </div>
  </div>
</div>

<!-- Template de Item (Hidden) -->
<template id="cartItemTemplate">
    <div class="cart-item d-flex gap-3 align-items-center p-3 rounded-3 position-relative" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.05);">
        <img src="" class="item-img rounded" style="width: 60px; height: 60px; object-fit: contain; background: rgba(0,0,0,0.3);">
        <div class="flex-grow-1">
            <h6 class="item-title text-white mb-1 small fw-bold text-truncate" style="max-width: 180px;">Nome do Produto</h6>
            <div class="text-accent fw-bold small item-price">R$ 0,00</div>
        </div>
        <div class="d-flex flex-column align-items-end gap-1">
            <button class="btn-remove btn btn-sm p-0 text-white-50 hover-danger" title="Remover">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            <div class="d-flex align-items-center gap-2 bg-dark rounded px-2 py-1 border border-white border-opacity-10">
                <button class="btn-qty btn-minus text-white p-0 border-0 bg-transparent">-</button>
                <small class="item-qty text-white fw-mono">1</small>
                <button class="btn-qty btn-plus text-white p-0 border-0 bg-transparent">+</button>
            </div>
        </div>
    </div>
</template>

<style>
    .hover-white:hover { color: #fff !important; }
    .hover-danger:hover { color: #ff0055 !important; }
    .btn-qty { width: 20px; text-align: center; font-weight: bold; font-family: monospace; cursor: pointer; }
    .fw-mono { font-family: monospace; }
</style>

<script>
window.cartManager = {
    offcanvas: null,

    init: function() {
        console.log('CartManager: Initializing...');
        const el = document.getElementById('cartOffcanvas');
        if(el) {
            this.offcanvas = new bootstrap.Offcanvas(el);
            console.log('CartManager: Offcanvas found');
        } else {
            console.error('CartManager: Offcanvas NOT found');
        }
        this.updateView();
    },

    open: function() {
        this.updateView();
        this.offcanvas?.show();
    },

    api: async function(action, data = {}) {
        const formData = new FormData();
        formData.append('action', action);
        for (const k in data) formData.append(k, data[k]);

        try {
            const res = await fetch('api/cart.php', { method: 'POST', body: formData });
            const json = await res.json();
            return json;
        } catch (e) {
            console.error('Cart API Error:', e);
            return { success: false };
        }
    },

    add: async function(id, qty = 1) {
        console.log('Adding product:', id);
        const res = await this.api('add', { id, qty });
        if (res.success) {
            this.render(res.cart);
            this.open(); // Auto open on add
        } else {
            alert('Erro ao adicionar ao carrinho. Tente novamente.');
        }
    },

    remove: async function(id) {
        const res = await this.api('remove', { id });
        if (res.success) this.render(res.cart);
    },

    update: async function(id, qty) {
        const res = await this.api('update', { id, qty });
        if (res.success) this.render(res.cart);
    },
    
    clear: async function() {
        if(!confirm('Limpar todo o carrinho?')) return;
        const res = await this.api('clear');
        if (res.success) {
             this.updateView(); // Will show empty state
        }
    },

    updateView: async function() {
        const res = await this.api('get');
        if (res.success) this.render(res.cart);
    },

    render: function(cart) {
        const container = document.getElementById('cartItemsContainer');
        const totalEl = document.getElementById('cartTotalMain');
        const badge = document.getElementById('cartCountBadge');
        const template = document.getElementById('cartItemTemplate');

        // Update Badge
        if (badge) {
            badge.innerText = cart.count;
            badge.style.display = cart.count > 0 ? 'inline-block' : 'none';
        }

        // Update Total
        if(totalEl) totalEl.innerText = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(cart.total_cents / 100);

        // Render Items
        if(container) {
            container.innerHTML = '';

            if (cart.items.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5 opacity-50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-3"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                        <p>Seu carrinho está vazio.</p>
                    </div>
                `;
                const btn = document.getElementById('btnFinalizar');
                if(btn) btn.disabled = true;
                return;
            }

            const btn = document.getElementById('btnFinalizar');
            if(btn) btn.disabled = false;

            cart.items.forEach(item => {
                const clone = template.content.cloneNode(true);
                const imgUrl = item.image_url || 'assets/keys/glock/glock.png'; 
                
                clone.querySelector('.item-title').innerText = item.title;
                clone.querySelector('.item-price').innerText = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(item.price / 100);
                clone.querySelector('.item-img').src = imgUrl;
                clone.querySelector('.item-qty').innerText = item.qty;
                
                // Events
                clone.querySelector('.btn-plus').onclick = () => this.update(item.id, item.qty + 1);
                clone.querySelector('.btn-minus').onclick = () => this.update(item.id, item.qty - 1);
                clone.querySelector('.btn-remove').onclick = () => this.remove(item.id);

                container.appendChild(clone);
            });
        }
    }
};

// Auto init
document.addEventListener('DOMContentLoaded', () => window.cartManager.init());
</script>
