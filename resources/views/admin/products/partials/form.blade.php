<div>
    <label for="name" class="text-sm font-semibold text-slate-700">Nom</label>
    <input id="name" name="name" value="{{ old('name', $product->name ?? '') }}" class="form-field mt-2" required>
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <label for="description" class="text-sm font-semibold text-slate-700">Description</label>
    <textarea id="description" name="description" rows="5" class="form-field mt-2">{{ old('description', $product->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="grid gap-5 sm:grid-cols-3">
    <div>
        <label for="price" class="text-sm font-semibold text-slate-700">Prix</label>
        <input id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price ?? '') }}" class="form-field mt-2" required>
        <x-input-error :messages="$errors->get('price')" class="mt-2" />
    </div>
    <div>
        <label for="stock" class="text-sm font-semibold text-slate-700">Stock</label>
        <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $product->stock ?? '') }}" class="form-field mt-2" required>
        <x-input-error :messages="$errors->get('stock')" class="mt-2" />
    </div>
    <div>
        <label for="category_id" class="text-sm font-semibold text-slate-700">Categorie</label>
        <select id="category_id" name="category_id" class="form-field mt-2">
            <option value="">Sans categorie</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
    </div>
</div>

<div class="grid gap-5 sm:grid-cols-3">
    <div>
        <label for="sale_price" class="text-sm font-semibold text-slate-700">Prix promo</label>
        <input id="sale_price" name="sale_price" type="number" step="0.01" min="0" value="{{ old('sale_price', $product->sale_price ?? '') }}" class="form-field mt-2" placeholder="Optionnel">
        <p class="mt-1 text-xs text-slate-500">Doit etre inferieur au prix normal.</p>
        <x-input-error :messages="$errors->get('sale_price')" class="mt-2" />
    </div>
    <div>
        <label for="sale_ends_at" class="text-sm font-semibold text-slate-700">Fin promo</label>
        <input id="sale_ends_at" name="sale_ends_at" type="date" value="{{ old('sale_ends_at', isset($product) && $product->sale_ends_at ? $product->sale_ends_at->format('Y-m-d') : '') }}" class="form-field mt-2">
        <x-input-error :messages="$errors->get('sale_ends_at')" class="mt-2" />
    </div>
    <div class="flex items-end">
        <label class="flex w-full items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700">
            <input name="is_featured" type="checkbox" value="1" @checked(old('is_featured', $product->is_featured ?? false)) class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
            Mettre en avant
        </label>
    </div>
</div>

<div x-data="{ preview: null }">
    <label for="image" class="text-sm font-semibold text-slate-700">Image du produit</label>

    @if(isset($product) && $product->image_url)
        <div class="mt-2 mb-3 flex items-center gap-4">
            <img src="{{ $product->image_url }}" alt="Apercu" class="h-20 w-20 rounded-md border border-slate-200 object-cover shadow-sm">
            <div class="text-xs text-slate-500">
                <p class="font-medium text-slate-700">Image actuelle</p>
                <p class="mt-0.5">Sera remplacee si vous selectionnez un nouveau fichier.</p>
            </div>
        </div>
    @endif

    <template x-if="preview">
        <div class="mt-2 mb-3 flex items-center gap-4 rounded-lg border border-emerald-200 bg-emerald-50 p-3">
            <img :src="preview" alt="Nouvel apercu" class="h-20 w-20 rounded-md border border-emerald-200 object-cover shadow-sm">
            <div class="text-xs text-emerald-800">
                <p class="font-bold">Nouvelle image selectionnee</p>
                <p class="mt-0.5">Verifiez le cadrage avant d'enregistrer.</p>
            </div>
        </div>
    </template>

    <input id="image" name="image" type="file" accept="image/*" class="form-field mt-2 cursor-pointer file:mr-4 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200" @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
    <x-input-error :messages="$errors->get('image')" class="mt-2" />
</div>
