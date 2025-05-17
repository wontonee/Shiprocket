<x-admin::layouts>
    <x-slot:title>
        @lang('shiprocket::app.admin.sales.orders.view-title', ['order_id' => $order->increment_id])
    </x-slot:title>

    <!-- Header -->
    <div class="grid">
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <div class="flex items-center gap-2.5">
                <p class="text-xl font-bold leading-6 text-gray-800 dark:text-white">
                    @lang('shiprocket::app.admin.sales.orders.view-title', ['order_id' => $order->increment_id])
                </p>

                <!-- Order Status -->
                <span class="text-sm px-2 py-1 rounded-full font-semibold
                    @if($shiprocketOrder->status == 'DELIVERED') bg-green-100 text-green-700
                    @elseif($shiprocketOrder->status == 'CANCELED') bg-red-100 text-red-700
                    @elseif(in_array($shiprocketOrder->status, ['SHIPPED', 'IN_TRANSIT'])) bg-blue-100 text-blue-700
                    @else bg-gray-100 text-gray-700 @endif">
                    {{ $shiprocketOrder->status }}
                </span>
            </div>

            <!-- Back Button -->
            <a
                href="{{ url()->previous() }}"
                class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
            >
                @lang('admin::app.account.edit.back-btn')
            </a>
        </div>
    </div>

    <div class="mt-5 flex-wrap items-center justify-between gap-x-1 gap-y-2">
        <div class="flex gap-1.5">
            @if($shiprocketOrder->status !== 'CANCELED')
            {{-- Order cancellation --}}
                <a href="{{ route('admin.shiprocket.orders.cancel', $order->id) }}"
                   onclick="return confirm('{{ __('shiprocket::app.admin.sales.orders.cancel-confirm') }}')"
                   class="bg-red-100 text-red-700 px-3 py-2 rounded shadow hover:bg-red-200 focus:ring-2 focus:ring-red-400 focus:outline-none transition">
                    {{ __('shiprocket::app.admin.sales.orders.cancel') }}
                </a>
                <a href="{{ route('admin.shiprocket.orders.create.awb', $order->id) }}"
                   onclick="return confirm('{{ __('shiprocket::app.admin.sales.orders.create-awb-confirm') }}')"
                   class="bg-blue-600 text-white px-3 py-2 rounded shadow hover:bg-blue-700 focus:ring-2 focus:ring-blue-400 focus:outline-none transition">
                    {{ __('shiprocket::app.admin.sales.orders.create-awb') }}
                </a>
                @if($shiprocketOrder->awb_status == 1)
                <a href="{{ route('admin.shiprocket.orders.shipment-cancel', $order->id) }}"
                   onclick="return confirm('{{ __('shiprocket::app.admin.sales.orders.cancel-shipment-confirm') }}')"
                   class="bg-yellow-500 text-white px-3 py-2 rounded shadow hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-400 focus:outline-none transition">
                    {{ __('shiprocket::app.admin.sales.orders.shipment-cancel') }}
                </a>
               
                <a href="{{ route('admin.shiprocket.orders.pickup', $order->id) }}"
                   onclick="return confirm('{{ __('shiprocket::app.admin.sales.orders.confirm-pickup') }}')"
                   class="bg-green-600 text-white px-3 py-2 rounded shadow hover:bg-green-700 focus:ring-2 focus:ring-green-400 focus:outline-none transition">
                    {{ __('shiprocket::app.admin.sales.orders.pickup-request') }}
                </a>
                 @endif
            @endif
        </div>

        <!-- Order details with accordion tabs -->
        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
            <!-- Content Component -->
            <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                <!-- Shiprocket Order Information -->
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-600 dark:text-gray-300">
                            @lang('shiprocket::app.admin.sales.orders.shiprocket-details')
                        </p>
                    </x-slot:header>

                    <x-slot:content>
                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Order ID</p>
                                <p class="text-sm text-gray-800 dark:text-white">{{ $shiprocketOrder->shiprocket_order_id }}</p>
                            </div>

                              <div>
                                <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Shipment Id</p>
                                <p class="text-sm text-gray-800 dark:text-white">{{ $shiprocketOrder->shiprocket_shipment_id }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Tracking Number</p>
                                <p class="text-sm text-gray-800 dark:text-white">{{ $shiprocketOrder->tracking_number ?: 'N/A' }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Courier</p>
                                <p class="text-sm text-gray-800 dark:text-white">{{ $shiprocketOrder->courier_name ?: 'N/A' }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">AWB Code</p>
                                <p class="text-sm text-gray-800 dark:text-white">{{ $shiprocketOrder->awb_code ?: 'N/A' }}</p>
                            </div>
                        </div>
                    </x-slot:content>
                </x-admin::accordion>

                <!-- Shiprocket Shipments Information -->
                @if(isset($shiprocketOrderDetails) && !empty($shiprocketOrderDetails))
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-600 dark:text-gray-300">
                            @lang('shiprocket::app.admin.sales.orders.shipment-details')
                            ({{ isset($shiprocketOrderDetails['shipments']) ? count($shiprocketOrderDetails['shipments']) : 0 }})
                        </p>
                    </x-slot:header>

                    <x-slot:content>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                                            AWB
                                        </th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                                            Shipment ID
                                        </th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                                            Status
                                        </th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                                            Courier
                                        </th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">
                                            Actions
                                        </th>
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
                                                            <a href="{{ $shipment['tracking_url'] }}" target="_blank"
                                                                class="text-sm text-blue-600 transition-all hover:underline">
                                                                <span class="icon-location text-[16px] mr-1"></span>
                                                                @lang('shiprocket::app.admin.sales.orders.track')
                                                            </a>
                                                        @endif

                                                        @if(isset($shipment['status']) && strtoupper($shipment['status']) !== 'CANCELED' &&
                                                        $shiprocketOrder->status !== 'CANCELED')
                                                            <a href="{{ route('admin.shiprocket.orders.cancel', $order->id) }}"
                                                                onclick="return confirm('{{ __('shiprocket::app.admin.sales.orders.cancel-confirm') }}')"
                                                                class="text-sm text-red-600 transition-all hover:underline">
                                                                <span class="icon-cancel text-[16px] mr-1"></span>
                                                                @lang('shiprocket::app.admin.sales.orders.cancel')
                                                            </a>
                                                        @endif

                                                        @if(empty($shipment['tracking_url']) && (!isset($shipment['status']) ||
                                                        strtoupper($shipment['status']) === 'CANCELED'))
                                                            <span class="text-gray-400 text-sm">N/A</span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center text-gray-400 italic py-4">
                                                @lang('shiprocket::app.admin.sales.orders.no-shipments')
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </x-slot:content>
                </x-admin::accordion>
                @endif

                <!-- Courier Tab -->
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-600 dark:text-gray-300">
                            @lang('shiprocket::app.admin.menu.courier')
                        </p>
                    </x-slot:header>

                    <x-slot:content>
                        <div class="p-4">
                            @if(isset($shiprocketOrderDetails['courier_name']))
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Courier Name</p>
                                        <p class="text-sm text-gray-800 dark:text-white">{{ $shiprocketOrderDetails['courier_name'] }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">ETD (Estimated Delivery)</p>
                                        <p class="text-sm text-gray-800 dark:text-white">
                                            {{ isset($shiprocketOrderDetails['etd']) ? $shiprocketOrderDetails['etd'] : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400 text-center py-4">
                                    No courier information available for this order.
                                </p>
                            @endif
                        </div>
                    </x-slot:content>
                </x-admin::accordion>
               
            </div>
        </div>
    </div>
</x-admin::layouts>