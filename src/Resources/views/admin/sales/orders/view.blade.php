<x-admin::layouts>
    <x-slot:title>
       {{ __('shiprocket::app.admin.sales.orders.view-title') }}
    </x-slot>

    <div class="p-[16px] md:p-[24px] space-y-[24px]">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-[16px]">
            <div class="flex gap-[12px] items-center">
                <a href="{{ url()->previous() }}" class="text-blue-600 hover:text-blue-800 transition-colors">
                    <span class="icon-arrow-left text-[20px]"></span>
                </a>
                <h1 class="text-[18px] font-bold text-gray-800 dark:text-white">
                    {{ __('shiprocket::app.admin.sales.orders.view-title') }} #{{ $order->increment_id }}
                </h1>
            </div>
            
            <div class="flex items-center gap-3">
                @if($shiprocketOrder->status !== 'CANCELED')
                <a href="{{ route('admin.shiprocket.orders.cancel', $order->id) }}" 
                   onclick="return confirm('{{ __('shiprocket::app.admin.sales.orders.cancel-confirm') }}')"
                   class="flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded transition-colors">
                    <span class="icon-cancel text-[18px] mr-1"></span>
                    {{ __('shiprocket::app.admin.sales.orders.cancel') }}
                </a>
                @endif
                
                <a href="https://app.shiprocket.in/orders/{{ $shiprocketOrder->shiprocket_order_id }}" target="_blank" 
                   class="flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition-colors">
                    <span class="icon-external-link text-[18px] mr-1"></span>
                    {{ __('shiprocket::app.admin.orders.view-on-shiprocket') }}
                </a>
            </div>
        </div>

        <!-- Shiprocket Order Info -->
        <div class="bg-white dark:bg-gray-900 rounded-md shadow border border-gray-200 overflow-hidden">
            <div class="p-[20px] border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-[16px] font-semibold text-gray-800 dark:text-white">
                    {{ __('shiprocket::app.admin.sales.orders.shiprocket-details') }}
                </h2>
            </div>

            <div class="p-[20px] grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-[24px]">
                <div>
                    <p class="text-gray-500 text-xs font-medium mb-1">Order ID</p>
                    <p class="text-gray-800 text-sm">{{ $shiprocketOrder->shiprocket_order_id }}</p>
                </div>

                <div>
                    <p class="text-gray-500 text-xs font-medium mb-1">Status</p>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold inline-block
                        @if($shiprocketOrder->status == 'DELIVERED') bg-green-100 text-green-700
                        @elseif($shiprocketOrder->status == 'CANCELED') bg-red-100 text-red-700
                        @elseif(in_array($shiprocketOrder->status, ['SHIPPED', 'IN_TRANSIT'])) bg-blue-100 text-blue-700
                        @else bg-gray-100 text-gray-700 @endif">
                        {{ $shiprocketOrder->status }}
                    </span>
                </div>

                <div>
                    <p class="text-gray-500 text-xs font-medium mb-1">Tracking Number</p>
                    <p class="text-gray-800 text-sm">{{ $shiprocketOrder->tracking_number ?: 'N/A' }}</p>
                </div>

                <div>
                    <p class="text-gray-500 text-xs font-medium mb-1">Courier</p>
                    <p class="text-gray-800 text-sm">{{ $shiprocketOrder->courier_name ?: 'N/A' }}</p>
                </div>
            </div>
        </div>

        <hr class="border-gray-200 my-4">

        <!-- Shipment Table -->
        @if(isset($shiprocketOrderDetails) && !empty($shiprocketOrderDetails))
        <div class="bg-white dark:bg-gray-900 rounded-md shadow border border-gray-200 overflow-hidden">
            <div class="p-[20px] border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-[16px] font-semibold text-gray-800 dark:text-white">
                    {{ __('shiprocket::app.admin.sales.orders.shipment-details') }}
                </h2>
            </div>

            <div class="p-[16px] overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">AWB</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Shipment ID</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Courier</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($shiprocketOrderDetails['shipments']) && count($shiprocketOrderDetails['shipments']) > 0)
                            @foreach($shiprocketOrderDetails['shipments'] as $shipment)
                            <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-3 py-2">{{ $shipment['awb'] ?? 'N/A' }}</td>
                                <td class="px-3 py-2">{{ $shipment['id'] ?? 'N/A' }}</td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        @if(isset($shipment['status']) && strtoupper($shipment['status']) == 'DELIVERED')
                                            bg-green-100 text-green-700
                                        @elseif(isset($shipment['status']) && strtoupper($shipment['status']) == 'CANCELED')
                                            bg-red-100 text-red-700
                                        @elseif(isset($shipment['status']) && in_array(strtoupper($shipment['status']), ['SHIPPED', 'IN_TRANSIT']))
                                            bg-blue-100 text-blue-700
                                        @else
                                            bg-gray-100 text-gray-700
                                        @endif">
                                        {{ $shipment['status'] ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">{{ $shipment['courier_name'] ?? 'N/A' }}</td>
                                <td class="px-3 py-2">
                                    <div class="flex gap-3">
                                        @if(!empty($shipment['tracking_url']))
                                            <a href="{{ $shipment['tracking_url'] }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                                                <span class="icon-location text-[16px] mr-1"></span>
                                                {{ __('shiprocket::app.admin.sales.orders.track') }}
                                            </a>
                                        @endif
                                        
                                        @if(isset($shipment['status']) && strtoupper($shipment['status']) !== 'CANCELED' && $shiprocketOrder->status !== 'CANCELED')
                                            <a href="{{ route('admin.shiprocket.orders.cancel', $order->id) }}" 
                                               onclick="return confirm('{{ __('shiprocket::app.admin.sales.orders.cancel-confirm') }}')"
                                               class="inline-flex items-center text-red-600 hover:text-red-800 text-sm">
                                                <span class="icon-cancel text-[16px] mr-1"></span>
                                                {{ __('shiprocket::app.admin.sales.orders.cancel') }}
                                            </a>
                                        @endif
                                        
                                        @if(empty($shipment['tracking_url']) && (!isset($shipment['status']) || strtoupper($shipment['status']) === 'CANCELED'))
                                            <span class="text-gray-400 text-sm">N/A</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center text-gray-400 italic py-4">
                                    {{ __('shiprocket::app.admin.sales.orders.no-shipments') }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-admin::layouts>
