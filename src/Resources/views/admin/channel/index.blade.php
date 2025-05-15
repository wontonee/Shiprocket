<x-admin::layouts>

    <!-- Title of the page -->
    <x-slot:title>
    Channels
    </x-slot:title>

    <div class="flex gap-4 justify-between max-sm:flex-wrap">
        <p class="py-[11px] text-xl text-gray-800 dark:text-white font-bold">
            <!-- Section Title -->
        Channels
        </p>
    </div>

    <!-- Channel Card -->
    <div class="flex gap-2.5 mt-7 max-xl:flex-wrap">
        <div class="flex flex-col gap-2 flex-1 max-xl:flex-auto">
            <div class="bg-white dark:bg-gray-900 rounded box-shadow">
                <div class="p-4 pb-0">
                    <p class="text-base text-gray-800 dark:text-white font-semibold mb-4">
                        Select Shiprocket Channel
                    </p>

                    @if (empty($channels))
                        <div class="grid gap-3.5 justify-center mb-4">
                            <p class="text-gray-600 dark:text-gray-300">
                                No channels found from Shiprocket. Please make sure your Shiprocket account has channels configured.
                            </p>
                        </div>
                    @else
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">
                                Please select a channel from your Shiprocket account. This will be used for all shipments.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('admin.shiprocket.channel.save') }}">
                            @csrf

                            <div class="mb-4">
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label class="required">
                                        Shiprocket Channel
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="select"
                                        name="channel_id"
                                        id="channel_id"
                                        rules="required"
                                        :value="$currentChannelId"
                                        :label="__('Shiprocket Channel')"
                                    >
                                        @foreach ($channels as $channel)
                                            <option 
                                                value="{{ $channel['id'] }}" 
                                                {{ (string)$currentChannelId === (string)$channel['id'] ? 'selected' : '' }}
                                            >
                                             {{ $channel['id']  }}  - {{ $channel['name'] }} {{ !empty($channel['brand_name']) ? '- ' . $channel['brand_name'] : '' }}
                                            </option>
                                        @endforeach
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="channel_id"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>
                            </div>

                            <div class="flex justify-end mb-4">
                                <button 
                                    type="submit" 
                                    class="primary-button"
                                >
                                    Save Channel
                                </button>
                            </div>
                        </form>
                    @endif
                </div>

                @if (!empty($channels) && !empty($currentChannelId))
                    @php
                        $currentChannel = null;
                        foreach ($channels as $channel) {
                            if ($channel['id'] == $currentChannelId) {
                                $currentChannel = $channel;
                                break;
                            }
                        }
                    @endphp

                    @if ($currentChannel)
                        <div class="p-4 border-t border-gray-300 dark:border-gray-800">
                            <p class="text-base text-gray-800 dark:text-white font-semibold mb-4">
                                Current Channel Details
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Name:</p>
                                    <p class="text-sm text-gray-800 dark:text-white">{{ $currentChannel['name'] }}</p>
                                </div>
                                
                                @if(!empty($currentChannel['brand_name']))
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Brand Name:</p>
                                    <p class="text-sm text-gray-800 dark:text-white">{{ $currentChannel['brand_name'] }}</p>
                                </div>
                                @endif
                                
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Status:</p>
                                    <p class="text-sm text-gray-800 dark:text-white">{{ $currentChannel['status'] }}</p>
                                </div>
                                
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Last Updated:</p>
                                    <p class="text-sm text-gray-800 dark:text-white">{{ $currentChannel['channel_updated_at'] }}</p>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-300 font-medium">Sync Status:</p>
                                    <div class="flex flex-wrap gap-4 mt-2">
                                        <span class="inline-flex items-center px-3 py-1 text-xs rounded bg-{{ $currentChannel['orders_sync'] ? 'green' : 'gray' }}-100 text-{{ $currentChannel['orders_sync'] ? 'green' : 'gray' }}-600">
                                            Orders: {{ $currentChannel['orders_sync'] ? 'Enabled' : 'Disabled' }}
                                        </span>
                                        <span class="inline-flex items-center px-3 py-1 text-xs rounded bg-{{ $currentChannel['inventory_sync'] ? 'green' : 'gray' }}-100 text-{{ $currentChannel['inventory_sync'] ? 'green' : 'gray' }}-600">
                                            Inventory: {{ $currentChannel['inventory_sync'] ? 'Enabled' : 'Disabled' }}
                                        </span>
                                        <span class="inline-flex items-center px-3 py-1 text-xs rounded bg-{{ $currentChannel['catalog_sync'] ? 'green' : 'gray' }}-100 text-{{ $currentChannel['catalog_sync'] ? 'green' : 'gray' }}-600">
                                            Catalog: {{ $currentChannel['catalog_sync'] ? 'Enabled' : 'Disabled' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

</x-admin::layouts>