@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">

                <h2 class="text-xl font-semibold mb-6">Modifier la page Contact</h2>

                @if (session('success'))
                    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('dashboard.contact.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Contenu (TinyMCE) --}}
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-2">Contenu de la page Contact</label>
                        <textarea id="content" name="content" class="w-full border-gray-300 rounded-md shadow-sm" rows="10">{{ old('content', $page->content) }}</textarea>
                    </div>

                    {{-- Bouton --}}
                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-md">
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- TinyMCE from local files --}}
<script src="{{ asset('tinymce/tinymce.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
        selector: '#content',
        license_key: 'gpl',
        height: 500,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
        skin_url: '{{ asset("tinymce/skins/ui/oxide") }}',
        content_css: '{{ asset("tinymce/skins/content/default/content.css") }}'
    });
});
</script>
@endsection
