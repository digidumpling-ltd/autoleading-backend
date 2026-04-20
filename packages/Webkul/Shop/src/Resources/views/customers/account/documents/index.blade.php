<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        Document Verification
    </x-slot>

    <!-- Breadcrumbs -->
    @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        @section('breadcrumbs')
            <x-shop::breadcrumbs name="documents" />
        @endSection
    @endif

    <div class="mx-4">
        <x-shop::layouts.account.navigation />
    </div>

    <span class="mb-5 mt-2 w-full border-t border-zinc-300"></span>

    <div class="mx-4">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-zinc-800">Document Verification</h1>
            <p class="mt-2 text-zinc-600">Upload and manage your verification documents</p>
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            @foreach($documentTypes as $type => $label)
                <div class="rounded-lg border border-zinc-200 p-6">
                    <h3 class="text-lg font-semibold text-zinc-800 mb-4">{{ $label }}</h3>

                    @php
                        $document = $documents->where('type', $type)->first();
                    @endphp

                    @if($document)
                        <div class="mb-4">
                            <div class="flex items-center gap-2 text-green-600 mb-2">
                                <span class="icon-check-circle text-xl"></span>
                                <span class="text-sm font-medium">Document Uploaded</span>
                            </div>
                            <p class="text-sm text-zinc-500">{{ $document->original_name }}</p>
                            <p class="text-xs text-zinc-400 mt-1">
                                Uploaded: {{ $document->created_at->format('M d, Y') }}
                            </p>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ Storage::disk('public')->url($document->path) }}"
                               target="_blank"
                               class="flex-1 text-center px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                                View Document
                            </a>
                            <button onclick="replaceDocument('{{ $type }}', '{{ $label }}')"
                                    class="px-4 py-2 text-sm bg-zinc-600 text-white rounded hover:bg-zinc-700">
                                Replace
                            </button>
                        </div>
                    @else
                        <div class="mb-4">
                            <div class="flex items-center gap-2 text-orange-600 mb-2">
                                <span class="icon-exclamation-triangle text-xl"></span>
                                <span class="text-sm font-medium">Document Required</span>
                            </div>
                            <p class="text-sm text-zinc-500">No document uploaded yet</p>
                        </div>

                        <button onclick="uploadDocument('{{ $type }}', '{{ $label }}')"
                                class="w-full px-4 py-2 text-sm bg-green-600 text-white rounded hover:bg-green-700">
                            Upload Document
                        </button>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Upload Modal -->
        <div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-lg max-w-md w-full p-6">
                    <h3 class="text-lg font-semibold mb-4" id="modalTitle">Upload Document</h3>

                    <form id="uploadForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="document_type" id="documentTypeInput">

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-zinc-700 mb-2">
                                Select File
                            </label>
                            <input type="file"
                                   name="document"
                                   id="documentInput"
                                   accept="image/png,image/jpeg,image/webp,application/pdf"
                                   class="w-full px-3 py-2 border border-zinc-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   required>
                            <p class="text-xs text-zinc-500 mt-1">
                                Accepted formats: PNG, JPG, JPEG, WebP, PDF (max 5MB)
                            </p>
                        </div>

                        <div class="flex gap-3">
                            <button type="button"
                                    onclick="closeModal()"
                                    class="flex-1 px-4 py-2 text-sm bg-zinc-600 text-white rounded hover:bg-zinc-700">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="flex-1 px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function uploadDocument(type, label) {
            document.getElementById('modalTitle').textContent = `Upload ${label}`;
            document.getElementById('documentTypeInput').value = type;
            document.getElementById('uploadForm').action = '{{ route("shop.customers.account.documents.upload") }}';
            document.getElementById('uploadModal').classList.remove('hidden');
        }

        function replaceDocument(type, label) {
            document.getElementById('modalTitle').textContent = `Replace ${label}`;
            document.getElementById('documentTypeInput').value = type;
            document.getElementById('uploadForm').action = '{{ route("shop.customers.account.documents.upload") }}';
            document.getElementById('uploadModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('uploadModal').classList.add('hidden');
            document.getElementById('uploadForm').reset();
        }

        // Close modal when clicking outside
        document.getElementById('uploadModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
    @endpush
</x-shop::layouts.account>