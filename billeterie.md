# 🎟️ Documentation complète - Billetterie Full Laravel (Mode Web, Événement unique)

## 🏃‍♂️ Sprints Agile

### Sprint 1 : Socle technique
- Initialisation Laravel + PostgreSQL/Supabase
- Authentification avec Breeze (web guards)
- Gestion des rôles avec Spatie
- Mise en place des routes web sécurisées

### Sprint 2 : Gestion de l’événement unique
- Création d’un seul événement (ex. Concert)
- Prix du billet fixé à **5000 FCFA**
- Gestion des quotas de billets
- Relations Eloquent (event ↔ tickets)

### Sprint 3 : Commandes et paiements
- Tunnel de vente simplifié
- Intégration Mobile Money
- Génération tickets PDF + QR code
- Envoi par mail

### Sprint 4 : Validation des billets
- Page sécurisée Scan QR
- Vérification en temps réel via contrôleur
- Audit logs

### Sprint 5 : Tableau de bord analytique
- Statistiques ventes (nombre de billets vendus)
- Revenus générés (5000 FCFA × billets vendus)
- Export PDF/Excel

### Sprint 6 : Marketing & communication
- Newsletter pour informer les clients
- SEO optimisé pour l’événement
- Programme de parrainage

### Sprint 7 : Sécurité & déploiement
- Audit logs
- SSL/TLS
- CI/CD avec Docker
- Monitoring (Telescope, Sentry)

---

## 📊 Flows Utilisateurs

### 👤 Client
1. Navigue sur le site Laravel
2. Sélectionne le billet (prix unique : **5000 FCFA**)
3. Paie via Mobile Money
4. Reçoit e-ticket par mail (PDF + QR code)
5. Présente QR code à l’entrée → validation

### 👤 Organisateur
1. Se connecte au back-office Laravel
2. Suit ventes et revenus (5000 FCFA × billets vendus)
3. Valide billets via page sécurisée Scan QR
4. Exporte rapports

### 👤 Admin
1. Supervise l’événement unique
2. Analyse ventes et revenus
3. Gère contenu du site
4. Supervise logs et sécurité

---

## 🛠️ Système d’administration Laravel

### Modules principaux
- Gestion des utilisateurs (CRUD + rôles/permissions)
- Gestion de l’événement unique
- Gestion des billets (prix fixe : 5000 FCFA)
- Commandes & paiements
- Validation QR
- Analytics (ventes, revenus)
- CMS (pages, articles, médias)
- Marketing (newsletters, promos)
- Sécurité (audit logs, monitoring)

---

## 🌐 Routes Web (Laravel)

### Authentification
- `GET /login` → formulaire de connexion
- `POST /login` → traitement connexion
- `GET /register` → formulaire inscription
- `POST /register` → traitement inscription
- `POST /logout` → déconnexion

### Événement unique
- `GET /event` → détail de l’événement
- `GET /tickets` → liste billets disponibles
- `POST /tickets` → achat billet (prix fixe : 5000 FCFA)

### Commandes & Paiements
- `GET /orders` → liste commandes
- `POST /orders` → création commande
- `POST /payments/mobile-money` → traitement paiement

### QR Code
- `GET /qr/scan` → page scan
- `POST /qr/validate` → validation ticket

### Analytics
- `GET /analytics/sales`
- `GET /analytics/revenue`

---

## 🎨 Vues Blade (Administration)

### Layouts
- `layouts/admin.blade.php`

### Authentification
- `auth/login.blade.php`
- `auth/register.blade.php`

### Tableau de bord
- `dashboard/index.blade.php` → ventes et revenus
- `dashboard/analytics.blade.php` → graphiques ventes, revenus

### Gestion billets
- `tickets/index.blade.php` → liste billets
- `tickets/create.blade.php` → création billet (prix fixe : 5000 FCFA)

### Commandes & paiements
- `orders/index.blade.php`
- `payments/index.blade.php`

### Validation QR
- `qr/scan.blade.php`

### CMS & Marketing
- `content/index.blade.php`
- `newsletters/index.blade.php`

### Sécurité & Logs
- `logs/index.blade.php`

---

## ⚡ Optimisations
- **Prix fixe** : 5000 FCFA par billet → simplifie la logique de paiement.  
- **Pagination** : pour commandes et utilisateurs.  
- **Indexation** : recherche par email ou numéro de commande.  
- **Cache Redis** : stockage du nombre de billets vendus et revenus.  
- **Index DB** : index sur colonnes critiques (`user_id`, `status`, `created_at`).  
