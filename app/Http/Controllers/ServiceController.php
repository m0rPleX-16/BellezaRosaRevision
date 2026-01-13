<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        // Check if user has access
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->isStaff())) {
            abort(403, 'Unauthorized access.');
        }

        // Only show active services that are available in the parlor
        $query = Service::where('is_active', true)->with('category');

        // Staff can only see services matching their specialty
        if ($user->isStaff() && $user->staff) {
            $staffSpecialty = $user->staff->specialty ?? 'all';

            // Map staff specialty to allowed category specialties
            $allowedBySpecialty = [
                'hair' => ['hair', 'all'],
                'nail' => ['nail', 'all'],
                'spa' => ['spa', 'all'],
                'hair_nail' => ['hair', 'nail', 'all'],
                'hair_spa' => ['hair', 'spa', 'all'],
                'nail_spa' => ['nail', 'spa', 'all'],
                'all' => ['hair', 'nail', 'spa', 'all'],
            ];

            $allowed = $allowedBySpecialty[$staffSpecialty] ?? ['hair', 'nail', 'spa', 'all'];

            $query->whereHas('category', function ($q) use ($allowed) {
                $q->whereIn('specialty', $allowed);
            });
        }

        $services = $query->get();
        $categories = ServiceCategory::all();

        // Group services by category
        $servicesByCategory = $services->groupBy(function ($service) {
            return $service->category->name ?? 'Uncategorized';
        });

        return view('dashboard.services.index', compact('services', 'categories', 'servicesByCategory'));
    }

    public function create()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $categories = ServiceCategory::all();
        return view('dashboard.services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Service::where('category_id', $request->category_id)
                        ->where('name', $value)
                        ->exists();
                    if ($exists) {
                        $category = \App\Models\ServiceCategory::find($request->category_id);
                        $categoryName = $category ? $category->name : 'this category';
                        $fail("A service named '{$value}' already exists in {$categoryName}. Please choose a different name.");
                    }
                },
            ],
            'category_id' => 'required|exists:service_categories,id',
            'duration_minutes' => 'required|integer|min:30',
            'price_regular' => 'required|numeric|min:0',
            'price_premium' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ], [
            'duration_minutes.min' => 'Service duration must be at least 30 minutes.',
        ]);

        try {
            Service::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'duration_minutes' => $request->duration_minutes,
                'price_regular' => $request->price_regular,
                'price_premium' => $request->price_premium,
                'is_premium' => $request->has('is_premium'),
                'description' => $request->description,
                'is_active' => true,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation
                $category = \App\Models\ServiceCategory::find($request->category_id);
                $categoryName = $category ? $category->name : 'this category';
                return back()
                    ->withErrors(['name' => "A service named '{$request->name}' already exists in {$categoryName}. Please choose a different name."])
                    ->withInput();
            }
            throw $e;
        }

        return redirect()->route('dashboard.services.index')
            ->with('success', 'Service created successfully!');
    }

    public function show(Service $service)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->isStaff())) {
            abort(403, 'Unauthorized access.');
        }

        return view('dashboard.services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $categories = ServiceCategory::all();
        return view('dashboard.services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'category_id' => 'required|exists:service_categories,id',
            'duration_minutes' => 'required|integer|min:30',
            'price_regular' => 'required|numeric|min:0',
            'price_premium' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ], [
            'duration_minutes.min' => 'Service duration must be at least 30 minutes.',
        ]);

        $service->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'duration_minutes' => $request->duration_minutes,
            'price_regular' => $request->price_regular,
            'price_premium' => $request->price_premium,
            'is_premium' => $request->has('is_premium'),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('dashboard.services.index')
            ->with('success', 'Service updated successfully!');
    }

    public function destroy(Service $service)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }

        $service->delete();

        return redirect()->route('dashboard.services.index')
            ->with('success', 'Service deleted successfully!');
    }
}