# 🎟️ Billeterie - Système de Billetterie avec QR Codes & PWA

Système complet de billetterie pour événements uniques avec paiement Mobile Money, génération de QR codes et validation via PWA mobile-first.

## 📋 Table des matières

- [Fonctionnalités](#fonctionnalités)
- [Architecture Technique](#architecture-technique)
- [Découpage en Sprints Agile](#découpage-en-sprints-agile)
- [Installation](#installation)
- [Configuration](#configuration)

## ✨ Fonctionnalités

- **Paiement Mobile Money** : Intégration CinetPay/FedaPay
- **Tickets Multiples** : Un ticket par billet avec QR code unique
- **Validation PWA** : Application mobile-first pour scanners
- **Génération QR Codes** : Via API QRServer
- **Envoi PDF** : Tickets envoyés par email
- **Tableau de bord** : Statistiques et exports
- **CMS Intégré** : Gestion de contenu et newsletter

## 🏗️ Architecture Technique

| Composant | Technologie |
|-----------|-------------|
| Backend | Laravel 10 |
| Frontend | Vue.js 3 + Tailwind CSS |
| Base de données | PostgreSQL (Supabase) |
| Authentification | Laravel Breeze |
| PWA Scanner | Service Worker + Camera API |
| QR Codes | QRServer API |
| Paiement | CinetPay / FedaPay |
| Emails | Laravel Mail + Queue |
| PDF | DomPDF / Snappy |

## 🏃‍♂️ Découpage en Sprints Agile

### Sprint 1 — Socle technique ✅
- Installer **Laravel 10** et configurer PostgreSQL (via Supabase).
- Mettre en place **Laravel Breeze** pour l'authentification.
- Définir les rôles (`CLIENT`, `ORGANIZER`, `ADMIN`, `SCANNER`) comme **constantes dans User.php**.
- Créer les migrations de base : `users`, `events`, `orders`, `tickets`.

---

### Sprint 2 — Gestion de l'événement unique
- Implémenter le CRUD pour l'événement (un seul actif).
- Fixer le **prix du billet à 5000 FCFA** dans le modèle `Event`.
- Gérer la capacité et le nombre de billets vendus.
- Vue publique Vue.js : `EventView.vue`.

---

### Sprint 3 — Commandes et paiements
- Tunnel de vente simplifié : formulaire d'achat avec nom, email, téléphone, quantité.
- Création d'une commande (`orders`) avec statut `pending`.
- Intégration **Mobile Money via CinetPay/FedaPay** :
  - Endpoint `/payment/initiate` → redirection vers opérateur.
  - Endpoint `/payment/webhook` → confirmation opérateur.
- Mise à jour du statut de la commande (`confirmed`).

---

### Sprint 4 — Génération Tickets & QR codes
- Pour chaque billet commandé (`quantity`), créer une entrée dans `tickets`.
- Générer un **token unique** pour chaque ticket (UUID ou hash).
- Appeler l'API externe pour générer le QR code : https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={ticket_token}
- Stocker l'URL du QR code dans `tickets.qr_image_path`.
- Exemple de référence : `LPP-ABC123-001`, `LPP-ABC123-002`.

---

### Sprint 5 — Envoi par email
- Générer un PDF contenant tous les billets de la commande.
- Inclure les QR codes distincts dans le PDF.
- Job en queue (`SendTicketEmail`) pour envoyer le PDF par email au client.

---

### Sprint 6 — Interface Scanner (PWA)
- Créer une **PWA mobile-first** pour le rôle Scanner.
- Ajouter `manifest.json` et `service-worker.js` pour installation sur téléphone.
- Composant Vue.js `QrScan.vue` :
  - Utiliser `navigator.mediaDevices.getUserMedia()` pour activer la caméra.
  - Intégrer une librairie comme `jsQR` ou `vue-qrcode-reader` pour décoder les QR codes.
  - Lors du scan :
    - Envoyer le **token du ticket** au backend Laravel (`/tickets/validate`).
    - Vérifier en base : ticket actif, non utilisé.
    - Mettre à jour le statut (`used`) et enregistrer `scanned_by`.
  - Feedback immédiat :
    - ✅ Ticket valide → accès autorisé.
    - ❌ Ticket invalide ou déjà utilisé → accès refusé.
- Historique des scans pour l'organisateur.

---

### Sprint 7 — Tableau de bord analytique
- Écran Vue.js `Dashboard.vue` pour l'organisateur.
- Statistiques : billets vendus, revenus générés (5000 FCFA × billets).
- Export CSV/Excel des ventes (`Reports.vue`).
- Écran Vue.js `Analytics.vue` pour l'admin avec graphiques globaux.

---

### Sprint 8 — CMS & Marketing
- Module `ContentManager.vue` pour gérer les sections (hero, FAQ, galerie).
- Gestion des abonnés newsletter (`Newsletters.vue`).
- Envoi de campagnes marketing.
- SEO optimisé pour l'événement unique.

---

### Sprint 9 — Sécurité & déploiement
- Mise en place des **audit logs** (`Logs.vue`).
- Sécurisation HTTPS (SSL/TLS).
- CI/CD avec Docker.
- Monitoring avec **Laravel Telescope + Sentry**.

---

## ⚡ Points clés

- **Un ticket par billet** → même commande peut générer plusieurs tickets.
- **QR codes distincts** → chaque ticket est lié à un token unique.
- **API externe QRServer** → génération rapide et fiable des QR codes.
- **Scanner = PWA mobile-first** → utilisable comme une app native avec accès caméra.
- **Webhook sécurisé** → seule confirmation opérateur déclenche la génération.
- **Email PDF** → tous les billets envoyés ensemble au client.
- **Validation en temps réel** → un ticket scanné change d'état et ne peut plus être réutilisé.

## 🚀 Installation

```bash
# Cloner le projet
git clone https://github.com/smartdev-ci/billeterie.git
cd billeterie

# Installer les dépendances PHP
composer install

# Installer les dépendances Node
npm install

# Copier le fichier .env
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Lancer les migrations
php artisan migrate

# Compiler les assets
npm run build

# Démarrer le serveur
php artisan serve
```

## ⚙️ Configuration

### Base de données (Supabase)

Modifier `.env` :
```env
DB_CONNECTION=pgsql
DB_HOST=xxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=votre_mot_de_passe
```

### Paiement (CinetPay/FedaPay)

```env
CINETPAY_API_KEY=votre_cle
CINETPAY_SITE_ID=votre_id
FEDAPAY_API_KEY=votre_cle
```

### Email

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre_user
MAIL_PASSWORD=votre_password
```

## 📱 Rôles Utilisateurs

| Rôle | Permissions |
|------|-------------|
| `CLIENT` | Acheter des billets, recevoir tickets par email |
| `ORGANIZER` | Gérer l'événement, voir statistiques, exports |
| `SCANNER` | Valider les tickets via PWA mobile |
| `ADMIN` | Administration complète, logs, utilisateurs |

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
