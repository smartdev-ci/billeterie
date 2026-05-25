<template>
  <div class="event-management">
    <div class="header">
      <h1>Gestion des Événements</h1>
      <button @click="showCreateForm = true" class="btn-primary">
        + Créer un événement
      </button>
    </div>

    <!-- Events List -->
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Chargement...</p>
    </div>

    <div v-else-if="events.length === 0" class="empty-state">
      <p>Aucun événement créé. Commencez par créer votre premier événement !</p>
    </div>

    <div v-else class="events-grid">
      <div 
        v-for="event in events" 
        :key="event.id" 
        class="event-card"
        :class="{ 'active': event.is_active }"
      >
        <div class="event-header">
          <h3>{{ event.title }}</h3>
          <span v-if="event.is_active" class="badge-active">Actif</span>
          <span v-else class="badge-inactive">Inactif</span>
        </div>
        
        <div class="event-info">
          <p><strong>Lieu:</strong> {{ event.location }}</p>
          <p><strong>Date:</strong> {{ formatDate(event.start_date) }}</p>
          <p><strong>Capacité:</strong> {{ event.capacity }} places</p>
          <p><strong>Vendus:</strong> {{ event.tickets_sold }} billets</p>
          <p><strong>Prix:</strong> {{ formatPrice(event.ticket_price) }} FCFA</p>
        </div>

        <div class="event-actions">
          <button @click="editEvent(event)" class="btn-edit">Modifier</button>
          <button @click="toggleActive(event)" class="btn-toggle">
            {{ event.is_active ? 'Désactiver' : 'Activer' }}
          </button>
          <button @click="deleteEvent(event)" class="btn-delete">Supprimer</button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showCreateForm || showEditForm" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h2>{{ isEditing ? 'Modifier l\'événement' : 'Créer un événement' }}</h2>
          <button @click="closeModal" class="btn-close">&times;</button>
        </div>

        <form @submit.prevent="saveEvent" class="event-form">
          <div class="form-group">
            <label for="title">Titre *</label>
            <input 
              id="title"
              v-model="formData.title" 
              type="text" 
              required 
              placeholder="Nom de l'événement"
            />
          </div>

          <div class="form-group">
            <label for="description">Description *</label>
            <textarea 
              id="description"
              v-model="formData.description" 
              required 
              rows="5"
              placeholder="Description détaillée de l'événement"
            ></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="location">Lieu *</label>
              <input 
                id="location"
                v-model="formData.location" 
                type="text" 
                required 
                placeholder="Adresse complète"
              />
            </div>

            <div class="form-group">
              <label for="ticket_price">Prix du billet (FCFA) *</label>
              <input 
                id="ticket_price"
                v-model.number="formData.ticket_price" 
                type="number" 
                required 
                min="0"
                :disabled="isEditing"
              />
              <small v-if="isEditing" class="form-hint">Le prix ne peut pas être modifié après création</small>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="start_date">Date de début *</label>
              <input 
                id="start_date"
                v-model="formData.start_date" 
                type="datetime-local" 
                required 
              />
            </div>

            <div class="form-group">
              <label for="end_date">Date de fin *</label>
              <input 
                id="end_date"
                v-model="formData.end_date" 
                type="datetime-local" 
                required 
              />
            </div>
          </div>

          <div class="form-group">
            <label for="capacity">Capacité maximale *</label>
            <input 
              id="capacity"
              v-model.number="formData.capacity" 
              type="number" 
              required 
              min="1"
              placeholder="Nombre maximum de participants"
            />
          </div>

          <div class="form-group">
            <label>
              <input 
                type="checkbox" 
                v-model="formData.is_active"
              />
              Activer cet événement immédiatement
            </label>
            <small class="form-hint">Un seul événement peut être actif à la fois</small>
          </div>

          <!-- FAQ Section -->
          <div class="form-section">
            <h3>FAQ</h3>
            <div v-for="(faq, index) in formData.faq" :key="index" class="faq-entry">
              <input 
                v-model="faq.question" 
                type="text" 
                placeholder="Question"
                class="faq-input"
              />
              <textarea 
                v-model="faq.answer" 
                placeholder="Réponse"
                rows="2"
                class="faq-input"
              ></textarea>
              <button type="button" @click="removeFaq(index)" class="btn-remove">✕</button>
            </div>
            <button type="button" @click="addFaq" class="btn-add-faq">+ Ajouter une question</button>
          </div>

          <!-- Gallery Section -->
          <div class="form-section">
            <h3>Galerie d'images</h3>
            <div class="gallery-inputs">
              <input 
                v-for="(image, index) in formData.gallery" 
                :key="index"
                v-model="formData.gallery[index]" 
                type="url" 
                placeholder="URL de l'image"
                class="gallery-input"
              />
            </div>
            <button type="button" @click="addGalleryImage" class="btn-add-gallery">+ Ajouter une image</button>
          </div>

          <div class="form-actions">
            <button type="button" @click="closeModal" class="btn-cancel">Annuler</button>
            <button type="submit" class="btn-submit" :disabled="saving">
              {{ saving ? 'Enregistrement...' : (isEditing ? 'Mettre à jour' : 'Créer') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EventManagement',
  
  data() {
    return {
      events: [],
      loading: false,
      saving: false,
      showCreateForm: false,
      showEditForm: false,
      isEditing: false,
      currentEventId: null,
      formData: {
        title: '',
        description: '',
        location: '',
        start_date: '',
        end_date: '',
        capacity: 100,
        ticket_price: 5000,
        is_active: false,
        faq: [],
        gallery: []
      }
    }
  },

  async mounted() {
    await this.fetchEvents();
  },

  methods: {
    async fetchEvents() {
      try {
        this.loading = true;
        const response = await fetch('/api/events', {
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${this.getAuthToken()}`
          }
        });

        if (!response.ok) {
          if (response.status === 401) {
            alert('Veuillez vous connecter en tant qu\'organisateur ou admin');
            return;
          }
          throw new Error('Erreur lors du chargement');
        }

        this.events = await response.json();
      } catch (err) {
        console.error('Error fetching events:', err);
        alert('Erreur lors du chargement des événements');
      } finally {
        this.loading = false;
      }
    },

    async saveEvent() {
      try {
        this.saving = true;

        const url = this.isEditing 
          ? `/api/events/${this.currentEventId}` 
          : '/api/events';
        
        const method = this.isEditing ? 'PUT' : 'POST';

        const response = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${this.getAuthToken()}`
          },
          body: JSON.stringify(this.formData)
        });

        if (!response.ok) {
          const errors = await response.json();
          throw new Error(errors.message || 'Erreur lors de l\'enregistrement');
        }

        await this.fetchEvents();
        this.closeModal();
        alert(this.isEditing ? 'Événement mis à jour !' : 'Événement créé avec succès !');
      } catch (err) {
        console.error('Error saving event:', err);
        alert(err.message || 'Erreur lors de l\'enregistrement');
      } finally {
        this.saving = false;
      }
    },

    async toggleActive(event) {
      if (!confirm(`Voulez-vous vraiment ${event.is_active ? 'désactiver' : 'activer'} cet événement ?`)) {
        return;
      }

      try {
        const response = await fetch(`/api/events/${event.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${this.getAuthToken()}`
          },
          body: JSON.stringify({ is_active: !event.is_active })
        });

        if (!response.ok) {
          throw new Error('Erreur lors de la mise à jour');
        }

        await this.fetchEvents();
      } catch (err) {
        console.error('Error toggling event:', err);
        alert('Erreur lors de la mise à jour');
      }
    },

    async deleteEvent(event) {
      if (!confirm(`Êtes-vous sûr de vouloir supprimer "${event.title}" ? Cette action est irréversible.`)) {
        return;
      }

      try {
        const response = await fetch(`/api/events/${event.id}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${this.getAuthToken()}`
          }
        });

        if (!response.ok) {
          throw new Error('Erreur lors de la suppression');
        }

        await this.fetchEvents();
        alert('Événement supprimé avec succès !');
      } catch (err) {
        console.error('Error deleting event:', err);
        alert('Erreur lors de la suppression');
      }
    },

    editEvent(event) {
      this.isEditing = true;
      this.currentEventId = event.id;
      this.formData = {
        title: event.title,
        description: event.description,
        location: event.location,
        start_date: this.formatDateTimeLocal(event.start_date),
        end_date: this.formatDateTimeLocal(event.end_date),
        capacity: event.capacity,
        ticket_price: event.ticket_price,
        is_active: event.is_active,
        faq: event.faq || [],
        gallery: event.gallery || []
      };
      this.showEditForm = true;
    },

    closeModal() {
      this.showCreateForm = false;
      this.showEditForm = false;
      this.isEditing = false;
      this.currentEventId = null;
      this.resetForm();
    },

    resetForm() {
      this.formData = {
        title: '',
        description: '',
        location: '',
        start_date: '',
        end_date: '',
        capacity: 100,
        ticket_price: 5000,
        is_active: false,
        faq: [],
        gallery: []
      };
    },

    addFaq() {
      this.formData.faq.push({ question: '', answer: '' });
    },

    removeFaq(index) {
      this.formData.faq.splice(index, 1);
    },

    addGalleryImage() {
      this.formData.gallery.push('');
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

    formatDateTimeLocal(dateString) {
      const date = new Date(dateString);
      return date.toISOString().slice(0, 16);
    },

    formatPrice(price) {
      return new Intl.NumberFormat('fr-FR').format(price);
    },

    getAuthToken() {
      // Get token from localStorage or other auth storage
      return localStorage.getItem('auth_token') || '';
    }
  }
}
</script>

