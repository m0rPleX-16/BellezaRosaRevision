@extends('layouts.dashboard')

@section('title', 'Create Appointment - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Create Appointment</h1>
        <a href="{{ route('dashboard.appointments.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
            &larr; Back to Appointments
        </a>
    </div>

    <!-- Create Appointment Form -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <form action="{{ route('dashboard.appointments.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Basic Information -->
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Customer <span class="text-red-500">*</span>
                        </label>
                        <select name="customer_id" id="customer_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->full_name }} - {{ $customer->phone }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="service_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Service <span class="text-red-500">*</span>
                        </label>
                        <select name="service_id" id="service_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Service</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" 
                                        data-duration="{{ $service->duration_minutes }}"
                                        data-price="{{ $service->price_premium ?? $service->price_regular }}">
                                    {{ $service->name }} - ₱{{ number_format($service->price_premium ?? $service->price_regular, 2) }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Staff <span class="text-red-500">*</span>
                        </label>
                        <select name="staff_id" id="staff_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Staff</option>
                            @foreach ($staff as $staffMember)
                                <option value="{{ $staffMember->id }}">{{ $staffMember->user->full_name }} ({{ $staffMember->formatted_specialty }})</option>
                            @endforeach
                        </select>
                        @error('staff_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-2">
                            Date & Time <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="start_datetime" id="start_datetime" required
                                min="{{ now()->format('Y-m-d\T') . substr($openingTime, 0, 5) }}"
                                max="{{ now()->addDays($maxDaysAhead)->format('Y-m-d\T') . substr($closingTime, 0, 5) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">
                            Business hours: {{ \Carbon\Carbon::createFromFormat('H:i:s', $openingTime)->format('g:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i:s', $closingTime)->format('g:i A') }}
                        </p>
                        @error('start_datetime')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Services -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Additional Services</h2>
                    <button type="button" id="add-addon-btn" 
                            class="text-sm bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-1 rounded-md transition-colors">
                        <i class="fas fa-plus mr-1"></i>Add Service
                    </button>
                </div>
                
                <div id="addons-container" class="space-y-2">
                    <!-- Addons will be dynamically added here -->
                </div>
                
                <div id="no-addons-message" class="text-sm text-gray-500 text-center py-3 bg-gray-50 rounded-lg border border-gray-200">
                    No additional services added
                </div>
            </div>

            <!-- Notes -->
            <div class="p-6 border-b border-gray-200">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Special Requests / Notes
                </label>
                <textarea name="notes" id="notes" rows="3"
                        placeholder="Any special requests or notes..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Price Summary -->
            <div class="p-6 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Price Summary</h3>
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Base Service Price:</span>
                        <span id="base-price" class="font-semibold text-gray-900">₱0.00</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Addons Total:</span>
                        <span id="addons-total" class="font-semibold text-purple-700">₱0.00</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-300">
                        <span class="text-base font-medium text-gray-900">Total Amount:</span>
                        <span id="total-amount" class="text-xl font-bold text-blue-900">₱0.00</span>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="p-6 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('dashboard.appointments.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
                        <i class="fas fa-calendar-check mr-2"></i>Create Appointment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let addonCount = 0;
    const addAddonBtn = document.getElementById('add-addon-btn');
    const addonsContainer = document.getElementById('addons-container');
    const noAddonsMessage = document.getElementById('no-addons-message');
    const serviceSelect = document.getElementById('service_id');
    const basePriceEl = document.getElementById('base-price');
    const addonsTotalEl = document.getElementById('addons-total');
    const totalAmountEl = document.getElementById('total-amount');

    // Update price summary when service changes
    serviceSelect.addEventListener('change', updatePriceSummary);
    
    // Add addon functionality
    addAddonBtn.addEventListener('click', addAddonField);

    function addAddonField() {
        addonCount++;
        
        const addonDiv = document.createElement('div');
        addonDiv.className = 'flex gap-2 items-center p-3 bg-purple-50 border border-purple-200 rounded-lg';
        addonDiv.id = `addon-${addonCount}`;
        
        addonDiv.innerHTML = `
            <select name="addon_service_${addonCount}" 
                    id="addon_service_${addonCount}"
                    class="flex-1 px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-all">
                <option value="">Choose service...</option>
                @foreach ($services as $service)
                    <option value="{{ $service->id }}" 
                            data-price="{{ $service->price_premium ?? $service->price_regular }}"
                            data-name="{{ $service->name }}"
                            data-duration="{{ $service->duration_minutes }}">
                        {{ $service->name }} - ₱{{ number_format($service->price_premium ?? $service->price_regular, 2) }} ({{ $service->duration_minutes }}min)
                    </option>
                @endforeach
                <option value="custom">-- Custom Service --</option>
            </select>
            <input type="text" 
                   name="addon_name_${addonCount}" 
                   id="addon_name_${addonCount}"
                   placeholder="Custom service name" 
                   class="flex-1 px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-all hidden">
            <input type="number" 
                   name="addon_price_${addonCount}" 
                   id="addon_price_${addonCount}"
                   placeholder="Price" 
                   step="0.01" 
                   min="0"
                   class="w-24 px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-purple-300 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition-all"
                   onchange="updatePriceSummary()">
            <button type="button" 
                    onclick="removeAddon(${addonCount})"
                    class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors">
                <i class="fas fa-trash text-sm"></i>
            </button>
        `;
        
        addonsContainer.appendChild(addonDiv);
        noAddonsMessage.style.display = 'none';
        
        // Add event listeners for service selection
        const serviceSelect = addonDiv.querySelector(`#addon_service_${addonCount}`);
        const nameInput = addonDiv.querySelector(`#addon_name_${addonCount}`);
        const priceInput = addonDiv.querySelector(`#addon_price_${addonCount}`);
        
        serviceSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (this.value === 'custom') {
                // Show custom inputs
                nameInput.classList.remove('hidden');
                nameInput.required = true;
                priceInput.required = true;
                priceInput.value = '';
                nameInput.value = '';
            } else if (this.value) {
                // Hide custom inputs and auto-fill from service
                nameInput.classList.add('hidden');
                nameInput.required = false;
                priceInput.required = false;
                priceInput.value = selectedOption.dataset.price;
                nameInput.value = selectedOption.dataset.name;
            } else {
                // No selection
                nameInput.classList.add('hidden');
                nameInput.required = false;
                priceInput.required = false;
                priceInput.value = '';
                nameInput.value = '';
            }
            
            updatePriceSummary();
        });
        
        // Add event listener for price changes
        priceInput.addEventListener('input', updatePriceSummary);
    }
    
    function removeAddon(id) {
        const addonDiv = document.getElementById(`addon-${id}`);
        if (addonDiv) {
            addonDiv.remove();
            updatePriceSummary();
            
            if (addonsContainer.children.length === 0) {
                noAddonsMessage.style.display = 'block';
            }
        }
    }
    
    function updatePriceSummary() {
        // Calculate base price
        const selectedService = serviceSelect.options[serviceSelect.selectedIndex];
        const basePrice = parseFloat(selectedService?.dataset?.price || '0');
        basePriceEl.textContent = `₱${basePrice.toFixed(2)}`;
        
        // Calculate addon prices
        let addonTotal = 0;
        const addonInputs = document.querySelectorAll('input[name^="addon_price_"]');
        addonInputs.forEach(input => {
            addonTotal += parseFloat(input.value) || 0;
        });
        
        addonsTotalEl.textContent = `₱${addonTotal.toFixed(2)}`;
        
        const totalAmount = basePrice + addonTotal;
        totalAmountEl.textContent = `₱${totalAmount.toFixed(2)}`;
    }
    
    // Make removeAddon globally available
    window.removeAddon = removeAddon;
    
    // Initial update
    updatePriceSummary();
});
</script>
@endpush
@endsection