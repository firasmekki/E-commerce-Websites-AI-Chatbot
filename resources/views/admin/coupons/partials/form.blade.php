<div>
    <label for="code" class="text-sm font-semibold text-slate-700">Code Coupon</label>
    <input id="code" name="code" value="{{ old('code', $coupon->code ?? '') }}" class="form-field mt-2" placeholder="EX: SOLDE20" required>
    <x-input-error :messages="$errors->get('code')" class="mt-2" />
</div>

<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label for="type" class="text-sm font-semibold text-slate-700">Type de remise</label>
        <select id="type" name="type" class="form-field mt-2" required>
            <option value="percent" @selected(old('type', $coupon->type ?? '') === 'percent')>Pourcentage (%)</option>
            <option value="fixed" @selected(old('type', $coupon->type ?? '') === 'fixed')>Montant fixe (EUR)</option>
        </select>
        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>

    <div>
        <label for="value" class="text-sm font-semibold text-slate-700">Valeur</label>
        <input id="value" name="value" type="number" step="0.01" min="0" value="{{ old('value', $coupon->value ?? '') }}" class="form-field mt-2" placeholder="ex: 15.00" required>
        <x-input-error :messages="$errors->get('value')" class="mt-2" />
    </div>
</div>

<div>
    <label for="expires_at" class="text-sm font-semibold text-slate-700">Date d'expiration (optionnel)</label>
    <input id="expires_at" name="expires_at" type="date" value="{{ old('expires_at', isset($coupon) && $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : '') }}" class="form-field mt-2">
    <x-input-error :messages="$errors->get('expires_at')" class="mt-2" />
</div>

<div class="flex items-center gap-3 mt-2">
    <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', $coupon->is_active ?? true)) class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
    <label for="is_active" class="text-sm font-medium text-slate-700">Coupon Actif</label>
</div>
