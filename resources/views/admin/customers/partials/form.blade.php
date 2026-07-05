<div>
    <label class="text-sm font-semibold text-slate-700" for="name">Nom</label>
    <input id="name" name="name" value="{{ old('name', $customer->name ?? '') }}" class="form-field mt-2" required>
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>
<div>
    <label class="text-sm font-semibold text-slate-700" for="email">Email</label>
    <input id="email" name="email" type="email" value="{{ old('email', $customer->email ?? '') }}" class="form-field mt-2" required>
    <x-input-error :messages="$errors->get('email')" class="mt-2" />
</div>
<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label class="text-sm font-semibold text-slate-700" for="password">Mot de passe</label>
        <input id="password" name="password" type="password" class="form-field mt-2" @if(!isset($customer)) required @endif>
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>
    <div>
        <label class="text-sm font-semibold text-slate-700" for="password_confirmation">Confirmation</label>
        <input id="password_confirmation" name="password_confirmation" type="password" class="form-field mt-2" @if(!isset($customer)) required @endif>
    </div>
</div>
<div>
    <label class="text-sm font-semibold text-slate-700" for="status">Statut</label>
    <select id="status" name="status" class="form-field mt-2">
        <option value="active" @selected(old('status', $customer->status ?? 'active') === 'active')>Accepte</option>
        <option value="refused" @selected(old('status', $customer->status ?? '') === 'refused')>Refuse</option>
    </select>
    <x-input-error :messages="$errors->get('status')" class="mt-2" />
</div>
