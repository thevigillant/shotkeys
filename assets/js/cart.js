/**
 * ShotKeys Cart Manager
 * Handles cart operations via API and updates UI
 * Moved to external file to comply with CSP
 */

const cartManager = {
    offcanvas: null,

    init: function() {
        console.log('CartManager: Initializing...');
        
        // Wait for Bootstrap
        if (typeof bootstrap === 'undefined') {
            console.warn('Bootstrap not loaded yet, retrying in 200ms...');
            setTimeout(() => this.init(), 200);
            return;
        }

        const el = document.getElementById('cartOffcanvas');
        if(el) {
            try {
                this.offcanvas = new bootstrap.Offcanvas(el);
                console.log('CartManager: Offcanvas initialized');
            } catch (err) {
                console.error('CartManager: Failed to init offcanvas', err);
            }
        }

        // Attach Global Listeners (Delegation)
        this.attachListeners();
        
        // Initial View Update
        this.updateView();
    },

    attachListeners: function() {
        // Use capture phase to ensure we catch it before anything stops propagation
        document.addEventListener('click', (e) => {
            console.log('Global Click:', e.target); // Debug
            
            // 1. Open Cart Trigger
            const openBtn = e.target.closest('.js-open-cart');
            if (openBtn) {
                e.preventDefault();
                console.log('Open Cart Clicked');
                this.open();
                return;
            }

            // 2. Add to Cart Trigger
            const addBtn = e.target.closest('.js-add-cart');
            if (addBtn) {
                e.preventDefault();
                console.log('Add Cart Clicked via Delegation');
                
                const id = addBtn.dataset.id;
                if(id) {
                    this.add(id, addBtn);
                } else {
                    console.error('No ID found on button');
                }
            }
        }, true); // Capture Phase = true
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
            return await res.json();
        } catch (e) {
            console.error('Cart API Error:', e);
            return { success: false };
        }
    },

    add: async function(id, btnElement = null) {
        console.log('Adding product:', id);
        
        let originalText = '';
        if (btnElement) {
            // Se for passado "this" no onclick
            if (!(btnElement instanceof Element)) btnElement = null; // Safety
        }

        if (btnElement) {
            originalText = btnElement.innerHTML;
            btnElement.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ...';
            btnElement.style.pointerEvents = 'none';
            btnElement.style.opacity = '0.7';
        }

        try {
            const res = await this.api('add', { id, qty: 1 });
            
            if (res.success) {
                this.render(res.cart);
                this.open(); 
            } else {
                alert('Erro ao adicionar: ' + (res.message || 'Desconhecido'));
            }
        } catch(err) {
            console.error(err);
            alert('Erro de conexão ao adicionar produto.');
        } finally {
            if (btnElement) {
                // Restore logic
                setTimeout(() => {
                    btnElement.innerHTML = originalText;
                    btnElement.style.pointerEvents = 'auto';
                    btnElement.style.opacity = '1';
                }, 500);
            }
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
        if (res.success) this.updateView();
    },

    updateView: async function() {
        const res = await this.api('get');
        if (res.success) this.render(res.cart);
    },

    render: function(cart) {
        const container = document.getElementById('cartItemsContainer');
        const totalEl = document.getElementById('cartTotalMain');
        const badge = document.getElementById('cartCountBadge');
        const btnFinalizar = document.getElementById('btnFinalizar');
        const template = document.getElementById('cartItemTemplate');

        if (badge) {
            badge.innerText = cart.count;
            badge.style.display = cart.count > 0 ? 'inline-block' : 'none';
        }

        if(totalEl) totalEl.innerText = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(cart.total_cents / 100);

        if(container) {
            container.innerHTML = '';

            if (cart.items.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5 opacity-50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mb-3"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                        <p>Seu carrinho está vazio.</p>
                    </div>
                `;
                if(btnFinalizar) btnFinalizar.disabled = true;
                return;
            }

            if(btnFinalizar) btnFinalizar.disabled = false;

            cart.items.forEach(item => {
                const clone = template.content.cloneNode(true);
                const imgUrl = item.image_url || 'assets/keys/glock/glock.png'; 
                
                clone.querySelector('.item-title').innerText = item.title;
                clone.querySelector('.item-price').innerText = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(item.price / 100);
                clone.querySelector('.item-img').src = imgUrl;
                clone.querySelector('.item-qty').innerText = item.qty;
                
                // Events (Using closures here is fine as they are created dynamically)
                // But to be consistent with CSP, we should ideally use delegation too, 
                // but these are created programmatically so they don't trigger inline script blocks.
                clone.querySelector('.btn-plus').onclick = () => this.update(item.id, item.qty + 1);
                clone.querySelector('.btn-minus').onclick = () => this.update(item.id, item.qty - 1);
                clone.querySelector('.btn-remove').onclick = () => this.remove(item.id);

                container.appendChild(clone);
            });
        }
    }
};

window.cartManager = cartManager;
// Start
document.addEventListener('DOMContentLoaded', () => window.cartManager.init());
