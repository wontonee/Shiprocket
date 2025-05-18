<x-shop::layouts>
    <x-slot:title>
        Track Your Shipment
    </x-slot>
    <div class="flex justify-center items-center min-h-[60vh] bg-gray-50 ">
        <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Track Your Shipment</h2>
            <div class="space-y-6">
                <v-shiprocket-tracking
                    action="{{ route('shiprocket.shop.tracking.show') }}"
                    csrf-token="{{ csrf_token() }}"
                ></v-shiprocket-tracking>
            </div>
        </div>
    </div>
    @pushOnce('scripts')
    <script type="text/x-template" id="v-shiprocket-tracking-template">
        <form :action="action" method="POST" @submit.prevent="submitForm" class="space-y-6">
            <input type="hidden" name="_token" :value="csrfToken">
            <div>
                <label for="awb_number" class="block text-base font-medium text-gray-700 mb-2">AWB Number / Shipment ID</label>
                <input type="text" id="awb_number" name="awb_number" v-model="formData.awb_number" required
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-lg"
                    placeholder="Enter AWB or Shipment ID">
            </div>
            <div class="flex gap-4 justify-center">
                <label class="inline-flex items-center">
                    <input type="radio" name="trackingby" value="1" v-model="formData.trackingby" class="accent-blue-600">
                    <span class="ml-2 text-gray-700">By Shipment ID</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" name="trackingby" value="2" v-model="formData.trackingby" class="accent-blue-600">
                    <span class="ml-2 text-gray-700">By AWB</span>
                </label>
            </div>
            <div class="flex justify-center">
                <button type="submit" style="background-color:#2563eb;color:#fff;" class="font-semibold px-8 py-3 rounded-md shadow-md transition text-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    Track
                </button>
            </div>
        </form>
    </script>
    <script type="module">
        app.component('v-shiprocket-tracking', {
            template: '#v-shiprocket-tracking-template',
            props: {
                action: { type: String, required: true },
                csrfToken: { type: String, required: true },
            },
            data() {
                return {
                    formData: {
                        awb_number: '',
                        trackingby: '1',
                    }
                };
            },
            methods: {
                submitForm() {
                    this.$el.submit();
                }
            }
        });
    </script>
@endPushOnce
</x-shop::layouts>
