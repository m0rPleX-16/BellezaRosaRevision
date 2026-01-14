@extends('layouts.staff')

@section('title', 'Weekly Schedule - Belleza Rosa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-calendar-alt mr-3 text-blue-600"></i>
                Weekly Schedule
            </h1>
            <p class="text-gray-600 mt-1">Manage your available consultation hours</p>
        </div>
        <button onclick="openAddModal()" 
                class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Add Time Slot
        </button>
    </div>

    <!-- Schedule Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($daysOfWeek as $day)
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <div class="flex items-center mb-4">
                    <i class="fas fa-calendar-day text-blue-600 mr-2"></i>
                    <h3 class="text-lg font-semibold text-gray-900">{{ ucfirst($day) }}</h3>
                </div>
                
                <div class="space-y-3">
                    @if(isset($schedules[$day]) && $schedules[$day]->count() > 0)
                        @foreach($schedules[$day] as $schedule)
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }} - 
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                                        </p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            @if($schedule->max_appointments)
                                                Max {{ $schedule->max_appointments }} appointments/day
                                            @else
                                                Unlimited
                                            @endif
                                        </p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 ml-2">
                                        Active
                                    </span>
                                </div>
                                <div class="flex gap-2 mt-3">
                                    <button data-schedule-id="{{ $schedule->id }}" onclick="openEditModal(this.dataset.scheduleId)" 
                                            class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 py-2 px-3 rounded text-sm font-medium transition">
                                        Edit
                                    </button>
                                    <button data-schedule-id="{{ $schedule->id }}" onclick="deleteSchedule(this.dataset.scheduleId)" 
                                            class="flex-1 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 py-2 px-3 rounded text-sm font-medium transition">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-400 text-sm text-center py-4">No time slots</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Add/Edit Schedule Modal -->
<div id="scheduleModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900" id="modalTitle">Add New Schedule</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="scheduleForm" class="p-6">
            @csrf
            <input type="hidden" id="schedule_id" name="schedule_id">
            
            <!-- Day of Week -->
            <div class="mb-4">
                <label for="day_of_week" class="block text-sm font-medium text-gray-700 mb-2">Day of Week</label>
                <select id="day_of_week" name="day_of_week" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Day</option>
                    <option value="monday">Monday</option>
                    <option value="tuesday">Tuesday</option>
                    <option value="wednesday">Wednesday</option>
                    <option value="thursday">Thursday</option>
                    <option value="friday">Friday</option>
                    <option value="saturday">Saturday</option>
                    <option value="sunday">Sunday</option>
                </select>
            </div>

            <!-- Start Time -->
            <div class="mb-4">
                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                <div class="relative">
                    <input type="time" id="start_time" name="start_time" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <i class="fas fa-clock absolute right-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- End Time -->
            <div class="mb-4">
                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                <div class="relative">
                    <input type="time" id="end_time" name="end_time" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <i class="fas fa-clock absolute right-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- Daily Limit -->
            <div class="mb-6">
                <label for="max_appointments" class="block text-sm font-medium text-gray-700 mb-2">
                    Daily Limit <span class="text-gray-500 text-xs">(Optional)</span>
                </label>
                <input type="number" id="max_appointments" name="max_appointments" min="1"
                    placeholder="Unlimited"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Leave empty for unlimited appointments</p>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button type="button" onclick="closeModal()"
                    class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" id="submitBtn"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition">
                    Add
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentScheduleId = null;

    function openAddModal() {
        currentScheduleId = null;
        const modal = document.getElementById('scheduleModal');
        if (!modal) {
            console.error('Modal element not found');
            alert('Modal not found. Please refresh the page.');
            return;
        }
        document.getElementById('modalTitle').textContent = 'Add New Schedule';
        document.getElementById('submitBtn').textContent = 'Add';
        document.getElementById('scheduleForm').reset();
        document.getElementById('schedule_id').value = '';
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function openEditModal(scheduleId) {
        currentScheduleId = scheduleId;
        document.getElementById('modalTitle').textContent = 'Edit Schedule';
        document.getElementById('submitBtn').textContent = 'Update';
        
        // Fetch schedule data
        fetch(`/staff/schedule/${scheduleId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const schedule = data.schedule;
                    document.getElementById('schedule_id').value = schedule.id;
                    document.getElementById('day_of_week').value = schedule.day_of_week;
                    document.getElementById('start_time').value = schedule.start_time.substring(0, 5);
                    document.getElementById('end_time').value = schedule.end_time.substring(0, 5);
                    document.getElementById('max_appointments').value = schedule.max_appointments || '';
                    const modal = document.getElementById('scheduleModal');
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load schedule data.');
            });
    }

    function closeModal() {
        const modal = document.getElementById('scheduleModal');
        if (modal) {
            modal.style.display = 'none';
        }
        document.body.style.overflow = ''; // Restore scrolling
        currentScheduleId = null;
    }

    function deleteSchedule(scheduleId) {
        if (!confirm('Are you sure you want to delete this schedule?')) {
            return;
        }

        fetch(`/staff/schedule/${scheduleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Failed to delete schedule.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete schedule.');
        });
    }

    // Close modal on outside click
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('scheduleModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        }
        
        // Ensure form exists before adding event listener
        const form = document.getElementById('scheduleForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const data = Object.fromEntries(formData);
                
                const url = currentScheduleId 
                    ? `/staff/schedule/${currentScheduleId}`
                    : '/staff/schedule';
                
                const method = currentScheduleId ? 'PUT' : 'POST';

                fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || 'Failed to save schedule.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to save schedule.');
                });
            });
        }
    });
</script>
@endpush
@endsection
