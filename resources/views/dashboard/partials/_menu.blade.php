<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Bloc Menu de navigation --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 mr-4">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Gestion du Menu</h2>
                            <p class="text-gray-600">Organisez la navigation de votre site</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('dashboard.menu.store') }}" class="flex items-center space-x-3">
                        @csrf
                        <select name="type" id="menuType" class="border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-200" onchange="updateItems()" required>
                            <option value="">+ Ajouter un élément</option>
                            <option value="page">📄 Page</option>
                            <option value="category">📂 Catégorie</option>
                        </select>
                        <select name="item_id" id="menuItems" class="border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-yellow-200 hidden" required>
                            <option value="">Sélectionner...</option>
                        </select>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200" id="submitBtn" style="display: none;">
                            <span>Ajouter</span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                    </form>
                </div>

                @if ($menu->isEmpty())
                    <div class="text-center text-gray-500 py-8">
                        <p>Aucun élément dans le menu pour le moment.</p>
                    </div>
                @else
                    <div id="menu-sortable" class="grid grid-cols-1 gap-3">
                        @foreach ($menu as $item)
                            <div class="menu-item flex justify-between items-center bg-gray-50 px-4 py-3 rounded-lg border hover:bg-gray-100 transition-colors cursor-move" data-id="{{ $item->id }}">
                                <div class="flex items-center space-x-3">
                                    <div class="drag-handle cursor-grab active:cursor-grabbing mr-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                        </svg>
                                    </div>
                                    @if($item->page_id)
                                        <div class="p-2 rounded-full bg-blue-100">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $item->page->title }}</span>
                                            <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Page
                                            </span>
                                        </div>
                                    @else
                                        <div class="p-2 rounded-full bg-purple-100">
                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-900">{{ $item->category->name }}</span>
                                            <span class="inline-flex items-center ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Catégorie
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                <form action="{{ route('dashboard.menu.destroy', $item) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="p-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
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

    // Initialiser SortableJS pour le drag and drop
    document.addEventListener('DOMContentLoaded', function() {
        const menuList = document.getElementById('menu-sortable');

        if (menuList) {
            new Sortable(menuList, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'bg-yellow-100',
                dragClass: 'opacity-50',
                onEnd: function(evt) {
                    // Récupérer le nouvel ordre
                    const items = [];
                    document.querySelectorAll('.menu-item').forEach((item, index) => {
                        items.push({
                            id: item.dataset.id,
                            order: index + 1
                        });
                    });

                    // Envoyer l'ordre au serveur
                    fetch('{{ route('dashboard.menu.reorder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ items: items })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Ordre mis à jour avec succès');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la mise à jour de l\'ordre:', error);
                        alert('Erreur lors de la sauvegarde de l\'ordre');
                    });
                }
            });
        }
    });
</script>
