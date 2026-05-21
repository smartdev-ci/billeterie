@extends('layouts.admin')

@section('title', 'Configuration de l\'événement')

@section('content')
    <div class="max-w-2xl bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg text-sm">
            ⚠️ Le système gère un seul événement. Le prix du billet est fixé à <strong>5 000 FCFA</strong> (constant).
        </div>
        <form method="POST" action="{{ route('admin.event.update') }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom de l'événement</label>
                    <input type="text" name="name" value="{{ old('name', $event->name) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">{{ old('description', $event->description) }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date & Heure</label>
                        <input type="datetime-local" name="event_date" value="{{ old('event_date', $event->event_date->format('Y-m-d\TH:i')) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lieu</label>
                        <input type="text" name="location" value="{{ old('location', $event->location) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quota maximal</label>
                        <input type="number" name="max_tickets" value="{{ old('max_tickets', $event->max_tickets) }}" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" required min="1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                        <select name="status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            <option value="draft" {{ $event->status === 'draft' ? 'selected' : '' }}>Brouillon</option>
                            <option value="active" {{ $event->status === 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="sold_out" {{ $event->status === 'sold_out' ? 'selected' : '' }}>Complet</option>
                            <option value="closed" {{ $event->status === 'closed' ? 'selected' : '' }}>Clôturé</option>
                        </select>
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50 transition">Annuler</a>
                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
@endsection