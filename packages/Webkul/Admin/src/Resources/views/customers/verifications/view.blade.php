@extends('admin::layouts.master')

@section('page_title')
    @lang('admin::app.customers.verifications.detail-title')
@endsection

@section('content')
    <div class="page-content">
        <!-- Back Button -->
        <div class="mb-4">
            <a href="{{ route('admin.customers.verifications.index') }}" class="text-blue-600 hover:text-blue-800">
                ← @lang('admin::app.common.back')
            </a>
        </div>

        <div class="flex gap-6">
            <!-- Customer Information -->
            <div class="flex-1">
                <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                    <h2 class="mb-4 text-lg font-bold text-gray-800 dark:text-white">
                        @lang('admin::app.customers.customers.view.title')
                    </h2>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-600 dark:text-gray-400">
                                @lang('admin::app.customers.customers.datagrid.first-name'):
                            </span>
                            <span class="text-gray-800 dark:text-gray-200">{{ $customer->first_name }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-600 dark:text-gray-400">
                                @lang('admin::app.customers.customers.datagrid.last-name'):
                            </span>
                            <span class="text-gray-800 dark:text-gray-200">{{ $customer->last_name }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-600 dark:text-gray-400">
                                @lang('admin::app.customers.customers.datagrid.email'):
                            </span>
                            <span class="text-gray-800 dark:text-gray-200">{{ $customer->email }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-600 dark:text-gray-400">
                                @lang('admin::app.customers.verifications.datagrid.status'):
                            </span>
                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold 
                                @if($customer->verification_status === 'approved') bg-green-100 text-green-800
                                @elseif($customer->verification_status === 'rejected') bg-red-100 text-red-800
                                @elseif($customer->verification_status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif
                            ">
                                @lang('admin::app.customers.verifications.status.' . $customer->verification_status)
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-600 dark:text-gray-400">
                                @lang('admin::app.customers.customers.datagrid.date'):
                            </span>
                            <span class="text-gray-800 dark:text-gray-200">
                                {{ $customer->created_at->format('M d, Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification Form -->
            <div class="w-80">
                <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                    <h3 class="mb-4 text-lg font-bold text-gray-800 dark:text-white">
                        @lang('admin::app.customers.verifications.update-status')
                    </h3>

                    <form id="verificationForm" onsubmit="submitVerification(event)">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="mb-2 block font-semibold text-gray-700 dark:text-gray-300">
                                @lang('admin::app.customers.verifications.datagrid.status')
                            </label>
                            <select name="verification_status" id="verification_status" 
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="incomplete" {{ $customer->verification_status === 'incomplete' ? 'selected' : '' }}>
                                    @lang('admin::app.customers.verifications.status.incomplete')
                                </option>
                                <option value="pending" {{ $customer->verification_status === 'pending' ? 'selected' : '' }}>
                                    @lang('admin::app.customers.verifications.status.pending')
                                </option>
                                <option value="approved" {{ $customer->verification_status === 'approved' ? 'selected' : '' }}>
                                    @lang('admin::app.customers.verifications.status.approved')
                                </option>
                                <option value="rejected" {{ $customer->verification_status === 'rejected' ? 'selected' : '' }}>
                                    @lang('admin::app.customers.verifications.status.rejected')
                                </option>
                            </select>
                        </div>

                        <div class="mb-4" id="rejectionReasonDiv" style="display: none;">
                            <label class="mb-2 block font-semibold text-gray-700 dark:text-gray-300">
                                @lang('admin::app.customers.verifications.rejection-reason')
                            </label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="3"
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                placeholder="@lang('admin::app.customers.verifications.rejection-reason-placeholder')"></textarea>
                        </div>

                        <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">
                            @lang('admin::app.common.save')
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Documents Section -->
        @if($documents->count())
            <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
                <h3 class="mb-4 text-lg font-bold text-gray-800 dark:text-white">
                    @lang('admin::app.customers.verifications.documents-title')
                </h3>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($documents as $document)
                        <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-600">
                            <div class="mb-2 flex items-center justify-between">
                                <span class="inline-block rounded bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ strtoupper($document->type) }}
                                </span>
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold 
                                    @if($document->status === 'approved') bg-green-100 text-green-800
                                    @elseif($document->status === 'rejected') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif
                                ">
                                    @lang('admin::app.customers.verifications.document-status.' . $document->status)
                                </span>
                            </div>

                            <div class="mb-3 space-y-1 text-sm">
                                <p class="text-gray-600 dark:text-gray-400">
                                    <strong>@lang('admin::app.customers.verifications.file-name'):</strong> {{ $document->original_name }}
                                </p>
                                <p class="text-gray-600 dark:text-gray-400">
                                    <strong>@lang('admin::app.customers.verifications.file-size'):</strong> {{ formatBytes($document->size) }}
                                </p>
                                <p class="text-gray-600 dark:text-gray-400">
                                    <strong>@lang('admin::app.customers.verifications.uploaded-at'):</strong> 
                                    {{ \Carbon\Carbon::parse($document->created_at)->format('M d, Y H:i') }}
                                </p>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('admin.customers.verifications.download', $document->id) }}" 
                                    class="flex-1 rounded bg-gray-200 px-3 py-2 text-center text-sm font-semibold text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                                    @lang('admin::app.common.download')
                                </a>
                                @if($document->status !== 'approved')
                                    <button type="button" onclick="approveDocument({{ $document->id }})"
                                        class="flex-1 rounded bg-green-600 px-3 py-2 text-center text-sm font-semibold text-white hover:bg-green-700">
                                        @lang('admin::app.common.approve')
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="mt-6 rounded-lg border border-yellow-200 bg-yellow-50 p-6 dark:border-yellow-900 dark:bg-yellow-950">
                <p class="text-yellow-800 dark:text-yellow-200">
                    @lang('admin::app.customers.verifications.no-documents')
                </p>
            </div>
        @endif
    </div>

    <script>
        document.getElementById('verification_status').addEventListener('change', function() {
            const rejectionDiv = document.getElementById('rejectionReasonDiv');
            if (this.value === 'rejected') {
                rejectionDiv.style.display = 'block';
            } else {
                rejectionDiv.style.display = 'none';
            }
        });

        function submitVerification(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('verificationForm'));

            fetch('{{ route('admin.customers.verifications.update', $customer->id) }}', {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert('@lang("admin::app.customers.verifications.update-success")');
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
@endsection
