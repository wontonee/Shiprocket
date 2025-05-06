<x-admin::layouts>
    <x-slot:title>
        Shiprocket Settings
    </x-slot>

    <div class="flex justify-center mt-10">
        <div class="w-full max-w-lg bg-white rounded shadow p-8 pb-16">
            <form method="POST" action="{{ route('admin.shiprocket.settings.save') }}">
                @csrf
                <div class="mb-6">
                    <label for="api_username" class="block mb-2 text-sm font-medium text-gray-700">Shiprocket API Username</label>
                    <input type="text" name="api_username" id="api_username" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('api_username', $apiUsername ?? '') }}" required>
                </div>
                <div class="mb-6">
                    <label for="api_password" class="block mb-2 text-sm font-medium text-gray-700">Shiprocket API Password</label>
                    <input type="password" name="api_password" id="api_password" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('api_password', $apiPassword ?? '') }}" required>
                </div>
                <div class="mb-6">
                    <label for="license_key" class="block mb-2 text-sm font-medium text-gray-700">License Key</label>
                    <input type="text" name="license_key" id="license_key" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('license_key', $licenseKey ?? '') }}" required>
                </div>
                <div class="flex justify-end mt-8 mb-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded shadow">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin::layouts>
