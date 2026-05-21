# Prompt — Architecture Laravel 11 Billetterie Mobile Money + QR Code + Guest Checkout

Tu es un architecte logiciel senior expert en Laravel 11.

Je veux construire une application web de billetterie simple, propre, scalable et production-ready avec les contraintes suivantes.

---

# STACK TECHNIQUE

- Framework : Laravel 11
- Auth : Laravel Breeze (Blade uniquement, web guards uniquement)
- Base de données : PostgreSQL (Supabase compatible)
- Paiement : Mobile Money (simulation ou abstraction API)
- QR Code : Simple QrCode
- PDF : DomPDF ou SnappyPDF
- Cache : Redis
- Queue : Redis ou Database
- Frontend : Blade + TailwindCSS
- Charts : Chart.js ou ApexCharts
- Architecture propre et scalable

---

# OBJECTIF DU PROJET

Construire une application de billetterie complète permettant :

- achat de billets pour un seul événement
- paiement Mobile Money
- génération ticket PDF + QR code unique
- validation ticket via scan QR sécurisé
- dashboard admin
- dashboard organizer
- support achat invité (sans compte)

---

# CONTRAINTE MÉTIER PRINCIPALE

⚠️ IMPORTANT :

Le système doit gérer UN SEUL événement.

NE PAS :
- créer un système multi-events
- créer des organisations complexes
- créer du multi-tenant
- sur-architecturer le domaine métier

Le système doit rester :
- simple
- maintenable
- scalable
- production-ready

---

# CONTRAINTE AUTHENTIFICATION IMPORTANTE

L’utilisateur NE DOIT PAS être obligé de créer un compte pour acheter un billet.

Le système doit supporter :

## Guest Checkout

Un visiteur peut :
- acheter billet sans compte
- payer
- recevoir ticket par email
- recevoir PDF ticket
- recevoir QR code

---

## Utilisateur connecté

Un utilisateur connecté peut :
- voir ses commandes
- retrouver ses billets
- télécharger ses tickets
- consulter historique

---

# CONSÉQUENCE ARCHITECTURE

Les relations suivantes DOIVENT être nullable :

```php
$table->foreignId('user_id')
    ->nullable()
    ->constrained()
    ->nullOnDelete();
```

Applicable sur :
- orders
- tickets

Le système doit fonctionner :
- avec utilisateur connecté
- avec guest checkout

---

# RÔLES

## guest

Peut :
- voir événement
- acheter billet
- payer
- recevoir ticket email

---

## user

Peut :
- acheter billets
- voir commandes
- télécharger tickets
- consulter historique

---

## organizer

Peut :
- accéder scanner QR
- valider tickets
- voir statistiques
- voir tickets vendus
- voir validations récentes

Ne peut PAS :
- gérer utilisateurs
- modifier système
- accéder logs critiques

---

## admin

Peut :
- tout gérer
- gérer organizers
- voir analytics complets
- voir logs
- superviser validations

---

# LOGIQUE MÉTIER

## Événement unique

Un seul événement existe dans toute l’application.

Colonnes :

- name
- description
- event_date
- location
- max_tickets
- tickets_sold

---

## Billets

Prix fixe global :

```php
const TICKET_PRICE = 5000;
```

Chaque ticket possède :

- UUID unique
- QR code unique
- QR signature
- propriétaire
- PDF téléchargeable
- statut

---

# STATUTS TICKET

```txt
valid
used
cancelled
```

---

# WORKFLOW COMPLET ACHAT

# Étape 1 — Visite événement

Route :

```txt
/event
```

Le visiteur voit :
- infos événement
- stock restant
- prix fixe 5000 FCFA
- formulaire achat

---

# Étape 2 — Achat billet

Le visiteur renseigne :

- nom
- email
- téléphone
- quantité
- provider Mobile Money

Même sans compte.

---

# Étape 3 — Création commande

Création :

```txt
payment_status = pending
```

Le système stocke :

- customer_name
- customer_email
- customer_phone
- quantity
- total_amount

---

# Étape 4 — Paiement Mobile Money

Workflow :

```txt
commande créée
→ paiement initié
→ callback confirmation
→ paiement confirmé
```

Simulation possible :
- succès
- échec
- timeout

---

# Étape 5 — Génération tickets

Après confirmation paiement :

- génération UUID
- génération QR sécurisé
- génération PDF
- création tickets
- envoi email

