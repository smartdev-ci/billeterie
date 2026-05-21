@extends('layouts.scanner')

@section('title', 'Scan QR - Validation')

@section('content')
<div x-data="qrScanner()" class="space-y-6">
    <div class="bg-white rounded-xl border p-6 shadow-sm">
        <h2 class="text-xl font-semibold mb-4">Validation des billets</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-4">
                <input type="text" x-model="uuid" @keydown.enter.prevent="validate" placeholder="UUID du billet" class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-orange-500">
                <input type="text" x-model="signature" @keydown.enter.prevent="validate" placeholder="Signature HMAC" class="w-full px-4 py-3 border rounded-lg bg-gray-50">
                <button @click="validate" :disabled="loading || !uuid" class="w-full py-3 bg-orange-600 hover:bg-orange-700 disabled:opacity-50 text-white font-medium rounded-lg transition">
                    <span x-show="!loading">Valider le billet</span>
                    <span x-show="loading">Verification en cours...</span>
                </button>
            </div>
            <div class="bg-gray-900 rounded-lg p-4 h-64 flex items-center justify-center relative">
                <div id="qr-reader" class="w-full h-full"></div>
                <p x-show="!cameraActive" class="text-gray-400 absolute">Camera inactive</p>
            </div>
        </div>
        <div x-show="message" :class="status === 'success' ? 'bg-green-50 text-green-800 border-green-200' : 'bg-red-50 text-red-800 border-red-200'" class="mt-4 p-4 rounded-lg border text-sm font-medium">
            <span x-text="message"></span>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-6 shadow-sm">
        <h3 class="font-semibold mb-3">Dernieres validations</h3>
        <ul class="divide-y max-h-64 overflow-y-auto">
            <template x-for="log in recentLogs" :key="log.created_at">
                <li class="py-3 flex justify-between items-center text-sm">
                    <span x-text="log.metadata.uuid.substring(0,8) + '...'"></span>
                    <span x-text="new Date(log.created_at).toLocaleTimeString()"></span>
                </li>
            </template>
            <li x-show="recentLogs.length === 0" class="py-3 text-gray-500 text-center">Aucune validation recente</li>
        </ul>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function qrScanner() {
    return {
        uuid: '', signature: '', message: '', status: '', loading: false,
        cameraActive: false, recentLogs: [], scanner: null,
        init() {
            this.scanner = new Html5Qrcode('qr-reader');
            this.startCamera();
        },
        async startCamera() {
            try {
                await this.scanner.start({ facingMode: 'environment' }, { fps: 10, qrbox: 250 }, decoded => {
                    const [uuid, sig] = decoded.includes(':') ? decoded.split(':') : [decoded, ''];
                    this.uuid = uuid.trim(); this.signature = (sig || '').trim();
                    this.validate();
                });
                this.cameraActive = true;
            } catch (e) { this.message = 'Acces camera refuse'; this.status = 'error'; }
        },
        async validate() {
            if (!this.uuid || this.loading) return;
            this.loading = true; this.message = '';
            try {
                const res = await fetch('/scanner/qr/validate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ ticket_uuid: this.uuid, signature: this.signature })
                });
                const data = await res.json();
                this.status = data.status; this.message = data.message;
                if (data.status === 'success') { this.uuid = ''; this.signature = ''; }
                await this.fetchLogs();
            } catch (e) { this.status = 'error'; this.message = 'Erreur reseau'; }
            finally { this.loading = false; }
        },
        async fetchLogs() {
            // Les scanners n'ont pas acces aux logs historiques pour simplifier l'interface
            // Cette fonction peut etre implementee si besoin
        }
    }
}
</script>
@endpush
@endsection
