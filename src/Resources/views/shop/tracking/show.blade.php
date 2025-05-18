<x-shop::layouts>
    <x-slot:title>
        Track Your Shipment
    </x-slot>

    <div class="flex justify-center items-center min-h-[60vh] bg-gray-50">
        <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-2xl">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Tracking Result</h2>

            @if(isset($response['tracking_data']))
                <div class="mb-6">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                        <div>
                            <div class="text-lg font-semibold text-gray-700">AWB Code: <span class="font-mono">{{ $response['tracking_data']['shipment_track'][0]['awb_code'] ?? '-' }}</span></div>
                            <div class="text-gray-600">Status: <span class="font-semibold">{{ $response['tracking_data']['shipment_track'][0]['current_status'] ?? '-' }}</span></div>
                        </div>
                        <div>
                            <a href="{{ $response['tracking_data']['track_url'] ?? '#' }}" target="_blank" class="text-blue-600 hover:underline font-medium">View on Shiprocket</a>
                        </div>
                    </div>
                    <div class="mt-2 text-gray-500 text-sm">Consignee: {{ $response['tracking_data']['shipment_track'][0]['consignee_name'] ?? '-' }} | Destination: {{ $response['tracking_data']['shipment_track'][0]['destination'] ?? '-' }}</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Activity</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Location</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($response['tracking_data']['shipment_track_activities'] as $activity)
                                <tr class="border-t border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $activity['date'] }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $activity['status'] }}</td>
                                    <td class="px-4 py-2">{{ $activity['activity'] }}</td>
                                    <td class="px-4 py-2">{{ $activity['location'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-red-600 font-semibold">No tracking data found.</div>
            @endif
        </div>
    </div>
</x-shop::layouts>
