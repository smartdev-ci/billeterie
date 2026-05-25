<template>
  <div class="event-container">
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Chargement de l'événement...</p>
    </div>

    <div v-else-if="error" class="error">
      <p>{{ error }}</p>
    </div>

    <div v-else-if="event" class="event-content">
      <!-- Hero Section -->
      <div class="hero" :style="{ backgroundImage: event.gallery && event.gallery.length ? `url(${event.gallery[0]})` : 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' }">
        <div class="hero-overlay">
          <h1>{{ event.title }}</h1>
          <p class="event-date">{{ formatDate(event.start_date) }} - {{ formatDate(event.end_date) }}</p>
          <p class="event-location">📍 {{ event.location }}</p>
        </div>
      </div>

      <!-- Event Details -->
      <div class="event-details">
        <div class="info-card">
          <h2>À propos de l'événement</h2>
          <p>{{ event.description }}</p>
        </div>

        <!-- Ticket Pricing -->
        <div class="ticket-card">
          <h2>Billetterie</h2>
          <div class="price-tag">
            <span class="price">{{ formatPrice(event.ticket_price) }}</span>
            <span class="currency">FCFA</span>
          </div>
          
          <div class="availability" :class="{ 'low-stock': availableTickets <= 10 }">
            <div class="progress-bar">
              <div 
                class="progress-fill" 
                :style="{ width: soldPercentage + '%' }"
              ></div>
            </div>
            <p>
              <strong>{{ availableTickets }}</strong> places disponibles sur {{ event.capacity }}
              <span v-if="soldPercentage > 0">({{ soldPercentage }}% vendues)</span>
            </p>
          </div>

          <button 
            @click="buyTickets" 
            class="buy-btn"
            :disabled="availableTickets === 0"
          >
            {{ availableTickets === 0 ? 'Complet' : 'Acheter des billets' }}
          </button>
        </div>

        <!-- FAQ Section -->
        <div v-if="event.faq && event.faq.length" class="faq-section">
          <h2>Questions Fréquentes</h2>
          <div v-for="(item, index) in event.faq" :key="index" class="faq-item">
            <h3>{{ item.question }}</h3>
            <p>{{ item.answer }}</p>
          </div>
        </div>

        <!-- Gallery -->
        <div v-if="event.gallery && event.gallery.length" class="gallery-section">
          <h2>Galerie</h2>
          <div class="gallery-grid">
            <img 
              v-for="(image, index) in event.gallery" 
              :key="index"
              :src="image" 
              :alt="'Image ' + (index + 1)"
              loading="lazy"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EventView',
  
  data() {
    return {
      event: null,
      loading: true,
      error: null,
      availableTickets: 0
    }
  },

  computed: {
    soldPercentage() {
      if (!this.event || this.event.capacity === 0) return 0;
      const sold = this.event.capacity - this.availableTickets;
      return Math.round((sold / this.event.capacity) * 100);
    }
  },

  async mounted() {
    await this.fetchEvent();
  },

  methods: {
    async fetchEvent() {
      try {
        this.loading = true;
        this.error = null;
        
        // Fetch active event (no ID needed)
        const response = await fetch('/api/event');
        
        if (!response.ok) {
          if (response.status === 404) {
            this.error = 'Aucun événement actif pour le moment.';
            return;
          }
          throw new Error('Erreur lors du chargement');
        }

        this.event = await response.json();
        
        // Fetch available tickets
        await this.fetchAvailableTickets();
      } catch (err) {
        this.error = 'Une erreur est survenue. Veuillez réessayer plus tard.';
        console.error('Error fetching event:', err);
      } finally {
        this.loading = false;
      }
    },

    async fetchAvailableTickets() {
      try {
        const response = await fetch(`/api/event/${this.event.id}/available-tickets`);
        if (response.ok) {
          const data = await response.json();
          this.availableTickets = data.available_tickets;
        }
      } catch (err) {
        console.error('Error fetching available tickets:', err);
      }
    },

    formatDate(dateString) {
      const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },

    formatPrice(price) {
      return new Intl.NumberFormat('fr-FR').format(price);
    },

    buyTickets() {
      // Redirect to checkout or open modal
      // This will be implemented in Sprint 3
      this.$emit('buy-tickets', this.event);
      alert('Fonctionnalité d\'achat disponible prochainement !');
    }
  }
}
</script>

<style scoped>
.event-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

/* Loading & Error States */
.loading, .error {
  text-align: center;
  padding: 60px 20px;
}

.spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error {
  color: #e74c3c;
  font-size: 1.2rem;
}

/* Hero Section */
.hero {
  height: 400px;
  background-size: cover;
  background-position: center;
  border-radius: 16px;
  margin-bottom: 30px;
  position: relative;
  overflow: hidden;
}

.hero-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
  padding: 40px;
  color: white;
}

.hero h1 {
  font-size: 2.5rem;
  margin-bottom: 10px;
  text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.event-date, .event-location {
  font-size: 1.1rem;
  margin: 5px 0;
}

/* Event Details */
.event-details {
  display: grid;
  gap: 30px;
}

.info-card, .ticket-card, .faq-section, .gallery-section {
  background: white;
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

h2 {
  color: #333;
  margin-bottom: 20px;
  font-size: 1.8rem;
}

/* Ticket Card */
.price-tag {
  display: flex;
  align-items: baseline;
  gap: 10px;
  margin: 20px 0;
}

.price {
  font-size: 3rem;
  font-weight: bold;
  color: #667eea;
}

.currency {
  font-size: 1.5rem;
  color: #666;
}

.availability {
  margin: 20px 0;
}

.low-stock {
  color: #e74c3c;
}

.progress-bar {
  height: 10px;
  background: #e0e0e0;
  border-radius: 5px;
  overflow: hidden;
  margin: 10px 0;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #667eea, #764ba2);
  transition: width 0.3s ease;
}

.buy-btn {
  width: 100%;
  padding: 18px;
  font-size: 1.2rem;
  font-weight: bold;
  color: white;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.buy-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.buy-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

/* FAQ */
.faq-item {
  margin-bottom: 20px;
  padding-bottom: 20px;
  border-bottom: 1px solid #eee;
}

.faq-item:last-child {
  border-bottom: none;
}

.faq-item h3 {
  color: #667eea;
  margin-bottom: 10px;
}

.faq-item p {
  color: #666;
  line-height: 1.6;
}

/* Gallery */
.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 15px;
}

.gallery-grid img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 8px;
  transition: transform 0.3s;
}

.gallery-grid img:hover {
  transform: scale(1.05);
}

/* Responsive */
@media (max-width: 768px) {
  .hero {
    height: 300px;
  }
  
  .hero h1 {
    font-size: 1.8rem;
  }
  
  .price {
    font-size: 2.5rem;
  }
  
  .info-card, .ticket-card, .faq-section, .gallery-section {
    padding: 20px;
  }
}
</style>