---

# Étape 6 — Validation entrée événement

Organizer/Admin :

```txt
/qr/scan
```

Scan QR :
- validation serveur
- anti double scan
- ticket marqué used

---

# ARCHITECTURE LARAVEL ATTENDUE

Utiliser :

- Controllers séparés
- Services métiers
- Form Requests
- Policies
- Middleware
- Jobs
- Mailables
- Notifications
- Cache
- Queues
- Events/Listeners si pertinent

Architecture propre SOLID.

---

# STRUCTURE ATTENDUE

```txt
app/
├── Actions/
├── DTOs/
├── Events/
├── Exceptions/
├── Helpers/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   ├── Auth/
│   │   ├── Event/
│   │   ├── Order/
│   │   ├── Payment/
│   │   ├── QR/
│   │   └── Ticket/
│   ├── Middleware/
│   └── Requests/
├── Jobs/
├── Listeners/
├── Mail/
├── Models/
├── Notifications/
├── Policies/
├── Providers/
├── Services/
│   ├── Payment/
│   ├── Ticket/
│   ├── QR/
│   ├── PDF/
│   └── Analytics/
└── Support/

database/
├── factories/
├── migrations/
└── seeders/

resources/
├── views/
│   ├── auth/
│   ├── event/
│   ├── tickets/
│   ├── orders/
│   ├── dashboard/
│   ├── analytics/
│   ├── qr/
│   ├── components/
│   └── layouts/
├── css/
└── js/

routes/
├── web.php
└── auth.php
```

---

# MODÈLES ELOQUENT

## User

Colonnes :
- id
- name
- email
- password
- role

Relations :
- orders()
- tickets()

---

## Event

⚠️ Un seul événement.

Colonnes :
- id
- name
- description
- event_date
- location
- max_tickets
- tickets_sold

Relations :
- tickets()

---

## Order

⚠️ user_id nullable.

Colonnes :
- id
- uuid
- user_id nullable
- customer_name
- customer_email
- customer_phone
- quantity
- total_amount
- payment_status
- payment_reference
- mobile_provider
- paid_at

Relations :
- user()
- tickets()

---

## Ticket

⚠️ user_id nullable.

Colonnes :
- id
- uuid
- order_id
- user_id nullable
- event_id
- customer_email
- qr_code
- qr_signature
- status
- validated_by nullable
- used_at

Relations :
- user()
- order()
- event()
- validator()

---

## AdminAuditLog

Colonnes :
- id
- admin_id
- action
- metadata
- ip_address
- user_agent

---

# SERVICES MÉTIERS

## PaymentService

Responsabilités :
- initier paiement
- confirmer paiement
- générer référence
- abstraction Mobile Money
- simulation provider

Méthodes :
- initiate()
- verify()
- complete()
- fail()

---

## TicketService

Responsabilités :
- créer tickets
- générer UUID
- générer PDF
- envoyer email
- supporter guest checkout

Méthodes :
- createTicket()
- generatePdf()
- sendTicketEmail()

---

## QRCodeService

Responsabilités :
- générer QR
- signer QR
- valider QR
- empêcher double scan

Méthodes :
- generate()
- validate()
- markAsUsed()

---

## AnalyticsService

Responsabilités :
- statistiques ventes
- revenus
- tickets restants
- métriques dashboard

---

# QR CODE SYSTEM

---

## Routes sécurisées

```php
Route::middleware(['auth', 'role:admin,organizer'])
    ->group(function () {

        Route::get('/qr/scan', [
            QRScanController::class,
            'scan'
        ])->name('qr.scan');

        Route::post('/qr/validate', [
            QRScanController::class,
            'validateTicket'
        ])->name('qr.validate');

    });
```

---

# QR VALIDATION FLOW

## Étape 1

Organizer/Admin ouvre :

```txt
/qr/scan
```

---

## Étape 2

Scan caméra :
- mobile
- webcam
- scanner externe

---

## Étape 3

POST :

```txt
/qr/validate
```

Payload :

```json
{
  "ticket_uuid": "uuid",
  "signature": "signed_hash"
}
```

---

## Étape 4

Backend :

- vérifie signature
- vérifie ticket
- vérifie statut
- vérifie anti reuse
- marque ticket used
- stocke validator
- log action

---

# MIDDLEWARE

## RoleMiddleware

