<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Bloc Menu de navigation --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-md font-semibold text-gray-800">Menu de navigation</h2>

                    @if ($menu->count() < 3 && $availablePages->count())
                        <form method="POST" action="{{ route('dashboard.menu.store') }}" class="flex items-center space-x-2">
                            @csrf
                            <select name="page_id" class="border-gray-300 rounded text-sm focus:ring focus:ring-indigo-200" required>
                                <option value="">+ Ajouter</option>
                                @foreach ($availablePages as $page)
                                    <option value="{{ $page->id }}">{{ $page->title }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-2 py-1 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                OK
                            </button>
                        </form>
                    @endif
                </div>

                @if ($menu->isEmpty())
                    <p class="text-sm text-gray-500">Aucune page sélectionnée.</p>
                @else
                    <ul class="space-y-2">
                        @foreach ($menu as $item)
                            <li class="flex justify-between items-center bg-gray-50 px-3 py-2 rounded text-sm text-gray-700">
                                <span>{{ $item->page->title }}</span>
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
