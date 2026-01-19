<!-- [file name]: resources/views/dashboard/payments/create.blade.php -->
@extends('layouts.dashboard')

@section('title', 'Create Payment - Belleza Rosa')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Record Payment</h1>
            <a href="{{ route('dashboard.payments.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-xl transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to Payments
            </a>
        </div>

        <div class="card">
            <form action="{{ route('dashboard.payments.store') }}" method="POST" data-amount-due="{{ $appointment->total_amount }}">
                @csrf
                <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
                <input type="hidden" name="status" value="paid">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Appointment Info -->
                    <div class="md:col-span-2 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">Appointment Details</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium">Customer:</span> {{ $appointment->customer->full_name ?? 'N/A' }}
                            </div>
                            <div>
                                <span class="font-medium">Service:</span> {{ $appointment->service->name ?? 'Service Unavailable' }}
                            </div>
                            <div>
                                <span class="font-medium">Date:</span>
                                {{ $appointment->start_datetime->format('M j, Y g:i A') }}
                            </div>
                            <div>
                                <span class="font-medium">Amount Due:</span>
                                ₱{{ number_format($appointment->total_amount, 2) }}
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Payment Method *</label>
                        <select name="method" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <option value="">Select Method</option>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>

                    <!-- Amount Due (Read-only) -->
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Amount Due</label>
                        <input type="text" value="₱{{ number_format($appointment->total_amount, 2) }}"
                            readonly
                            id="amountDue"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50 text-gray-700 font-semibold cursor-not-allowed">
                        <input type="hidden" name="amount" value="{{ $appointment->total_amount }}" id="amountInput">
                    </div>

                    <!-- Customer Payment (Only for Cash) -->
                    <div class="form-group" id="customerPaymentGroup">
                        <label class="block text-gray-700 font-semibold mb-2">Amount Received from Customer *</label>
                        <input type="number" step="0.01" name="customer_payment" id="customerPayment"
                            min="{{ $appointment->total_amount }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                            placeholder="Enter amount received">
                        <p class="text-xs text-gray-500 mt-1">Required for cash payments</p>
                    </div>

                    <!-- Change Display -->
                    <div class="form-group hidden" id="changeDisplay">
                        <label class="block text-gray-700 font-semibold mb-2">Change</label>
                        <div class="w-full px-4 py-3 border-2 border-green-300 rounded-xl bg-green-50">
                            <span class="text-2xl font-bold text-green-700" id="changeAmount">₱0.00</span>
                        </div>
                    </div>

                    <!-- Reference Number (Conditional) -->
                    <div id="referenceField" class="form-group hidden">
                        <label class="block text-gray-700 font-semibold mb-2" id="referenceLabel">Reference Number *</label>
                        <input type="text" name="reference_number"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                            placeholder="Enter reference number">
                    </div>

                    <!-- Notes -->
                    <div class="form-group md:col-span-2">
                        <label class="block text-gray-700 font-semibold mb-2">Notes</label>
                        <textarea name="notes" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                            placeholder="Additional payment notes..."></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-4">
                    <a href="{{ route('dashboard.payments.index') }}"
                        class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold rounded-xl shadow-lg transform hover:scale-105 transition">
                        <i class="fas fa-money-bill-wave mr-2"></i> Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const methodSelect = document.querySelector('select[name="method"]');
        const referenceField = document.getElementById('referenceField');
        const referenceLabel = document.getElementById('referenceLabel');
        const referenceInput = document.querySelector('input[name="reference_number"]');
        const customerPaymentGroup = document.getElementById('customerPaymentGroup');
        const customerPaymentInput = document.getElementById('customerPayment');
        const changeDisplay = document.getElementById('changeDisplay');
        const changeAmount = document.getElementById('changeAmount');
        const amountDue = parseFloat(document.querySelector('form[data-amount-due]').getAttribute('data-amount-due'));

        // Handle payment method change
        methodSelect.addEventListener('change', function() {
            const isCash = this.value === 'cash';
            const isDigital = ['gcash', 'bank_transfer'].includes(this.value);

            // Show/hide customer payment field (only for cash)
            if (isCash) {
                customerPaymentGroup.classList.remove('hidden');
                customerPaymentInput.required = true;
            } else {
                customerPaymentGroup.classList.add('hidden');
                customerPaymentInput.required = false;
                customerPaymentInput.value = '';
                changeDisplay.classList.add('hidden');
            }

            // Show/hide reference number field
            if (isDigital) {
                referenceField.classList.remove('hidden');
                referenceLabel.textContent = this.value === 'gcash' 
                    ? 'GCash Reference Number *' 
                    : 'Bank Reference Number *';
                referenceInput.required = true;
            } else {
                referenceField.classList.add('hidden');
                referenceInput.required = false;
                referenceInput.value = '';
            }
        });

        // Calculate change when customer payment is entered
        customerPaymentInput.addEventListener('input', function() {
            const customerPayment = parseFloat(this.value) || 0;
            
            if (customerPayment >= amountDue) {
                const change = customerPayment - amountDue;
                changeAmount.textContent = '₱' + change.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                changeDisplay.classList.remove('hidden');
                this.setCustomValidity('');
            } else if (customerPayment > 0) {
                changeDisplay.classList.add('hidden');
                this.setCustomValidity('Amount received must be equal to or greater than amount due.');
            } else {
                changeDisplay.classList.add('hidden');
                this.setCustomValidity('');
            }
        });

        // Initialize on page load
        if (methodSelect.value === 'cash') {
            customerPaymentGroup.classList.remove('hidden');
            customerPaymentInput.required = true;
        }
    </script>
@endsection
