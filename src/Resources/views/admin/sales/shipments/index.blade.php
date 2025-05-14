@php
    $hasPermission = bouncer()->hasPermission('sales.shipments.create');
@endphp

<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shiprocket::app.admin.sales.shipments.title')
    </x-slot>

    <!-- Top Toolbar -->
    <div class="flex items-center justify-between gap-4 max-md:flex-wrap">
        <!-- Title -->
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('shiprocket::app.admin.sales.shipments.title')
        </p>
    </div>

    <!-- Shipment Datagrid with No Search Box -->
    <x-admin::datagrid 
        :src="route('admin.shiprocket.shipments.index')"
    >
        <!-- Override toolbar components to remove search -->
        <template #toolbar-left>
            @include('shiprocket::admin.components.datagrid.toolbar.no-search')
        </template>
    </x-admin::datagrid>
</x-admin::layouts>