Support :

```php
->middleware('role:admin,organizer')
```

Implementation :

```php
public function handle($request, Closure $next, ...$roles)
{
    if (! auth()->check()) {
        abort(403);
    }

    if (! in_array(auth()->user()->role, $roles)) {
        abort(403);
    }

    return $next($request);
}
```

---

# POLICIES

## TicketPolicy

Méthodes :
- view
- download
- validate

Validation :

```php
public function validate(User $user): bool
{
    return in_array($user->role, [
        'admin',
        'organizer'
    ]);
}
```

---

## OrderPolicy

Méthodes :
- view

Support :
- ownership user_id
- ownership email guest

---

# ROUTES ATTENDUES

```php
/login
/register
/logout

/event

/tickets
/tickets/{uuid}

/orders
/orders/{uuid}

/payments/mobile-money/initiate
/payments/mobile-money/callback

/qr/scan
/qr/validate

/dashboard
/analytics
```

---

# CONTRÔLEURS ATTENDUS

## Auth

- LoginController
- RegisterController

---

## Event

- EventController

---

## Orders

- OrderController

---

## Tickets

- TicketController

---

## Payments

- MobileMoneyPaymentController

---

## QR

- QRScanController

---

## Dashboard

- DashboardController
- AnalyticsController

---

# FORM REQUESTS

Créer :

- PurchaseTicketRequest
- MobileMoneyPaymentRequest
- QRValidationRequest

Validation stricte obligatoire.

---

# DASHBOARD ADMIN

Créer :

- total tickets vendus
- revenus
- tickets restants
- commandes récentes
- validations QR récentes
- ventes journalières
- graphique simple

---

# DASHBOARD ORGANIZER

Afficher :

- validations live
- tickets utilisés
- tickets restants
- activité récente entrée

---

# VIEWS BLADE

Créer :

```txt
layouts/app.blade.php
layouts/admin.blade.php
```

Pages :

```txt
auth/login.blade.php
auth/register.blade.php

event/show.blade.php

tickets/index.blade.php
tickets/show.blade.php

orders/index.blade.php
orders/show.blade.php

dashboard/index.blade.php

analytics/index.blade.php

qr/scan.blade.php
```

---

# UI/UX

Interface :
- responsive
- moderne
- simple
- TailwindCSS uniquement

Inclure :
- cards
- stats widgets
- tables paginées
- alerts
- scanner UI
- feedback validation live

---

# PDF TICKET

Le PDF doit contenir :

- nom utilisateur
- UUID ticket
- QR code
- date événement
- lieu
- statut

Nom :

```txt
ticket-{uuid}.pdf
```

---

# EMAILS

Créer :

## TicketPurchasedMail

Contient :
- PDF attaché
- QR intégré
- infos ticket

Support :
- guest checkout
- utilisateur connecté

---

# BASE DE DONNÉES

Utiliser PostgreSQL compatible Supabase.

Ajouter :
- UUID indexes
- foreign keys
- indexes status
- indexes payment_status
- indexes user_id
- indexes customer_email

---

# OPTIMISATIONS

Implémenter :

- pagination
- eager loading
- cache Redis
- queues emails
- queues PDF
- transactions DB
- rate limiting QR validation

---

# SÉCURITÉ

Obligatoire :

- CSRF
- middleware auth
- role middleware
- validation stricte
- anti double scan
- signed QR payload
- logs admin
- protection replay attack

---

# BONUS OPTIONNEL

## Export Excel

Utiliser :
- Laravel Excel

---

## Notifications

- paiement confirmé
- ticket utilisé
- rappel événement

---

## Logs

Créer :
- audit logs admin
- logs validation QR

---

# TESTS

Créer :

- Feature tests
- Unit tests services
- tests paiement
- tests QR
- tests guest checkout
- tests anti double scan

---

# OBJECTIF FINAL

Le résultat doit être :

- simple
- propre
- scalable
- sécurisé
- maintenable
- production-ready

Architecture digne :
- d’un vrai SaaS moderne
- d’une app Laravel professionnelle
- facilement extensible plus tard vers multi-events

Ne donne PAS de pseudo-code inutile.

Donne :
- architecture complète
- vrai code Laravel 11
- migrations
- modèles
- services
- contrôleurs
- routes
- logique QR
- logique paiement
- logique génération ticket
- vues Blade minimales mais propres