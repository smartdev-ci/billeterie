<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Header PWA -->
    <header class="bg-indigo-600 text-white shadow-lg sticky top-0 z-50">
      <div class="max-w-md mx-auto px-4 py-3 flex items-center justify-between">
        <h1 class="text-lg font-bold flex items-center">
          <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
          </svg>
          Scanner Billets
        </h1>
        <button @click="showHistory = !showHistory" class="p-2 hover:bg-indigo-700 rounded-full transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </button>
      </div>
    </header>

    <main class="max-w-md mx-auto px-4 py-6">
      <!-- Message d'installation PWA -->
      <div v-if="!isInstalled && canInstallPWA" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4 rounded">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-yellow-700">
              Installez cette application pour un accès rapide !
            </p>
            <button @click="installPWA" class="mt-2 text-sm font-medium text-yellow-800 underline">
              Installer maintenant
            </button>
          </div>
        </div>
      </div>

      <!-- Historique des scans -->
      <div v-if="showHistory" class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold text-gray-800">Historique des scans</h2>
          <button @click="showHistory = false" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        
        <div v-if="loadingHistory" class="text-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
          <p class="mt-2 text-gray-600">Chargement...</p>
        </div>
        
        <div v-else-if="scanHistory.length === 0" class="text-center py-8 text-gray-500">
          <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p>Aucun scan enregistré</p>
        </div>
        
        <div v-else class="space-y-3 max-h-96 overflow-y-auto">
          <div v-for="ticket in scanHistory" :key="ticket.id" 
               class="border-l-4 rounded p-3"
               :class="ticket.status === 'used' ? 'border-green-500 bg-green-50' : 'border-gray-300 bg-gray-50'">
            <div class="flex justify-between items-start">
              <div>
                <p class="font-semibold text-gray-800">{{ ticket.ticket_number }}</p>
                <p class="text-xs text-gray-600">{{ ticket.event?.title || 'N/A' }}</p>
                <p class="text-xs text-gray-500 mt-1">
                  {{ formatDateTime(ticket.used_at) }}
                </p>
              </div>
              <span class="px-2 py-1 text-xs rounded-full"
                    :class="ticket.status === 'used' ? 'bg-green-200 text-green-800' : 'bg-gray-200 text-gray-800'">
                {{ ticket.status === 'used' ? '✓ Validé' : ticket.status }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Zone de scan QR -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">Scanner un billet</h2>
        
        <!-- État: En attente de permission -->
        <div v-if="cameraError === 'permission-denied'" class="text-center py-8">
          <svg class="w-16 h-16 mx-auto text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
          </svg>
          <p class="text-gray-600 mb-4">Accès à la caméra refusé</p>
          <button @click="requestCameraAccess" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
            Réessayer
          </button>
        </div>

        <!-- État: Chargement caméra -->
        <div v-else-if="loadingCamera" class="text-center py-12">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
          <p class="mt-4 text-gray-600">Activation de la caméra...</p>
        </div>

        <!-- État: Scanner actif -->
        <div v-else class="relative">
          <div class="scanner-container relative bg-black rounded-lg overflow-hidden" style="height: 300px;">
            <video ref="qrVideo" class="w-full h-full object-cover"></video>
            
            <!-- Overlay de guidage -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
              <div class="border-4 border-white rounded-lg opacity-60" style="width: 200px; height: 200px;"></div>
            </div>
            
            <!-- Ligne de scan animée -->
            <div class="absolute left-0 right-0 h-0.5 bg-red-500 opacity-80 animate-scan"></div>
          </div>
          
          <p class="text-center text-gray-600 mt-4 text-sm">
            Pointez la caméra vers le QR code du billet
          </p>
        </div>

        <!-- Saisie manuelle (fallback) -->
        <div class="mt-6 pt-6 border-t border-gray-200">
          <p class="text-sm text-gray-600 text-center mb-3">Ou saisissez le code manuellement</p>
          <form @submit.prevent="validateManualCode" class="flex gap-2">
            <input 
              v-model="manualCode" 
              type="text" 
              placeholder="Ex: LPP-ABC123-001 ou UUID"
              class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            />
            <button type="submit" :disabled="!manualCode.trim()" 
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition">
              Valider
            </button>
          </form>
        </div>
      </div>

      <!-- Résultat du scan -->
      <transition enter-active-class="transition duration-300 ease-out"
                  enter-from-class="transform translate-y-4 opacity-0"
                  enter-to-class="transform translate-y-0 opacity-100"
                  leave-active-class="transition duration-200 ease-in"
                  leave-from-class="transform translate-y-0 opacity-100"
                  leave-to-class="transform translate-y-4 opacity-0">
        <div v-if="scanResult" 
             class="rounded-lg shadow-lg p-6 mb-6"
             :class="scanResult.success ? 'bg-green-50 border-2 border-green-500' : 'bg-red-50 border-2 border-red-500'">
          
          <!-- Icône de statut -->
          <div class="flex justify-center mb-4">
            <div v-if="scanResult.success" 
                 class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center">
              <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <div v-else 
                 class="w-20 h-20 bg-red-500 rounded-full flex items-center justify-center">
              <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </div>
          </div>

          <!-- Message principal -->
          <h3 class="text-2xl font-bold text-center mb-2"
              :class="scanResult.success ? 'text-green-800' : 'text-red-800'">
            {{ scanResult.success ? 'ACCÈS AUTORISÉ ✓' : 'ACCÈS REFUSÉ ✗' }}
          </h3>
          
          <p class="text-center text-lg mb-4"
             :class="scanResult.success ? 'text-green-700' : 'text-red-700'">
            {{ scanResult.message }}
          </p>

          <!-- Détails du ticket -->
          <div v-if="scanResult.ticket" class="bg-white rounded-lg p-4 space-y-2">
            <div class="flex justify-between">
              <span class="text-gray-600">Numéro:</span>
              <span class="font-semibold">{{ scanResult.ticket.number }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Événement:</span>
              <span class="font-semibold">{{ scanResult.ticket.event }}</span>
            </div>
            <div v-if="scanResult.ticket.holder" class="flex justify-between">
              <span class="text-gray-600">Titulaire:</span>
              <span class="font-semibold">{{ scanResult.ticket.holder }}</span>
            </div>
            <div v-if="scanResult.ticket.used_at" class="flex justify-between">
              <span class="text-gray-600">Validé le:</span>
              <span class="font-semibold">{{ scanResult.ticket.used_at }}</span>
            </div>
            <div v-if="scanResult.ticket.scanned_by" class="flex justify-between">
              <span class="text-gray-600">Scanné par:</span>
              <span class="font-semibold">{{ scanResult.ticket.scanned_by }}</span>
            </div>
          </div>

          <!-- Bouton nouveau scan -->
          <button @click="resetScan" class="w-full mt-4 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition">
            Scanner un autre billet
          </button>
        </div>
      </transition>

      <!-- Statistiques rapides -->
      <div class="bg-white rounded-lg shadow-md p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Statistiques du jour</h3>
        <div class="grid grid-cols-2 gap-4">
          <div class="text-center p-3 bg-green-50 rounded-lg">
            <p class="text-2xl font-bold text-green-600">{{ todayStats.validated }}</p>
            <p class="text-sm text-gray-600">Billets validés</p>
          </div>
          <div class="text-center p-3 bg-red-50 rounded-lg">
            <p class="text-2xl font-bold text-red-600">{{ todayStats.rejected }}</p>
            <p class="text-sm text-gray-600">Refusés</p>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-4 mt-8">
      <div class="max-w-md mx-auto px-4 text-center">
        <p class="text-sm">© 2024 Billetterie - Scanner PWA</p>
        <p class="text-xs text-gray-400 mt-1">Version 1.0.0</p>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'QrScan',

  data() {
    return {
      // États de la caméra
      loadingCamera: false,
      cameraError: null,
      isScanning: false,
      
      // Résultat du scan
      scanResult: null,
      manualCode: '',
      
      // Historique
      showHistory: false,
      scanHistory: [],
      loadingHistory: false,
      
      // PWA
      isInstalled: false,
      canInstallPWA: false,
      deferredPrompt: null,
      
      // Statistiques
      todayStats: {
        validated: 0,
        rejected: 0
      }
    };
  },

  mounted() {
    this.checkPWAInstallStatus();
    this.setupPWAInstallListener();
    this.requestCameraAccess();
    this.loadScanHistory();
  },

  methods: {
    /**
     * Demander l'accès à la caméra et démarrer le scan
     */
    async requestCameraAccess() {
      this.loadingCamera = true;
      this.cameraError = null;
      
      try {
        // Vérifier les permissions
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
          throw new Error('API caméra non supportée');
        }
        
        // Demander la permission
        const stream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode: 'environment' } // Caméra arrière sur mobile
        });
        
        // Attacher le flux vidéo à l'élément video
        this.$refs.qrVideo.srcObject = stream;
        this.$refs.qrVideo.play();
        
        // Démarrer le scan avec jsQR
        this.startScanning();
        
        this.loadingCamera = false;
        this.isScanning = true;
        
      } catch (error) {
        console.error('Erreur caméra:', error);
        this.loadingCamera = false;
        
        if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
          this.cameraError = 'permission-denied';
        } else {
          this.cameraError = error.message;
        }
      }
    },

    /**
     * Démarrer le scan continu des QR codes
     */
    startScanning() {
      const video = this.$refs.qrVideo;
      const canvas = document.createElement('canvas');
      const context = canvas.getContext('2d');
      
      const scanFrame = () => {
        if (!this.isScanning || this.scanResult || !video.videoWidth) {
          requestAnimationFrame(scanFrame);
          return;
        }
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        
        // Utiliser jsQR pour décoder
        import('jsqr').then(({ default: jsQR }) => {
          const code = jsQR(imageData.data, canvas.width, canvas.height);
          
          if (code) {
            console.log('QR Code détecté:', code.data);
            this.handleScanResult(code.data);
          }
        });
        
        requestAnimationFrame(scanFrame);
      };
      
      scanFrame();
    },

    /**
     * Traiter le résultat du scan QR
     */
    async handleScanResult(token) {
      // Éviter les scans multiples
      if (this.scanResult) return;
      
      // Nettoyer le token (peut contenir des préfixes)
      const cleanToken = this.extractToken(token);
      
      if (!this.isValidUUID(cleanToken)) {
        this.scanResult = {
          success: false,
          message: 'Format de QR code invalide',
          error_code: 'INVALID_FORMAT'
        };
        this.todayStats.rejected++;
        return;
      }
      
      // Appeler l'API de validation
      try {
        const response = await fetch('/api/tickets/validate', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${this.getAuthToken()}`
          },
          body: JSON.stringify({ ticket_token: cleanToken })
        });
        
        const data = await response.json();
        
        this.scanResult = {
          success: data.success,
          message: data.message,
          ticket: data.ticket,
          error_code: data.error_code
        };
        
        if (data.success) {
          this.todayStats.validated++;
          // Vibration succès (si supporté)
          if (navigator.vibrate) {
            navigator.vibrate([200, 100, 200]);
          }
        } else {
          this.todayStats.rejected++;
          // Vibration erreur
          if (navigator.vibrate) {
            navigator.vibrate([500]);
          }
        }
        
        // Recharger l'historique
        this.loadScanHistory();
        
      } catch (error) {
        console.error('Erreur validation:', error);
        this.scanResult = {
          success: false,
          message: 'Erreur de communication avec le serveur',
          error_code: 'NETWORK_ERROR'
        };
        this.todayStats.rejected++;
      }
    },

    /**
     * Valider un code saisi manuellement
     */
    async validateManualCode() {
      if (!this.manualCode.trim()) return;
      
      const code = this.manualCode.trim();
      let token = code;
      
      // Si c'est un numéro de ticket (LPP-XXX-001), il faut trouver le token associé
      // Pour simplifier, on suppose que l'utilisateur peut scanner OU saisir le token UUID directement
      
      if (!this.isValidUUID(code)) {
        // Essayer de trouver le ticket par son numéro
        try {
          // Note: Cette API devrait être ajoutée si nécessaire
          const response = await fetch(`/api/tickets/lookup?number=${encodeURIComponent(code)}`, {
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'Authorization': `Bearer ${this.getAuthToken()}`
            }
          });
          
          if (response.ok) {
            const data = await response.json();
            token = data.ticket_token;
          } else {
            this.scanResult = {
              success: false,
              message: 'Numéro de billet non trouvé',
              error_code: 'NOT_FOUND'
            };
            return;
          }
        } catch (error) {
          this.scanResult = {
            success: false,
            message: 'Erreur de recherche du billet',
            error_code: 'SEARCH_ERROR'
          };
          return;
        }
      }
      
      await this.handleScanResult(token);
    },

    /**
     * Extraire le token UUID d'une chaîne QR
     */
    extractToken(data) {
      // Le QR code peut contenir juste l'UUID ou une URL complète
      const uuidPattern = /[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}/i;
      const match = data.match(uuidPattern);
      
      return match ? match[0] : data;
    },

    /**
     * Vérifier si c'est un UUID valide
     */
    isValidUUID(str) {
      const uuidPattern = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i;
      return uuidPattern.test(str);
    },

    /**
     * Réinitialiser pour un nouveau scan
     */
    resetScan() {
      this.scanResult = null;
      this.manualCode = '';
    },

    /**
     * Charger l'historique des scans
     */
    async loadScanHistory() {
      this.loadingHistory = true;
      
      try {
        const response = await fetch('/api/tickets/scan-history', {
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${this.getAuthToken()}`
          }
        });
        
        if (response.ok) {
          const data = await response.json();
          this.scanHistory = data.data?.data || [];
        }
      } catch (error) {
        console.error('Erreur chargement historique:', error);
      } finally {
        this.loadingHistory = false;
      }
    },

    /**
     * Formater une date
     */
    formatDateTime(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return date.toLocaleString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },

    /**
     * Récupérer le token d'authentification
     */
    getAuthToken() {
      return localStorage.getItem('token') || sessionStorage.getItem('token');
    },

    /**
     * Vérifier le statut d'installation PWA
     */
    checkPWAInstallStatus() {
      this.isInstalled = window.matchMedia('(display-mode: standalone)').matches;
    },

    /**
     * Configurer l'écouteur d'installation PWA
     */
    setupPWAInstallListener() {
      window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        this.deferredPrompt = e;
        this.canInstallPWA = true;
      });

      window.addEventListener('appinstalled', () => {
        this.isInstalled = true;
        this.canInstallPWA = false;
        this.deferredPrompt = null;
      });
    },

    /**
     * Installer la PWA
     */
    async installPWA() {
      if (!this.deferredPrompt) return;
      
      this.deferredPrompt.prompt();
      const { outcome } = await this.deferredPrompt.userChoice;
      
      if (outcome === 'accepted') {
        console.log('Utilisateur a accepté l\'installation');
      }
      
      this.deferredPrompt = null;
      this.canInstallPWA = false;
    }
  },

  beforeUnmount() {
    this.isScanning = false;
    
    // Arrêter le flux vidéo
    if (this.$refs.qrVideo && this.$refs.qrVideo.srcObject) {
      const tracks = this.$refs.qrVideo.srcObject.getTracks();
      tracks.forEach(track => track.stop());
    }
  }
};
</script>

<style scoped>
@keyframes scan {
  0% {
    top: 10%;
  }
  50% {
    top: 90%;
  }
  100% {
    top: 10%;
  }
}

.animate-scan {
  animation: scan 2s linear infinite;
}

.scanner-container {
  position: relative;
}

/* Styles pour l'overlay de guidage */
.scanner-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  pointer-events: none;
}

.scanner-container .border-4 {
  position: relative;
  z-index: 10;
}
</style>
