<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Achat de Billets</h1>
        <p class="text-gray-600">{{ event.title }}</p>
      </div>

      <!-- Event Summary -->
      <div v-if="event" class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-sm text-gray-500">Date de début</p>
            <p class="font-semibold">{{ formatDate(event.start_date) }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Lieu</p>
            <p class="font-semibold">{{ event.location }}</p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Prix par billet</p>
            <p class="font-semibold text-green-600">{{ formatPrice(event.ticket_price) }} FCFA</p>
          </div>
          <div>
            <p class="text-sm text-gray-500">Billets disponibles</p>
            <p class="font-semibold" :class="availableTickets <= 10 ? 'text-red-600' : 'text-green-600'">
              {{ availableTickets }} / {{ event.capacity }}
            </p>
          </div>
        </div>
        <div class="mt-4">
          <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div 
              class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
              :style="{ width: `${ticketsSoldPercentage}%` }"
            ></div>
          </div>
          <p class="text-xs text-gray-500 mt-1">{{ ticketsSoldPercentage }}% rempli</p>
        </div>
      </div>

      <!-- Purchase Form -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Formulaire d'achat</h2>

        <form @submit.prevent="submitOrder" class="space-y-4">
          <!-- Customer Information -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">
                Nom complet *
              </label>
              <input
                id="customer_name"
                v-model="form.customer_name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Votre nom complet"
              />
            </div>
            <div>
              <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">
                Email *
              </label>
              <input
                id="customer_email"
                v-model="form.customer_email"
                type="email"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="votre@email.com"
              />
            </div>
          </div>

          <div>
            <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">
              Téléphone *
            </label>
            <input
              id="customer_phone"
              v-model="form.customer_phone"
              type="tel"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="+225 XX XX XX XX XX"
            />
          </div>

          <!-- Quantity -->
          <div>
            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
              Nombre de billets *
            </label>
            <div class="flex items-center space-x-4">
              <button
                type="button"
                @click="decrementQuantity"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md transition"
                :disabled="form.quantity <= 1"
              >
                -
              </button>
              <span class="text-2xl font-semibold w-12 text-center">{{ form.quantity }}</span>
              <button
                type="button"
                @click="incrementQuantity"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md transition"
                :disabled="form.quantity >= Math.min(10, availableTickets)"
              >
                +
              </button>
              <span class="text-sm text-gray-500">(max: {{ Math.min(10, availableTickets) }})</span>
            </div>
          </div>

          <!-- Payment Method -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Méthode de paiement *
            </label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <button
                v-for="method in paymentMethods"
                :key="method.value"
                type="button"
                @click="form.payment_method = method.value"
                class="p-3 border-2 rounded-lg transition-all duration-200 flex flex-col items-center justify-center space-y-2"
                :class="form.payment_method === method.value 
                  ? 'border-blue-500 bg-blue-50' 
                  : 'border-gray-200 hover:border-gray-300'"
              >
                <div class="text-2xl">{{ method.icon }}</div>
                <span class="text-sm font-medium">{{ method.label }}</span>
              </button>
            </div>
          </div>

          <!-- Order Summary -->
          <div class="bg-gray-50 rounded-lg p-4 mt-6">
            <h3 class="font-semibold text-gray-800 mb-3">Récapitulatif de la commande</h3>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Nombre de billets:</span>
                <span class="font-medium">{{ form.quantity }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Prix unitaire:</span>
                <span class="font-medium">{{ formatPrice(event.ticket_price) }} FCFA</span>
              </div>
              <div class="border-t border-gray-200 pt-2 flex justify-between text-lg font-bold">
                <span>Total à payer:</span>
                <span class="text-green-600">{{ formatPrice(totalAmount) }} FCFA</span>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading || !canSubmit"
            class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center space-x-2"
          >
            <svg v-if="loading" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-else>Payer maintenant</span>
          </button>

          <!-- Error Message -->
          <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center space-x-2 text-red-800">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="font-medium">{{ error }}</span>
            </div>
          </div>

          <!-- Success Message -->
          <div v-if="success" class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center space-x-2 text-green-800">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div>
                <p class="font-medium">Commande créée avec succès !</p>
                <p class="text-sm mt-1">Vous allez être redirigé vers le paiement...</p>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Payment Modal (for simulation) -->
      <div v-if="showPaymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
          <h3 class="text-lg font-semibold mb-4">Paiement en cours</h3>
          <p class="text-gray-600 mb-4">Vous êtes sur le point d'être redirigé vers {{ currentPaymentMethod }}.</p>
          <div class="flex justify-end space-x-3">
            <button
              @click="cancelPayment"
              class="px-4 py-2 text-gray-600 hover:text-gray-800"
            >
              Annuler
            </button>
            <button
              @click="confirmPayment"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
              Continuer
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TicketPurchase',
  
  data() {
    return {
      event: null,
      loading: false,
      error: null,
      success: false,
      showPaymentModal: false,
      currentPaymentMethod: '',
      form: {
        customer_name: '',
        customer_email: '',
        customer_phone: '',
        quantity: 1,
        payment_method: 'orange_money',
      },
      paymentMethods: [
        { value: 'orange_money', label: 'Orange Money', icon: '🟠' },
        { value: 'mtn_money', label: 'MTN Money', icon: '🟡' },
        { value: 'moov_money', label: 'Moov Money', icon: '🔵' },
        { value: 'cinetpay', label: 'CinetPay', icon: '💳' },
        { value: 'fedapay', label: 'FedaPay', icon: '💳' },
      ],
    };
  },

  computed: {
    availableTickets() {
      if (!this.event) return 0;
      return this.event.capacity - this.event.tickets_sold;
    },
    
    ticketsSoldPercentage() {
      if (!this.event || this.event.capacity === 0) return 0;
      return Math.round((this.event.tickets_sold / this.event.capacity) * 100);
    },
    
    totalAmount() {
      if (!this.event) return 0;
      return this.event.ticket_price * this.form.quantity;
    },
    
    canSubmit() {
      return (
        this.form.customer_name &&
        this.form.customer_email &&
        this.form.customer_phone &&
        this.form.quantity > 0 &&
        this.form.quantity <= this.availableTickets
      );
    },
  },

  async mounted() {
    await this.loadEvent();
  },

  methods: {
    async loadEvent() {
      try {
        const response = await fetch('/api/event');
        const data = await response.json();
        
        if (data.success || data.data) {
          this.event = data.data || data;
        } else {
          this.error = 'Aucun événement actif trouvé';
        }
      } catch (err) {
        this.error = 'Erreur lors du chargement de l\'événement';
        console.error(err);
      }
    },

    incrementQuantity() {
      if (this.form.quantity < Math.min(10, this.availableTickets)) {
        this.form.quantity++;
      }
    },

    decrementQuantity() {
      if (this.form.quantity > 1) {
        this.form.quantity--;
      }
    },

    async submitOrder() {
      if (!this.canSubmit) return;

      this.loading = true;
      this.error = null;
      this.success = false;

      try {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const response = await fetch('/api/payment/initiate', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken || '',
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            event_id: this.event.id,
            customer_name: this.form.customer_name,
            customer_email: this.form.customer_email,
            customer_phone: this.form.customer_phone,
            quantity: this.form.quantity,
            payment_method: this.form.payment_method,
          }),
        });

        const data = await response.json();

        if (response.ok && data.success) {
          this.success = true;
          
          // Store order info and redirect to payment
          localStorage.setItem('pending_order', JSON.stringify({
            order_id: data.data.order_id,
            transaction_id: data.data.transaction_id,
            payment_url: data.data.payment_url,
          }));

          // Show payment modal or redirect
          if (data.data.payment_url) {
            setTimeout(() => {
              window.location.href = data.data.payment_url;
            }, 2000);
          }
        } else {
          throw new Error(data.message || 'Échec de la création de commande');
        }
      } catch (err) {
        this.error = err.message;
        console.error(err);
      } finally {
        this.loading = false;
      }
    },

    cancelPayment() {
      this.showPaymentModal = false;
    },

    confirmPayment() {
      this.showPaymentModal = false;
      // Redirect to payment URL
      const pendingOrder = JSON.parse(localStorage.getItem('pending_order'));
      if (pendingOrder?.payment_url) {
        window.location.href = pendingOrder.payment_url;
      }
    },

    formatDate(dateString) {
      if (!dateString) return '';
      return new Date(dateString).toLocaleDateString('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    },

    formatPrice(price) {
      if (!price) return '0';
      return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    },
  },
};
</script>

<style scoped>
/* Add any additional styles here */
</style>
