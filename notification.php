<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Notifications - AGORA FRANCIA</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="icon" href="logo.ico" type="image/x-icon" />
  <script src="script.js" defer></script>
  <style>
    /* Style page de notif */
    .notifications-container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 40px 20px;
    }

    .notification-section {
      background: white;
      border-radius: 15px;
      padding: 30px;
      margin-bottom: 30px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      border-left: 4px solid #397194;
    }

    .section-title {
      font-size: 24px;
      color: #397194;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .toggle-container {
      display: flex;
      align-items: center;
      gap: 20px;
      margin: 20px 0;
      padding: 20px;
      background: #f8f9fa;
      border-radius: 10px;
      border: 2px solid #e0e0e0;
      transition: all 0.3s ease;
    }

    .toggle-container.active {
      border-color: #397194;
      background: rgba(57, 113, 148, 0.05);
    }

    .toggle-switch {
      position: relative;
      width: 60px;
      height: 30px;
      background: #ccc;
      border-radius: 15px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .toggle-switch.active {
      background: #397194;
    }

    .toggle-slider {
      position: absolute;
      top: 3px;
      left: 3px;
      width: 24px;
      height: 24px;
      background: white;
      border-radius: 50%;
      transition: all 0.3s ease;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .toggle-switch.active .toggle-slider {
      transform: translateX(30px);
    }

    .toggle-label {
      font-size: 18px;
      font-weight: 500;
      color: #333;
    }

    .toggle-description {
      font-size: 14px;
      color: #666;
      margin-left: 80px;
      line-height: 1.4;
    }

    .search-criteria-form {
      display: grid;
      gap: 25px;
    }

    .criteria-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .form-label {
      font-weight: 600;
      color: #333;
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .form-input, .form-select {
      padding: 12px 16px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 16px;
      transition: all 0.3s ease;
      background: white;
    }

    .form-input:focus, .form-select:focus {
      outline: none;
      border-color: #397194;
      box-shadow: 0 0 0 3px rgba(57, 113, 148, 0.1);
    }

    .price-range {
      display: grid;
      grid-template-columns: 1fr auto 1fr;
      gap: 10px;
      align-items: center;
    }

    .price-separator {
      color: #666;
      font-weight: bold;
    }

    .sale-types {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 16px;
      background: #f8f9fa;
      border-radius: 8px;
      border: 2px solid #e0e0e0;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .checkbox-group:hover {
      background: rgba(57, 113, 148, 0.05);
      border-color: #397194;
    }

    .checkbox-group.checked {
      background: rgba(57, 113, 148, 0.1);
      border-color: #397194;
    }

    .custom-checkbox {
      position: relative;
      width: 20px;
      height: 20px;
    }

    .custom-checkbox input {
      opacity: 0;
      position: absolute;
    }

    .checkmark {
      position: absolute;
      top: 0;
      left: 0;
      height: 20px;
      width: 20px;
      background: white;
      border: 2px solid #ccc;
      border-radius: 4px;
      transition: all 0.3s ease;
    }

    .custom-checkbox input:checked ~ .checkmark {
      background: #397194;
      border-color: #397194;
    }

    .checkmark:after {
      content: "";
      position: absolute;
      display: none;
      left: 6px;
      top: 2px;
      width: 6px;
      height: 10px;
      border: solid white;
      border-width: 0 2px 2px 0;
      transform: rotate(45deg);
    }

    .custom-checkbox input:checked ~ .checkmark:after {
      display: block;
    }

    .checkbox-label {
      font-size: 14px;
      font-weight: 500;
      color: #333;
    }

    .action-buttons {
      display: flex;
      gap: 15px;
      justify-content: flex-end;
      margin-top: 30px;
      flex-wrap: wrap;
    }

    .btn {
      padding: 12px 30px;
      border-radius: 8px;
      font-weight: bold;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.3s ease;
      border: 2px solid transparent;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .btn-primary {
      background: #397194;
      color: white;
      border-color: #397194;
    }

    .btn-primary:hover {
      background: #252d70;
      border-color: #252d70;
      transform: translateY(-2px);
    }

    .btn-secondary {
      background: transparent;
      color: #397194;
      border-color: #397194;
    }

    .btn-secondary:hover {
      background: #397194;
      color: white;
    }

    .btn-clear {
      background: transparent;
      color: #666;
      border-color: #ccc;
    }

    .btn-clear:hover {
      background: #f0f0f0;
      border-color: #999;
    }

    .current-alerts {
      margin-top: 20px;
    }

    .alert-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px;
      background: rgba(57, 113, 148, 0.05);
      border-radius: 8px;
      margin-bottom: 10px;
      border-left: 4px solid #397194;
    }

    .alert-info {
      flex: 1;
    }

    .alert-title {
      font-weight: 600;
      color: #333;
      margin-bottom: 5px;
    }

    .alert-details {
      font-size: 14px;
      color: #666;
    }

    .alert-actions {
      display: flex;
      gap: 10px;
    }

    .btn-small {
      padding: 6px 12px;
      font-size: 12px;
      border-radius: 4px;
    }

    .btn-delete {
      background: #dc3545;
      color: white;
      border-color: #dc3545;
    }

    .btn-delete:hover {
      background: #c82333;
      border-color: #c82333;
    }

    .info-box {
      background: rgba(57, 113, 148, 0.1);
      border: 1px solid rgba(57, 113, 148, 0.2);
      border-radius: 8px;
      padding: 15px;
      margin: 20px 0;
    }

    .info-box-title {
      font-weight: 600;
      color: #397194;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .info-box-text {
      font-size: 14px;
      color: #555;
      line-height: 1.4;
    }
  </style>
</head>

<body>
  <header class="header">
    <div class="header-top">
      <div class="header-top-content">
        <div class="header-top-left">
          🏠 Toute la France
        </div>
        <div class="header-top-right">
          <a href="mentions.html">Mentions légales</a>
          <a href="conf.html">Politique de Confidentialité</a>
        </div>
      </div>
    </div>

    <div class="header-main">
      <div class="header-main-content">

        <div class="burger-container">
          <button class="header-burger" onclick="toggleHeaderDropdown()">☰Menu</button>
          <div class="header-dropdown" id="headerDropdown">
            <ul>
              <li><a href="accueil.php"><span class="menu-icon">🏠</span>Accueil</a></li>
              <li><a href="toutparcourir.php"><span class="menu-icon">🔍</span>Tout parcourir</a></li>
              <li><a href="notification.php"><span class="menu-icon">🔔</span>Notifications</a></li>
              <li><a href="panier.php"><span class="menu-icon">🛒</span>Panier</a></li>
              <li><a href="redirection.php"><span class="menu-icon">👤</span>Votre compte</a></li>
            </ul>
          </div>  
        </div>

        <div class="logo-section">
          <a href="accueil.php">
            <img src="logo2.jpeg" alt="Logo Agora Francia" class="logo"/>
          </a>
          <h1>AGORA FRANCIA</h1>
        </div>

        <div class="search-container">
          <form class="search-form">
            <input type="text" class="search-input" placeholder="Que recherchez-vous ?">
            <input type="text" class="location-input" placeholder="Où ?">
            <button type="submit" class="search-btn">🔍</button>
          </form>
        </div>

        <div class="header-actions">
          <?php if (isset($_SESSION['username'])): ?>
            <span style="margin-right: 15px; color: #397194; font-weight: 500;">
              👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
            </span>
            <a href="logout.php" class="auth-btn danger">Se déconnecter</a>
          <?php else: ?>
            <a href="inscription.php" class="auth-btn">S'inscrire</a>
            <a href="connexion.php" class="auth-btn primary">Se connecter</a>
          <?php endif; ?>
        </div>

      </div>
    </div>

    <nav class="nav-bar">
      <div class="nav-content">
        <ul class="nav-menu">
          <li><a href="accueil.php">Accueil</a></li>
          <li><a href="toutparcourir.php">Tout parcourir</a></li>
          <li><a href="notification.php">Notifications</a></li>
          <li><a href="panier.php">Panier</a></li>
          <li><a href="redirection.php">Votre compte</a></li>
        </ul>
        <div class="header-top-right">
          <a href="mailto:agorafrancia@gmail.com">Contact</a>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <div class="notifications-container">
      
      <div class="notification-section">
        <h2 class="section-title">
          🔔 Paramètres des Notifications
        </h2>
        
        <div class="toggle-container" id="notificationToggle">
          <div class="toggle-switch" id="toggleSwitch" onclick="toggleNotifications()">
            <div class="toggle-slider"></div>
          </div>
          <div>
            <div class="toggle-label">Activer les alertes personnalisées</div>
            <div class="toggle-description">
              Recevez des notifications dès qu'un article correspondant à vos critères devient disponible sur Agora Francia.
            </div>
          </div>
        </div>

        <div class="info-box">
          <div class="info-box-title">
            ℹ️ Comment ça marche ?
          </div>
          <div class="info-box-text">
            Une fois les notifications activées, configurez vos critères de recherche ci-dessous. 
            Nous vous enverrons un email dès qu'un nouvel article correspondant à vos critères est mis en vente.
          </div>
        </div>
      </div>

      <div class="notification-section" id="criteriaSection" style="opacity: 0.5; pointer-events: none;">
        <h2 class="section-title">
          🎯 Critères de Recherche
        </h2>
        
        <form class="search-criteria-form" id="criteriaForm">
          <div class="criteria-row">
            <div class="form-group">
              <label class="form-label" for="objectName">Nom ou type d'objet</label>
              <input 
                type="text" 
                id="objectName" 
                class="form-input" 
                placeholder="Ex: iPhone, MacBook, Console..."
              >
            </div>

            <div class="form-group">
              <label class="form-label" for="category">Catégorie</label>
              <select id="category" class="form-select">
                <option value="">Toutes les catégories</option>
                <option value="electronics">📱 Électronique</option>
                <option value="fashion">👕 Mode & Accessoires</option>
                <option value="home">🏠 Maison & Jardin</option>
                <option value="sports">⚽ Sports & Loisirs</option>
                <option value="vehicles">🚗 Véhicules</option>
                <option value="books">📚 Livres & Multimédia</option>
                <option value="other">🔧 Autres</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Fourchette de prix (€)</label>
            <div class="price-range">
              <input 
                type="number" 
                id="minPrice" 
                class="form-input" 
                placeholder="Prix minimum"
                min="0"
              >
              <span class="price-separator">à</span>
              <input 
                type="number" 
                id="maxPrice" 
                class="form-input" 
                placeholder="Prix maximum"
                min="0"
              >
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Types de vente</label>
            <div class="sale-types">
              <div class="checkbox-group" onclick="toggleCheckbox('immediateCheck')">
                <div class="custom-checkbox">
                  <input type="checkbox" id="immediateCheck">
                  <span class="checkmark"></span>
                </div>
                <span class="checkbox-label">💰 Vente immédiate</span>
              </div>
              
              <div class="checkbox-group" onclick="toggleCheckbox('negotiationCheck')">
                <div class="custom-checkbox">
                  <input type="checkbox" id="negotiationCheck">
                  <span class="checkmark"></span>
                </div>
                <span class="checkbox-label">🤝 Vente par négociation</span>
              </div>
              
              <div class="checkbox-group" onclick="toggleCheckbox('bestOfferCheck')">
                <div class="custom-checkbox">
                  <input type="checkbox" id="bestOfferCheck">
                  <span class="checkmark"></span>
                </div>
                <span class="checkbox-label">🏆 Meilleure offre</span>
              </div>
            </div>
          </div>

          <div class="action-buttons">
            <button type="button" class="btn btn-clear" onclick="clearCriteria()">
              🗑️ Effacer
            </button>
            <button type="button" class="btn btn-secondary" onclick="previewAlert()">
              👁️ Aperçu
            </button>
            <button type="button" class="btn btn-primary" onclick="saveAlert()">
              💾 Créer l'alerte
            </button>
          </div>
        </form>
      </div>

      <div class="notification-section" id="activeAlertsSection">
        <h2 class="section-title">
          📋 Mes Alertes Actives
        </h2>
        
        <div class="current-alerts" id="alertsList">
          <div class="alert-item">
            <div class="alert-info">
              <div class="alert-title">iPhone - Électronique</div>
              <div class="alert-details">Prix: 500€ - 1000€ • Vente immédiate, Négociation</div>
            </div>
            <div class="alert-actions">
              <button class="btn btn-secondary btn-small">✏️ Modifier</button>
              <button class="btn btn-delete btn-small">🗑️ Supprimer</button>
            </div>
          </div>
          
          <div class="alert-item">
            <div class="alert-info">
              <div class="alert-title">Console - Tous types</div>
              <div class="alert-details">Prix: jusqu'à 600€ • Tous types de vente</div>
            </div>
            <div class="alert-actions">
              <button class="btn btn-secondary btn-small">✏️ Modifier</button>
              <button class="btn btn-delete btn-small">🗑️ Supprimer</button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </main>
  
  <footer>
    <p>&copy; 2025 ING2 TD3 GP1. Tous droits réservés.</p>
    <span style="font-size: 20px;">-</span>
    <p style="display: inline-block; margin-left: 10px;">
      Contactez-nous :
      <a href="mailto:agorafrancia@gmail.com" style="text-decoration: none; color: inherit;">agorafrancia@gmail.com</a>
    </p>
    <span style="font-size: 20px;">-</span>
    <p style="display: inline-block; margin-left: 10px;">
      <a href="mentions.html" class="legal-link">Mentions légales</a>
    </p>
    <span style="font-size: 20px;">-</span>
    <p style="display: inline-block; margin-left: 10px;">
      <a href="conf.html" class="legal-link">Politique de Confidentialité</a>
    </p>
  </footer>

  <script>
    //variables globales
    let notificationsEnabled = false;
    let alerts = [];

    //fonction pour activer/désactiver les notifs
    function toggleNotifications() {
      const toggleSwitch = document.getElementById('toggleSwitch');
      const toggleContainer = document.getElementById('notificationToggle');
      const criteriaSection = document.getElementById('criteriaSection');
      
      notificationsEnabled = !notificationsEnabled;
      
      if (notificationsEnabled) {
        toggleSwitch.classList.add('active');
        toggleContainer.classList.add('active');
        criteriaSection.style.opacity = '1';
        criteriaSection.style.pointerEvents = 'auto';
        showNotification('Notifications activées ! Configurez vos critères ci-dessous.');
      } else {
        toggleSwitch.classList.remove('active');
        toggleContainer.classList.remove('active');
        criteriaSection.style.opacity = '0.5';
        criteriaSection.style.pointerEvents = 'none';
        showNotification('Notifications désactivées.');
      }
    }

    //fonction pour toggler les checkboxes
    function toggleCheckbox(checkboxId) {
      const checkbox = document.getElementById(checkboxId);
      const container = checkbox.closest('.checkbox-group');
      
      checkbox.checked = !checkbox.checked;
      
      if (checkbox.checked) {
        container.classList.add('checked');
      } else {
        container.classList.remove('checked');
      }
    }

    //fonction pour effacer tous les critères
    function clearCriteria() {
      document.getElementById('objectName').value = '';
      document.getElementById('category').value = '';
      document.getElementById('minPrice').value = '';
      document.getElementById('maxPrice').value = '';
      
      //décocher toutes les checkboxes
      const checkboxes = document.querySelectorAll('.checkbox-group input[type="checkbox"]');
      checkboxes.forEach(checkbox => {
        checkbox.checked = false;
        checkbox.closest('.checkbox-group').classList.remove('checked');
      });
      
      showNotification('Critères effacés.');
    }

    // Fonction pour prévisualiser l'alerte
    function previewAlert() {
      const criteria = getCriteriaFromForm();
      
      if (!criteria.objectName && !criteria.category && !criteria.minPrice && !criteria.maxPrice && criteria.saleTypes.length === 0) {
        alert('Veuillez remplir au moins un critère pour créer une alerte.');
        return;
      }
      
      let preview = 'Aperçu de votre alerte :\n\n';
      
      if (criteria.objectName) {
        preview += `• Objet recherché: ${criteria.objectName}\n`;
      }
      
      if (criteria.category) {
        const categoryText = document.querySelector(`#category option[value="${criteria.category}"]`).textContent;
        preview += `• Catégorie: ${categoryText}\n`;
      }
      
      if (criteria.minPrice || criteria.maxPrice) {
        let priceText = '• Prix: ';
        if (criteria.minPrice && criteria.maxPrice) {
          priceText += `${criteria.minPrice}€ - ${criteria.maxPrice}€`;
        } else if (criteria.minPrice) {
          priceText += `à partir de ${criteria.minPrice}€`;
        } else {
          priceText += `jusqu'à ${criteria.maxPrice}€`;
        }
        preview += priceText + '\n';
      }
      
      if (criteria.saleTypes.length > 0) {
        preview += `• Types de vente: ${criteria.saleTypes.join(', ')}\n`;
      }
      
      preview += '\nVous recevrez une notification dès qu\'un article correspondant sera disponible.';
      
      alert(preview);
    }

    // Fonction pour sauvegarder une alerte
    function saveAlert() {
      if (!notificationsEnabled) {
        alert('Veuillez d\'abord activer les notifications.');
        return;
      }
      
      const criteria = getCriteriaFromForm();
      
      if (!criteria.objectName && !criteria.category && !criteria.minPrice && !criteria.maxPrice && criteria.saleTypes.length === 0) {
        alert('Veuillez remplir au moins un critère pour créer une alerte.');
        return;
      }
      // Ajouter l'alerte à la liste
      alerts.push({
        id: Date.now(),
        ...criteria,
        createdAt: new Date()
      });
      // Mettre à jour l'affichage
      updateAlertsList();
      // Effacer le formulaire
      clearCriteria();
      showNotification('Alerte créée avec succès ! 🎉');
    }
    // Fonction pour récupérer les critères du formulaire
    function getCriteriaFromForm() {
      const objectName = document.getElementById('objectName').value.trim();
      const category = document.getElementById('category').value;
      const minPrice = document.getElementById('minPrice').value;
      const maxPrice = document.getElementById('maxPrice').value;
      const saleTypes = [];
      if (document.getElementById('immediateCheck').checked) {
        saleTypes.push('Vente immédiate');
      }
      if (document.getElementById('negotiationCheck').checked) {
        saleTypes.push('Négociation');
      }
      if (document.getElementById('bestOfferCheck').checked) {
        saleTypes.push('Meilleure offre');
      }
      
      return {
        objectName,
        category,
        minPrice: minPrice ? parseInt(minPrice) : null,
        maxPrice: maxPrice ? parseInt(maxPrice) : null,
        saleTypes
      };
    }

    // Fonction pour mettre à jour la liste des alertes
    function updateAlertsList() {
      const alertsList = document.getElementById('alertsList');
      
      if (alerts.length === 0) {
        alertsList.innerHTML = '<p style="text-align: center; color: #666; font-style: italic;">Aucune alerte active pour le moment.</p>';
        return;
      }
      
      alertsList.innerHTML = alerts.map(alert => {
        let title = alert.objectName || 'Recherche générale';
        if (alert.category) {
          const categoryOption = document.querySelector(`#category option[value="${alert.category}"]`);
          title += ` - ${categoryOption ? categoryOption.textContent.replace(/^.{2}\s/, '') : alert.category}`;
        }
        
        let details = [];
        if (alert.minPrice || alert.maxPrice) {
          if (alert.minPrice && alert.maxPrice) {
            details.push(`Prix: ${alert.minPrice}€ - ${alert.maxPrice}€`);
          } else if (alert.minPrice) {
            details.push(`Prix: à partir de ${alert.minPrice}€`);
          } else {
            details.push(`Prix: jusqu'à ${alert.maxPrice}€`);
          }
        }
        
        if (alert.saleTypes.length > 0) {
          details.push(alert.saleTypes.join(', '));
        } else {
          details.push('Tous types de vente');
        }
        
        return `
          <div class="alert-item">
            <div class="alert-info">
              <div class="alert-title">${title}</div>
              <div class="alert-details">${details.join(' • ')}</div>
            </div>
            <div class="alert-actions">
              <button class="btn btn-secondary btn-small" onclick="editAlert(${alert.id})">✏️ Modifier</button>
              <button class="btn btn-delete btn-small" onclick="deleteAlert(${alert.id})">🗑️ Supprimer</button>
            </div>
          </div>
        `;
      }).join('');
    }

    //fonction pour supp une alerte
    function deleteAlert(alertId) {
      if (confirm('Êtes-vous sûr de vouloir supprimer cette alerte ?')) {
        alerts = alerts.filter(alert => alert.id !== alertId);
        updateAlertsList();
        showNotification('Alerte supprimée.');
      }
    }

    //fonction pour modifier une alerte
    function editAlert(alertId) {
      const alert = alerts.find(a => a.id === alertId);
      if (!alert) return;
      
      //remplir le formulaire avec les données de l'alerte
      document.getElementById('objectName').value = alert.objectName || '';
      document.getElementById('category').value = alert.category || '';
      document.getElementById('minPrice').value = alert.minPrice || '';
      document.getElementById('maxPrice').value = alert.maxPrice || '';
      
      //cocher les bonnes checkboxes
      document.getElementById('immediateCheck').checked = alert.saleTypes.includes('Vente immédiate');
      document.getElementById('negotiationCheck').checked = alert.saleTypes.includes('Négociation');
      document.getElementById('bestOfferCheck').checked = alert.saleTypes.includes('Meilleure offre');
      
      //mettre à jour l'apparence des checkboxes
      ['immediateCheck', 'negotiationCheck', 'bestOfferCheck'].forEach(id => {
        const checkbox = document.getElementById(id);
        const container = checkbox.closest('.checkbox-group');
        if (checkbox.checked) {
          container.classList.add('checked');
        } else {
          container.classList.remove('checked');
        }
      });
      
      //supp l'ancienne alerte
      alerts = alerts.filter(a => a.id !== alertId);
      updateAlertsList();
      
      //faire défiler vers le formulaire
      document.getElementById('criteriaSection').scrollIntoView({ behavior: 'smooth' });
      
      showNotification('Alerte chargée pour modification. Modifiez les critères et cliquez sur "Créer l\'alerte".');
    }

    // Fonction pour afficher des notifs toast
    function showNotification(message) {
      // Créer une notification temporaire
      const notification = document.createElement('div');
      notification.textContent = message;
      notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #397194;
        color: white;
        padding: 15px 25px;
        border-radius: 8px;
        z-index: 10000;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 300px;
        font-size: 14px;
        line-height: 1.4;
      `;
      
      document.body.appendChild(notification);
      
      // Animer l'entrée
      setTimeout(() => {
        notification.style.transform = 'translateX(0)';
      }, 100);
      
      // Supprimer après 4s
      setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
          if (document.body.contains(notification)) {
            document.body.removeChild(notification);
          }
        }, 300);
      }, 4000);
    }

    // Validation des prix en temps réel
    document.getElementById('minPrice').addEventListener('input', function() {
      const minPrice = parseInt(this.value);
      const maxPriceInput = document.getElementById('maxPrice');
      const maxPrice = parseInt(maxPriceInput.value);
      
      if (minPrice && maxPrice && minPrice > maxPrice) {
        maxPriceInput.value = minPrice;
      }
    });

    document.getElementById('maxPrice').addEventListener('input', function() {
      const maxPrice = parseInt(this.value);
      const minPriceInput = document.getElementById('minPrice');
      const minPrice = parseInt(minPriceInput.value);
      
      if (minPrice && maxPrice && maxPrice < minPrice) {
        minPriceInput.value = maxPrice;
      }
    });

    // Initialisation au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
      console.log('Page Notifications - Agora Francia chargée');
      
      // Simuler quelques alertes existantes pour la démo
      alerts = [
        {
          id: 1,
          objectName: 'iPhone',
          category: 'electronics',
          minPrice: 500,
          maxPrice: 1000,
          saleTypes: ['Vente immédiate', 'Négociation'],
          createdAt: new Date()
        },
        {
          id: 2,
          objectName: 'Console',
          category: '',
          minPrice: null,
          maxPrice: 600,
          saleTypes: ['Vente immédiate', 'Négociation', 'Meilleure offre'],
          createdAt: new Date()
        }
      ];
      
      updateAlertsList();
      
      // Ajouter des événements de validation 
      const form = document.getElementById('criteriaForm');
      const inputs = form.querySelectorAll('input, select');
      
      inputs.forEach(input => {
        input.addEventListener('focus', function() {
          this.style.borderColor = '#397194';
          this.style.boxShadow = '0 0 0 3px rgba(57, 113, 148, 0.1)';
        });
        
        input.addEventListener('blur', function() {
          this.style.borderColor = '#e0e0e0';
          this.style.boxShadow = 'none';
        });
      });
      
      // Animation d'entrée pour les sections
      const sections = document.querySelectorAll('.notification-section');
      sections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
          section.style.transition = 'all 0.6s ease';
          section.style.opacity = '1';
          section.style.transform = 'translateY(0)';
        }, index * 200);
      });
    });

    // Fonction pour sauvegarder les préférences localement
    function savePreferencesToLocal() {
      const preferences = {
        notificationsEnabled,
        alerts
      };
      
      try {
        localStorage.setItem('agoraNotificationPreferences', JSON.stringify(preferences));
      } catch (e) {
        console.warn('Impossible de sauvegarder les préférences localement');
      }
    }
    // Fonction pour charger les préférences locales 
    function loadPreferencesFromLocal() {
      try {
        const saved = localStorage.getItem('agoraNotificationPreferences');
        if (saved) {
          const preferences = JSON.parse(saved);
          notificationsEnabled = preferences.notificationsEnabled || false;
          alerts = preferences.alerts || [];
          if (notificationsEnabled) {
            toggleNotifications();
          }
          updateAlertsList();
        }
      } catch (e) {
        console.warn('Impossible de charger les préférences locales');
      }
    }
    // Sauvegarder automatiquement les changements
    window.addEventListener('beforeunload', savePreferencesToLocal);
  </script>
</body>

</html>