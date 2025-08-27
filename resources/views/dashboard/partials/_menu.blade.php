<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Bloc Menu de navigation --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-md font-semibold text-gray-800">Menu de navigation</h2>

                    @if ($menu->count() < 3)
                        <form method="POST" action="{{ route('dashboard.menu.store') }}" class="flex items-center space-x-2">
                            @csrf
                            <select name="type" id="menuType" class="border-gray-300 rounded text-sm focus:ring focus:ring-indigo-200" onchange="updateItems()" required>
                                <option value="">+ Ajouter</option>
                                <option value="page">📄 Page</option>
                                <option value="category">📂 Catégorie</option>
                            </select>
                            <select name="item_id" id="menuItems" class="border-gray-300 rounded text-sm focus:ring focus:ring-indigo-200 hidden" required>
                                <option value="">Sélectionner...</option>
                            </select>
                            <button type="submit" class="px-2 py-1 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700" id="submitBtn" style="display: none;">
                                OK
                            </button>
                        </form>
                    @endif
                </div>

                @if ($menu->isEmpty())
                    <p class="text-sm text-gray-500">Aucun élément au menu.</p>
                @else
                    <ul class="space-y-2">
                        @foreach ($menu as $item)
                            <li class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded text-sm text-gray-700">
                                <div class="flex items-center space-x-2">
                                    @if($item->page_id)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            📄 Page
                                        </span>
                                        <span>{{ $item->page->title }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            📂 Catégorie
                                        </span>
                                        <span>{{ $item->category->name }}</span>
                                    @endif
                                </div>
                                <form action="{{ route('dashboard.menu.destroy', $item) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 text-sm">✕</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Données pour les options
    const pages = @json($availablePages ?? []);
    const categories = @json($availableCategories ?? []);
    
    // Debug temporaire
    console.log('Pages disponibles:', pages);
    console.log('Catégories disponibles:', categories);

    function updateItems() {
        const type = document.getElementById('menuType').value;
        const itemsSelect = document.getElementById('menuItems');
        const submitBtn = document.getElementById('submitBtn');
        
        // Réinitialiser les options
        itemsSelect.innerHTML = '<option value="">Sélectionner...</option>';
        
        if (type === 'page') {
            pages.forEach(page => {
                const option = document.createElement('option');
                option.value = page.id;
                option.textContent = page.title;
                itemsSelect.appendChild(option);
            });
            itemsSelect.classList.remove('hidden');
            submitBtn.style.display = 'inline-block';
        } else if (type === 'category') {
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                itemsSelect.appendChild(option);
            });
            itemsSelect.classList.remove('hidden');
            submitBtn.style.display = 'inline-block';
        } else {
            itemsSelect.classList.add('hidden');
            submitBtn.style.display = 'none';
        }
    }
</script>
