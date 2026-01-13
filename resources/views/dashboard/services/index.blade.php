@extends('layouts.dashboard')

@section('title', 'Services - Belleza Rosa')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Services</h1>
            @if(auth()->user()->isAdmin())
                <button onclick="openAddServiceModal()"
                    class="bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold py-3 px-6 rounded-xl shadow-lg transform hover:-translate-y-1 transition">
                    <i class="fas fa-plus mr-2"></i> Add Service
                </button>
            @endif
        </div>

        @php
            $categoryColors = [
                'Hair Services' => ['bg-gradient-to-r from-purple-500 to-pink-500', 'text-purple-700', 'border-purple-200', 'hover:from-purple-600 hover:to-pink-600'],
                'Nail Services' => ['bg-gradient-to-r from-pink-500 to-rose-500', 'text-pink-700', 'border-pink-200', 'hover:from-pink-600 hover:to-rose-600'],
                'Spa Services' => ['bg-gradient-to-r from-blue-500 to-cyan-500', 'text-blue-700', 'border-blue-200', 'hover:from-blue-600 hover:to-cyan-600'],
                'Full Service' => ['bg-gradient-to-r from-indigo-500 to-purple-500', 'text-indigo-700', 'border-indigo-200', 'hover:from-indigo-600 hover:to-purple-600'],
            ];
            $categoryIcons = [
                'Hair Services' => 'fa-cut',
                'Nail Services' => 'fa-hand-sparkles',
                'Spa Services' => 'fa-spa',
                'Full Service' => 'fa-star',
            ];
        @endphp

        <!-- Category Tabs Navigation -->
        @if(isset($servicesByCategory) && $servicesByCategory->count() > 0)
            <div class="bg-white rounded-xl shadow-lg p-2 border border-gray-200">
                <div class="flex flex-wrap gap-2">
                    @foreach($servicesByCategory as $categoryName => $categoryServices)
                        @php
                            $colorClass = $categoryColors[$categoryName] ?? ['bg-gradient-to-r from-gray-500 to-gray-600', 'text-gray-700', 'border-gray-200', 'hover:from-gray-600 hover:to-gray-700'];
                            $iconClass = $categoryIcons[$categoryName] ?? 'fa-spa';
                            $tabId = 'tab-' . strtolower(str_replace(' ', '-', $categoryName));
                            $categoryId = 'category-' . strtolower(str_replace(' ', '-', $categoryName));
                            $isFirst = $loop->first;
                        @endphp
                        <button onclick="showCategory('{{ $categoryId }}', event)" id="{{ $tabId }}"
                            class="category-tab {{ $isFirst ? 'active' : '' }} px-6 py-3 rounded-lg font-semibold transition-all duration-300 {{ $colorClass[0] }} text-white shadow-md hover:shadow-lg transform hover:scale-105">
                            <i class="fas {{ $iconClass }} mr-2"></i> {{ $categoryName }}
                            <span class="ml-2 bg-white bg-opacity-30 px-2 py-1 rounded-full text-xs">{{ $categoryServices->count() }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Category-specific Views -->
            @foreach($servicesByCategory as $categoryName => $categoryServices)
                @php
                    $colorClass = $categoryColors[$categoryName] ?? ['bg-gradient-to-r from-gray-500 to-gray-600', 'text-gray-700', 'border-gray-200'];
                    $iconClass = $categoryIcons[$categoryName] ?? 'fa-spa';
                    $categoryId = 'category-' . strtolower(str_replace(' ', '-', $categoryName));
                    $isFirst = $loop->first;
                @endphp
                
                <div id="{{ $categoryId }}" class="category-content {{ $isFirst ? '' : 'hidden' }}">
                    <div class="mb-4 flex items-center space-x-3">
                        <div class="{{ $colorClass[0] }} p-3 rounded-xl shadow-lg">
                            <i class="fas {{ $iconClass }} text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold {{ $colorClass[1] }}">{{ $categoryName }}</h2>
                            <p class="text-sm text-gray-500">{{ $categoryServices->count() }} {{ $categoryServices->count() === 1 ? 'service' : 'services' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($categoryServices as $service)
                            <div class="card hover:shadow-xl transition-all duration-300 border-l-4 {{ $colorClass[2] }}">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-xl font-bold text-gray-900">{{ $service->name }}</h3>
                                    <span
                                        class="px-2 py-1 text-xs rounded-full {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $service->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <p class="text-gray-600 mb-4 line-clamp-2">{{ $service->description ?? 'No description available.' }}
                                </p>

                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Duration:</span>
                                        <span class="font-semibold">{{ $service->duration_minutes }} minutes</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Regular Price:</span>
                                        <span
                                            class="font-semibold text-green-600">₱{{ number_format($service->price_regular, 2) }}</span>
                                    </div>
                                    @if ($service->price_premium)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Premium Price:</span>
                                            <span
                                                class="font-semibold text-purple-600">₱{{ number_format($service->price_premium, 2) }}</span>
                                        </div>
                                    @endif
                                </div>

                                @if (auth()->user()->isAdmin())
                                    <div class="flex space-x-2 pt-4 border-t border-gray-200">
                                        <button type="button"
                                            onclick="openEditServiceModal({{ $service->id }}, '{{ addslashes($service->name) }}', {{ $service->category_id }}, {{ $service->duration_minutes }}, {{ $service->price_regular }}, {{ $service->price_premium ?? 'null' }}, {{ json_encode($service->description ?? '') }}, {{ $service->is_premium ? 'true' : 'false' }}, {{ $service->is_active ? 'true' : 'false' }})"
                                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition text-sm font-semibold">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </button>
                                        <button type="button" onclick="openDeleteModal({{ $service->id }}, '{{ addslashes($service->name) }}')"
                                            class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition text-sm font-semibold">
                                            <i class="fas fa-trash mr-1"></i> Delete
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-span-full">
                <div class="card text-center py-12">
                    <i class="fas fa-spa text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-500 mb-2">No Services Found</h3>
                    <p class="text-gray-400 mb-4">Get started by adding your first service.</p>
                    @if(auth()->user()->isAdmin())
                        <button onclick="openAddServiceModal()"
                            class="bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold py-2 px-6 rounded-xl transition">
                            Add Service
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Edit Service Modal -->
    <div id="editServiceModal"
        class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto">
            <div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">Edit Service</h2>
                    <button onclick="closeModal('editServiceModal')"
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form id="editServiceForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="block text-gray-700 font-semibold mb-2">Service Name</label>
                            <input type="text" id="edit_name" name="name" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                placeholder="e.g., Gel Manicure">
                        </div>
                        <div class="form-group">
                            <label class="block text-gray-700 font-semibold mb-2">Category</label>
                            <select id="edit_category_id" name="category_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                                <option value="">Select Category</option>
                                @foreach ($categories ?? [] as $category)
                                    @if (isset($category) && is_object($category))
                                        <option value="{{ $category->id ?? '' }}">{{ $category->name ?? '' }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="block text-gray-700 font-semibold mb-2">Duration (minutes)</label>
                            <input type="number" id="edit_duration_minutes" name="duration_minutes" required min="30"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <p class="text-xs text-gray-500 mt-1">Minimum duration: 30 minutes</p>
                        </div>
                        <div class="form-group">
                            <label class="block text-gray-700 font-semibold mb-2">Regular Price</label>
                            <input type="number" step="0.01" id="edit_price_regular" name="price_regular" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                placeholder="0.00">
                        </div>
                        <div class="form-group">
                            <label class="block text-gray-700 font-semibold mb-2">Premium Price (Optional)</label>
                            <input type="number" step="0.01" id="edit_price_premium" name="price_premium"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                placeholder="0.00">
                        </div>
                        <div class="form-group flex items-center">
                            <input type="checkbox" name="is_premium" id="edit_is_premium" class="mr-2 rounded">
                            <label for="edit_is_premium" class="text-gray-700 font-semibold">Premium Service</label>
                        </div>
                        <div class="form-group flex items-center">
                            <input type="checkbox" name="is_active" id="edit_is_active" class="mr-2 rounded" checked>
                            <label for="edit_is_active" class="text-gray-700 font-semibold">Active Service</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Description</label>
                        <textarea id="edit_description" name="description" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                            placeholder="Service description..."></textarea>
                    </div>
                    <div class="mt-6 flex justify-end space-x-4">
                        <button type="button" onclick="closeModal('editServiceModal')"
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold rounded-xl shadow-lg transform hover:scale-105 transition">
                            <i class="fas fa-save mr-2"></i> Update Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Service Modal -->
    <div id="addServiceModal"
        class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto">
            <div class="bg-gradient-to-r from-blue-900 to-blue-700 text-white p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">Add New Service</h2>
                    <button onclick="closeModal('addServiceModal')"
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form action="{{ route('dashboard.services.store') }}" method="POST" data-toast="true"
                    data-toast-message="Service created successfully!" data-toast-type="success">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="block text-gray-700 font-semibold mb-2">Service Name</label>
                            <input type="text" name="name" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                placeholder="e.g., Gel Manicure">
                        </div>
                        <div class="form-group">
                            <label class="block text-gray-700 font-semibold mb-2">Category</label>
                            <select name="category_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                                <option value="">Select Category</option>
                                @foreach ($categories ?? [] as $category)
                                    @if (isset($category) && is_object($category))
                                        <option value="{{ $category->id ?? '' }}">{{ $category->name ?? '' }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="block text-gray-700 font-semibold mb-2">Duration (minutes)</label>
                            <input type="number" name="duration_minutes" required min="30" value="60"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none">
                            <p class="text-xs text-gray-500 mt-1">Minimum duration: 30 minutes</p>
                        </div>
                        <div class="form-group">
                            <label class="block text-gray-700 font-semibold mb-2">Regular Price</label>
                            <input type="number" step="0.01" name="price_regular" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                placeholder="0.00">
                        </div>
                        <div class="form-group">
                            <label class="block text-gray-700 font-semibold mb-2">Premium Price (Optional)</label>
                            <input type="number" step="0.01" name="price_premium"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                                placeholder="0.00">
                        </div>
                        <div class="form-group flex items-center">
                            <input type="checkbox" name="is_premium" id="is_premium" class="mr-2 rounded">
                            <label for="is_premium" class="text-gray-700 font-semibold">Premium Service</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="block text-gray-700 font-semibold mb-2">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-blue-200 focus:border-blue-600 outline-none"
                            placeholder="Service description..."></textarea>
                    </div>
                    <div class="mt-6 flex justify-end space-x-4">
                        <button type="button" onclick="closeModal('addServiceModal')"
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-yellow-500 hover:bg-yellow-400 text-blue-900 font-bold rounded-xl shadow-lg transform hover:scale-105 transition">
                            <i class="fas fa-plus mr-2"></i> Add Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteServiceModal"
        class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white bg-opacity-20 p-2 rounded-lg">
                            <i class="fas fa-exclamation-triangle text-2xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold">Delete Service</h2>
                    </div>
                    <button onclick="closeDeleteModal()"
                        class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <p class="text-gray-700 text-lg mb-2">Are you sure you want to delete this service?</p>
                    <p class="text-gray-600 font-semibold" id="deleteServiceName"></p>
                    <p class="text-red-600 text-sm mt-2 font-medium">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        This action cannot be undone.
                    </p>
                </div>
                <form id="deleteServiceForm" method="POST" class="mt-6">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeDeleteModal()"
                            class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-semibold">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl shadow-lg transform hover:scale-105 transition font-semibold">
                            <i class="fas fa-trash mr-2"></i> Delete Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Category Tab Navigation
        function showCategory(categoryId, event) {
            // Hide all category contents
            document.querySelectorAll('.category-content').forEach(content => {
                content.classList.add('hidden');
            });

            // Remove active class from all tabs
            document.querySelectorAll('.category-tab').forEach(tab => {
                tab.classList.remove('active');
                tab.classList.remove('ring-4', 'ring-offset-2');
            });

            // Show selected category content
            const selectedContent = document.getElementById(categoryId);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
            }

            // Add active class to clicked tab
            if (event) {
                const clickedTab = event.target.closest('.category-tab');
                if (clickedTab) {
                    clickedTab.classList.add('active', 'ring-4', 'ring-offset-2', 'ring-blue-300');
                }
            } else {
                // Fallback: find tab by categoryId
                const tabId = categoryId.replace('category-', 'tab-');
                const tab = document.getElementById(tabId);
                if (tab) {
                    tab.classList.add('active', 'ring-4', 'ring-offset-2', 'ring-blue-300');
                }
            }
        }

        // Initialize: Show first category by default
        document.addEventListener('DOMContentLoaded', function() {
            const firstTab = document.querySelector('.category-tab.active');
            if (firstTab) {
                firstTab.classList.add('ring-4', 'ring-offset-2', 'ring-blue-300');
            }
        });

        function openAddServiceModal() {
            document.getElementById('addServiceModal').classList.remove('hidden');
        }

        function openEditServiceModal(id, name, categoryId, duration, priceRegular, pricePremium, description, isPremium, isActive) {
            const form = document.getElementById('editServiceForm');
            form.action = `/dashboard/services/${id}`;
            document.getElementById('edit_name').value = name || '';
            document.getElementById('edit_category_id').value = categoryId || '';
            document.getElementById('edit_duration_minutes').value = duration || 60;
            document.getElementById('edit_price_regular').value = priceRegular || 0;
            document.getElementById('edit_price_premium').value = (pricePremium && pricePremium !== 'null') ? pricePremium : '';
            // Handle description - it might be a JSON string or regular string
            let descValue = description || '';
            if (typeof description === 'string' && description.startsWith('"') && description.endsWith('"')) {
                try {
                    descValue = JSON.parse(description);
                } catch (e) {
                    descValue = description.replace(/^"|"$/g, '');
                }
            }
            document.getElementById('edit_description').value = descValue;
            document.getElementById('edit_is_premium').checked = isPremium === true || isPremium === 'true';
            // Set is_active checkbox - default to true if not specified
            document.getElementById('edit_is_active').checked = (isActive === undefined || isActive === null || isActive === true || isActive === 'true');

            document.getElementById('editServiceModal').classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('fixed')) {
                event.target.classList.add('hidden');
            }
        }

        // Handle form submission with toast notification
        document.addEventListener('DOMContentLoaded', function() {
            const editForm = document.getElementById('editServiceForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(editForm);

                    fetch(editForm.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-HTTP-Method-Override': 'PUT'
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                window.location.reload();
                            } else {
                                throw new Error('Failed to update service');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while updating the service. Please try again.');
                        });
                });
            }
        });

        // Delete Service Modal Functions
        function openDeleteModal(serviceId, serviceName) {
            const modal = document.getElementById('deleteServiceModal');
            const form = document.getElementById('deleteServiceForm');
            const serviceNameElement = document.getElementById('deleteServiceName');
            
            // Set the form action
            form.action = `/dashboard/services/${serviceId}`;
            
            // Set the service name
            serviceNameElement.textContent = serviceName;
            
            // Show the modal
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteServiceModal');
            modal.classList.add('hidden');
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('deleteServiceModal');
            if (deleteModal) {
                deleteModal.addEventListener('click', function(event) {
                    if (event.target === deleteModal) {
                        closeDeleteModal();
                    }
                });
            }

            // Handle form submission
            const deleteForm = document.getElementById('deleteServiceForm');
            if (deleteForm) {
                deleteForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(deleteForm);

                    fetch(deleteForm.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-HTTP-Method-Override': 'DELETE'
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                window.location.reload();
                            } else {
                                throw new Error('Failed to delete service');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deleting the service. Please try again.');
                        });
                });
            }
        });
    </script>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection
