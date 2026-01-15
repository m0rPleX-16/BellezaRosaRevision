<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Staff;
use App\Models\StaffSchedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DashboardFilterTestSeeder extends Seeder
{
    private const MARKER = '[seed:dashboard-filter]';

    public function run(): void
    {
        // Clean previous seeded data (idempotent).
        DB::transaction(function () {
            Payment::query()
                ->where('notes', 'like', '%' . self::MARKER . '%')
                ->delete();

            Appointment::query()
                ->where('notes', 'like', '%' . self::MARKER . '%')
                ->delete();
        });

        $customers = Customer::query()->inRandomOrder()->limit(50)->get();
        $staffMembers = Staff::query()->with('schedules')->get();
        $services = Service::query()
            ->with('category')
            ->where('is_active', true)
            ->get();

        if ($customers->isEmpty() || $staffMembers->isEmpty() || $services->isEmpty()) {
            // Ensure you ran: php artisan db:seed (DatabaseSeeder) first.
            $this->command?->warn('Missing customers/staff/services. Run `php artisan db:seed` first.');
            return;
        }

        // Ensure each staff has at least a basic schedule (Mon-Sat 09:00-18:00) for seeding.
        foreach ($staffMembers as $staff) {
            $hasActive = $staff->schedules->where('is_active', true)->count() > 0;
            if ($hasActive) {
                continue;
            }

            foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day) {
                StaffSchedule::query()->updateOrCreate(
                    [
                        'staff_id' => $staff->id,
                        'day_of_week' => $day,
                        'start_time' => '09:00:00',
                        'end_time' => '18:00:00',
                    ],
                    [
                        'max_appointments' => null,
                        'is_active' => true,
                    ]
                );
            }
        }

        // Reload schedules after potential creation.
        $staffMembers = Staff::query()->with('schedules')->get();

        // Generate appointments across multiple date ranges for dashboard filters.
        // We intentionally spread data across: today, this week, this month, last month, and earlier this year.
        $datesToSeed = collect([
            now()->toDateString(),
            now()->subDays(1)->toDateString(),
            now()->subDays(3)->toDateString(),
            now()->addDays(1)->toDateString(),
            now()->addDays(4)->toDateString(),
            now()->subDays(10)->toDateString(),
            now()->subDays(20)->toDateString(),
            now()->subMonthNoOverflow()->startOfMonth()->addDays(3)->toDateString(),
            now()->subMonthNoOverflow()->addDays(12)->toDateString(),
            now()->subMonthsNoOverflow(5)->addDays(7)->toDateString(),
        ])->unique()->values();

        $methods = ['cash', 'gcash', 'bank_transfer'];
        $statusesFuture = ['scheduled', 'confirmed'];
        $statusesPast = ['completed', 'no_show', 'cancelled', 'failed'];

        foreach ($datesToSeed as $date) {
            $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));

            foreach ($staffMembers as $staff) {
                $schedule = $staff->schedules
                    ->where('is_active', true)
                    ->firstWhere('day_of_week', $dayOfWeek);

                if (!$schedule) {
                    continue;
                }

                // Pick a service compatible with staff specialty (best-effort).
                $service = $this->pickServiceForStaff($services, $staff->specialty);
                if (!$service) {
                    continue;
                }

                $duration = max((int)($service->duration_minutes ?? 60), 30);
                $start = $this->pickAvailableStartTime(
                    staffId: $staff->id,
                    date: $date,
                    scheduleStart: (string)$schedule->start_time,
                    scheduleEnd: (string)$schedule->end_time,
                    durationMinutes: $duration
                );

                if (!$start) {
                    continue;
                }

                $startDt = Carbon::parse($start);
                $endDt = $startDt->copy()->addMinutes($duration);

                $isPast = $startDt->lt(now()->startOfDay());
                $isToday = $startDt->isToday();
                $isFuture = $startDt->gt(now());

                $status = $isPast
                    ? $statusesPast[array_rand($statusesPast)]
                    : ($isToday ? 'in_progress' : $statusesFuture[array_rand($statusesFuture)]);

                // If appointment is cancelled/no_show/failed, keep payment unpaid.
                $paymentMethod = $status === 'completed' ? $methods[array_rand($methods)] : 'unpaid';

                $customer = $customers->random();
                $amount = ($service->price_premium ?? $service->price_regular ?? 0);

                $appointment = Appointment::query()->create([
                    'customer_id' => $customer->id,
                    'staff_id' => $staff->id,
                    'service_id' => $service->id,
                    'start_datetime' => $startDt,
                    'end_datetime' => $endDt,
                    'status' => $status,
                    'payment_method' => $paymentMethod,
                    'total_amount' => $amount,
                    'notes' => self::MARKER . ' Seeded for dashboard filter testing',
                    'is_walk_in' => false,
                ]);

                // Create a payment only for completed appointments (keeps your payment logic consistent).
                if ($appointment->status === 'completed' && $amount > 0) {
                    Payment::query()->create([
                        'appointment_id' => $appointment->id,
                        'customer_id' => $appointment->customer_id,
                        'amount' => $amount,
                        'method' => $paymentMethod === 'unpaid' ? 'cash' : $paymentMethod,
                        'status' => 'paid',
                        'paid_at' => $endDt->copy()->addMinutes(5),
                        'notes' => self::MARKER . ' Seeded payment for dashboard filter testing',
                        'payment_details' => [],
                    ]);
                }
            }
        }

        $this->command?->info('DashboardFilterTestSeeder: seeded appointments/payments for dashboard filter testing.');
    }

    private function pickServiceForStaff($services, ?string $staffSpecialty): ?Service
    {
        $specialty = $staffSpecialty ?: 'all';

        // Staff specialty "all" can take any active service.
        if ($specialty === 'all') {
            return $services->random();
        }

        $filtered = $services->filter(function (Service $service) use ($specialty) {
            $cat = $service->category;
            $catSpecialty = $cat?->specialty;
            return $catSpecialty === $specialty || $catSpecialty === 'both';
        })->values();

        return $filtered->isNotEmpty() ? $filtered->random() : ($services->isNotEmpty() ? $services->random() : null);
    }

    private function pickAvailableStartTime(
        int $staffId,
        string $date,
        string $scheduleStart,
        string $scheduleEnd,
        int $durationMinutes
    ): ?string {
        $day = Carbon::parse($date)->format('Y-m-d');

        // Normalize schedule time (handles "09:00:00" vs "2026-01-15 09:00:00").
        $startTime = strlen($scheduleStart) > 8 ? Carbon::parse($scheduleStart)->format('H:i:s') : $scheduleStart;
        $endTime = strlen($scheduleEnd) > 8 ? Carbon::parse($scheduleEnd)->format('H:i:s') : $scheduleEnd;

        $windowStart = Carbon::parse($day . ' ' . $startTime);
        $windowEnd = Carbon::parse($day . ' ' . $endTime);

        if ($windowEnd->lte($windowStart)) {
            return null;
        }

        // Pull existing appointments for that staff/date to avoid overlaps.
        $existing = Appointment::query()
            ->where('staff_id', $staffId)
            ->whereDate('start_datetime', $day)
            ->get(['start_datetime', 'end_datetime']);

        // Try up to N random slots on a 30-minute grid.
        $interval = 30;
        $latestStart = $windowEnd->copy()->subMinutes($durationMinutes);
        if ($latestStart->lt($windowStart)) {
            return null;
        }

        for ($attempt = 0; $attempt < 30; $attempt++) {
            $candidate = $windowStart->copy()->addMinutes(rand(0, max(0, (int)$windowStart->diffInMinutes($latestStart))));
            // Snap to interval
            $minutes = (int)$candidate->format('i');
            $snap = (int)(floor($minutes / $interval) * $interval);
            $candidate->setTime((int)$candidate->format('H'), $snap, 0);

            $candidateEnd = $candidate->copy()->addMinutes($durationMinutes);
            if ($candidateEnd->gt($windowEnd)) {
                continue;
            }

            $overlaps = $existing->contains(function ($appt) use ($candidate, $candidateEnd) {
                $apptStart = Carbon::parse($appt->start_datetime);
                $apptEnd = $appt->end_datetime ? Carbon::parse($appt->end_datetime) : $apptStart->copy()->addMinutes(60);
                return $candidate->lt($apptEnd) && $candidateEnd->gt($apptStart);
            });

            if (!$overlaps) {
                return $candidate->toDateTimeString();
            }
        }

        return null;
    }
}

