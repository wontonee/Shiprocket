<x-admin::layouts>

    <!-- Title of the page -->
    <x-slot:title>
      Pickup Locations
    </x-slot:title>

    <div class="flex gap-4 justify-between max-sm:flex-wrap">
        <p class="py-[11px] text-xl text-gray-800 dark:text-white font-bold">
            <!-- Section Title -->
     Pickup Locations
        </p>
    </div>

    <!-- Pickup Location Card -->
    <div class="flex gap-2.5 mt-7 max-xl:flex-wrap">
        <div class="flex flex-col gap-2 flex-1 max-xl:flex-auto">
            <div class="bg-white dark:bg-gray-900 rounded box-shadow">
                <div class="p-4 pb-0">
                    <p class="text-base text-gray-800 dark:text-white font-semibold mb-4">
                        Select Pickup Location
                    </p>

                    @if (empty($pickupLocations))
                        <div class="grid gap-3.5 justify-center mb-4">
                            <p class="text-gray-600 dark:text-gray-300">
                                No pickup locations found from Shiprocket. Please make sure your Shiprocket account has pickup locations configured.
                            </p>
                        </div>
                    @else
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">
                                Please select a pickup location from your Shiprocket account. This will be used for all shipments.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('admin.shiprocket.pickup.save') }}">
                            @csrf

                            <div class="mb-4">
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label class="required">
                                        Pickup Location
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="select"
                                        name="pickup_location_id"
                                        id="pickup_location_id"
                                        rules="required"
                                        :value="$currentPickupId"
                                        :label="__('Pickup Location')"
                                        onchange="document.getElementById('pickup_location_name').value = this.options[this.selectedIndex].getAttribute('data-name');"
                                    >
                                    <option value="">Select Pickup Location</option>
                                        @foreach ($pickupLocations as $location)
                                            <option 
                                                value="{{ $location['id'] }}" 
                                                data-name="{{ $location['name'] }}"
                                                {{ (string)$currentPickupId === (string)$location['id'] ? 'selected' : '' }}
                                            >
                                                {{ $location['name'] }} - {{ $location['full_address'] }} 
                                            </option>
                                        @endforeach
                                        
                                    <!-- Hidden field to store the pickup location name -->
                                    <input type="hidden" name="pickup_location_name" id="pickup_location_name" value="{{ $currentPickupName }}">
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="pickup_location_id"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>
                            </div>

                            <div class="flex justify-end mb-4">
                                <button 
                                    type="submit" 
                                    class="primary-button"
                                >
                                    Save Location
                                </button>
                            </div>
                        </form>
                    @endif
                </div>

                @if (!empty($pickupLocations) && !empty($currentPickupId))
                    @php
                        $currentLocation = null;
                        foreach ($pickupLocations as $location) {
                            if ($location['id'] == $currentPickupId) {
                                $currentLocation = $location;
                                break;
                            }
                        }
                    @endphp

                    @if ($currentLocation || !empty($currentPickupName))
                        <div class="p-4 border-t border-gray-300 dark:border-gray-800">
                            <p class="text-base text-gray-800 dark:text-white font-semibold mb-4">
                                Current Pickup Location Details
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Name:</p>
                                    <p class="text-sm text-gray-800 dark:text-white">{{ $currentLocation['name'] }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Contact Person:</p>
                                    <p class="text-sm text-gray-800 dark:text-white">{{ $currentLocation['contact_name'] }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Contact Email:</p>
                                    <p class="text-sm text-gray-800 dark:text-white">{{ $currentLocation['contact_email'] }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Contact Phone:</p>
                                    <p class="text-sm text-gray-800 dark:text-white">{{ $currentLocation['contact_phone'] }}</p>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Full Address:</p>
                                    <p class="text-sm text-gray-800 dark:text-white">{{ $currentLocation['full_address'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Script to set pickup location name on page load -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectElement = document.getElementById('pickup_location_id');
            if (selectElement && selectElement.selectedIndex > 0) {
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                document.getElementById('pickup_location_name').value = selectedOption.getAttribute('data-name');
            }
        });
    </script>
</x-admin::layouts>