<div>
    <label for="name" class="text-sm font-semibold text-slate-700">Nom de la catégorie</label>
    <input id="name" name="name" value="{{ old('name', $category->name ?? '') }}" class="form-field mt-2" required>
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <label for="description" class="text-sm font-semibold text-slate-700">Description</label>
    <textarea id="description" name="description" rows="4" class="form-field mt-2" placeholder="Description optionnelle de la catégorie...">{{ old('description', $category->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>
