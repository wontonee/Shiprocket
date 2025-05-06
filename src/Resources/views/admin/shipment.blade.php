<x-admin::layouts>
    <x-slot:title>
        Shiprocket Shipment Tracking
    </x-slot>
    <div class="flex justify-center mt-10">
        <div class="w-full max-w-lg bg-white rounded shadow p-8 pb-16">
            <form method="GET" action="#">
                <div class="mb-6">
                    <label for="awb_number" class="block mb-2 text-sm font-medium text-gray-700">
                        AWB / Tracking / Consignment Number
                    </label>
                    <input type="text" name="awb_number" id="awb_number" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter AWB, Tracking, or Consignment Number" required>
                </div>
                <div class="flex justify-end mt-8 mb-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded shadow">
                        Track
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin::layouts>
