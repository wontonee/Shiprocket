@php
    $orderId = request()->route('id');
    $order = app('Webkul\Sales\Repositories\OrderRepository')->findOrFail($orderId);
    $shipmentExists = \Wontonee\Shiprocket\Models\ShiprocketOrder::where('order_id', $order->id)->exists();

@endphp

@if ($order->status !== 'canceled')
    @if (!$shipmentExists)
        <a
            href="{{ route('admin.shiprocket.orders.create', $order->id) }}"
            class="transparent-button px-1 py-1.5 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
            onclick="return confirm('{{ __('shiprocket::app.admin.orders.confirm-send-to-shiprocket') }}')"
        >
            <span class="icon-rocket text-2xl"></span>
            {{ __('shiprocket::app.admin.orders.send-to-shiprocket') }}
        </a>
    @else
        <!--<div class="transparent-button px-1 py-1.5 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800">
            <span class="icon-check text-green-600 text-2xl mr-1"></span>
            <span>{{ __('shiprocket::app.admin.orders.already-sent') }}</span>
        </div>//-->

        <a 
            href="{{ route('admin.shiprocket.shipment.view', $order->id) }}"
            class="transparent-button px-1 py-1.5 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
            title="{{ __('shiprocket::app.admin.orders.view-shipment-details') }}"
        >
            <span class="icon-promotion text-2xl"></span>
            {{ __('shiprocket::app.admin.orders.view-shipment-details') }}
        </a>
    @endif
@endif