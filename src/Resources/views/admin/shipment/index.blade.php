<x-admin::layouts>
    <!-- Title of the page -->
    <x-slot:title>
        {{ __('shiprocket::app.admin.sales.shipments.title') }}
    </x-slot>
    
    <!-- META for CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="flex gap-[16px] justify-between items-center max-sm:flex-wrap">
        <h1 class="text-[20px] text-gray-800 dark:text-white font-bold">
            {{ __('shiprocket::app.admin.sales.shipments.title') }}
        </h1>
    </div>
    <div class="page-content">
        <div class="grid">
            <!-- Error display from session -->
            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif
            
            <!-- Success message from session -->
            @if (session()->has('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            <!-- DataGrid -->
            <x-admin::datagrid
                src="{{ route('admin.shiprocket.shipment') }}"
            />
        </div>
    </div>
</x-admin::layouts>