<style scoped>
.event-management {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.header h1 {
  font-size: 2rem;
  color: #333;
}

.btn-primary {
  padding: 12px 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: transform 0.2s;
}

.btn-primary:hover {
  transform: translateY(-2px);
}

/* Loading & Empty States */
.loading, .empty-state {
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

.empty-state {
  color: #666;
  font-size: 1.2rem;
}

/* Events Grid */
.events-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 20px;
}

.event-card {
  background: white;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  border-left: 4px solid #ccc;
  transition: transform 0.2s, box-shadow 0.2s;
}

.event-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.event-card.active {
  border-left-color: #27ae60;
}

.event-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.event-header h3 {
  font-size: 1.3rem;
  color: #333;
  margin: 0;
}

.badge-active, .badge-inactive {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: bold;
}

.badge-active {
  background: #d4edda;
  color: #155724;
}

.badge-inactive {
  background: #f8f9fa;
  color: #6c757d;
}

.event-info {
  margin-bottom: 20px;
}

.event-info p {
  margin: 8px 0;
  color: #666;
}

.event-actions {
  display: flex;
  gap: 10px;
}

.event-actions button {
  flex: 1;
  padding: 8px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: opacity 0.2s;
}

.event-actions button:hover {
  opacity: 0.9;
}

.btn-edit {
  background: #3498db;
  color: white;
}

.btn-toggle {
  background: #f39c12;
  color: white;
}

.btn-delete {
  background: #e74c3c;
  color: white;
}

/* Modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
  overflow-y: auto;
}

.modal {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  border-bottom: 1px solid #eee;
}

.modal-header h2 {
  margin: 0;
  color: #333;
}

.btn-close {
  background: none;
  border: none;
  font-size: 2rem;
  cursor: pointer;
  color: #999;
}

.btn-close:hover {
  color: #333;
}

/* Form */
.event-form {
  padding: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #333;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group input[type="datetime-local"],
.form-group input[type="url"],
.form-group textarea {
  width: 100%;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 1rem;
  transition: border-color 0.2s;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
}

.form-group input:disabled {
  background: #f5f5f5;
  cursor: not-allowed;
}

.form-hint {
  display: block;
  margin-top: 6px;
  color: #666;
  font-size: 0.85rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-section {
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #eee;
}

.form-section h3 {
  margin-bottom: 15px;
  color: #667eea;
}

.faq-entry, .gallery-inputs {
  display: grid;
  gap: 10px;
  margin-bottom: 15px;
}

.faq-entry {
  grid-template-columns: 1fr 1fr auto;
  align-items: start;
}

.faq-input, .gallery-input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 0.95rem;
}

.btn-remove {
  background: #e74c3c;
  color: white;
  border: none;
  border-radius: 6px;
  width: 36px;
  height: 36px;
  cursor: pointer;
  font-size: 1.2rem;
}

.btn-add-faq, .btn-add-gallery {
  background: #f8f9fa;
  color: #667eea;
  border: 1px dashed #667eea;
  padding: 10px 20px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.2s;
}

.btn-add-faq:hover, .btn-add-gallery:hover {
  background: #667eea;
  color: white;
}

.form-actions {
  display: flex;
  gap: 15px;
  justify-content: flex-end;
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #eee;
}

.btn-cancel {
  padding: 12px 24px;
  background: #f8f9fa;
  color: #666;
  border: 1px solid #ddd;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 500;
}

.btn-submit {
  padding: 12px 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Responsive */
@media (max-width: 768px) {
  .header {
    flex-direction: column;
    gap: 15px;
  }

  .events-grid {
    grid-template-columns: 1fr;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .faq-entry {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column-reverse;
  }

  .btn-cancel, .btn-submit {
    width: 100%;
  }
}
</style>
