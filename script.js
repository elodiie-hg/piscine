
// Fonction pour toggler le menu dropdown du header
function toggleHeaderDropdown() {
    const dropdown = document.getElementById('headerDropdown');
    dropdown.classList.toggle('active');
}

// Fermer le dropdown si clique ailleurs sur la page
document.addEventListener('click', function(event) {
    const burgerContainer = document.querySelector('.burger-container');
    const dropdown = document.getElementById('headerDropdown');
    if (burgerContainer && dropdown && !burgerContainer.contains(event.target)) {
        dropdown.classList.remove('active');
    }
});

// Fermer la sidebar si clique sur overlay 
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    if (sidebar && sidebar.classList.contains('active') &&
        !sidebar.contains(event.target) &&
        !sidebarToggle.contains(event.target)) {
        sidebar.classList.remove('active');
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        // Fermer dropdown 
        const dropdown = document.getElementById('headerDropdown');
        if (dropdown) {
            dropdown.classList.remove('active');
        }
        // Fermer sidebar
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.remove('active');
        }
    }
});

// navigation au clavier
document.addEventListener('keydown', function(event) {
    const dropdown = document.getElementById('headerDropdown');
    if (dropdown && dropdown.classList.contains('active')) {
        const links = dropdown.querySelectorAll('a');
        const currentFocus = document.activeElement;
        const currentIndex = Array.from(links).indexOf(currentFocus);
        if (event.key === 'ArrowDown') {
            event.preventDefault();
            const nextIndex = currentIndex < links.length - 1 ? currentIndex + 1 : 0;
            links[nextIndex].focus();
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            const prevIndex = currentIndex > 0 ? currentIndex - 1 : links.length - 1;
            links[prevIndex].focus();
        }
    }
});



//actions des boutons
function quickBuy(productId) {
    const product = bestSellersData.find(p => p.id === productId);
    alert(`Achat rapide: ${product.title} pour ${product.currentPrice}€`);
}

function negotiate(productId) {
    const product = bestSellersData.find(p => p.id === productId);
    const userPrice = prompt(`Négocier le prix pour: ${product.title}\nPrix actuel: ${product.currentPrice}€\nVotre proposition:`);
    if (userPrice && !isNaN(userPrice)) {
        alert(`Proposition envoyée: ${userPrice}€ pour ${product.title}`);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Agora Francia - Site chargé avec succès');
    
    
    const sidebar = document.getElementById('sidebar');
    const dropdown = document.getElementById('headerDropdown');
    if (!sidebar) {
        console.warn('Sidebar non trouvée');
    }
    if (!dropdown) {
        console.warn('Header dropdown non trouvé');
    }

    const burgerButton = document.querySelector('.header-burger');
    if (burgerButton) {
        burgerButton.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        burgerButton.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    }

    const dropdownLinks = document.querySelectorAll('.header-dropdown a');
    dropdownLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });
        link.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
});


// formate  prix
function formatPrice(price) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
}

// trier produits sleon critères
function sortProducts(criteria) {
    let sortedData = [...bestSellersData];
    
    switch(criteria) {
        case 'price-asc':
            sortedData.sort((a, b) => a.currentPrice - b.currentPrice);
            break;
        case 'price-desc':
            sortedData.sort((a, b) => b.currentPrice - a.currentPrice);
            break;
        case 'sales-desc':
            sortedData.sort((a, b) => b.sales - a.sales);
            break;
        case 'discount-desc':
            sortedData.sort((a, b) => b.discount - a.discount);
            break;
        default:
            // Tri par défaut
            sortedData.sort((a, b) => b.sales - a.sales);
    }
    
    return sortedData;
}



// rechercher  produits
function searchProducts(query) {
    if (!query) return bestSellersData;
    
    const searchTerm = query.toLowerCase();
    return bestSellersData.filter(product => 
        product.title.toLowerCase().includes(searchTerm) ||
        product.description.toLowerCase().includes(searchTerm)
    );
}

// ajouter produit au panier
function addToCart(productId) {
    const product = bestSellersData.find(p => p.id === productId);
    if (product) {
        // Simulation d'ajout au panier
        console.log(`Produit ajouté au panier: ${product.title}`);
        
        let cart = JSON.parse(localStorage.getItem('cart') || '[]');
        const existingItem = cart.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                id: product.id,
                title: product.title,
                price: product.currentPrice,
                quantity: 1,
                icon: product.icon
            });
        }
        
        localStorage.setItem('cart', JSON.stringify(cart));
        
        showNotification(`${product.title} ajouté au panier !`);
    }
}

//affiche  notifications
function showNotification(message) {
    // Créer notification temporaire
    const notification = document.createElement('div');
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #303890;
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        z-index: 10000;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}