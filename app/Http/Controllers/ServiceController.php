<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = [
            [
                'id' => 1,
                'name' => 'Website Setup',
                'type' => 'onetime',
                'price' => 199,
                'currency' => 'USD',
                'status' => 'Active',
                'created_at' => now()->subDays(2)->toISOString(),
            ],
        ];

        return Inertia::render('services/Index', [
            'services' => [
                'data' => $services,
                'links' => [],
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('services/Create', [
            'employees' => [
                ['id' => 1, 'name' => 'John Doe'],
                ['id' => 2, 'name' => 'Jane Smith'],
            ],
            'tags' => [
                ['id' => 1, 'name' => 'Design'],
                ['id' => 2, 'name' => 'Development'],
            ],
            'currencies' => ['USD', 'EUR'],
        ]);
    }

    public function store(Request $request)
    {
        return redirect()->route('services.index');
    }

    public function edit(int $service)
    {
        $serviceData = [
            'id' => $service,
            'name' => 'Sample Service',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'type' => 'recurring',
            'price' => 49.99,
            'currency' => 'USD',
            'interval_count' => 1,
            'interval_unit' => 'month',
            'gallery' => [],
            'settings' => [
                'allow_review' => false,
                'group_quantities' => false,
                'set_deadline' => false,
                'show_in_services_page' => true,
            ],
        ];

        return Inertia::render('services/Edit', [
            'service' => $serviceData,
            'employees' => [
                ['id' => 1, 'name' => 'John Doe'],
                ['id' => 2, 'name' => 'Jane Smith'],
            ],
            'tags' => [
                ['id' => 1, 'name' => 'Design'],
                ['id' => 2, 'name' => 'Development'],
            ],
            'currencies' => ['USD', 'EUR'],
        ]);
    }

    public function update(Request $request, int $service)
    {
        return redirect()->route('services.index');
    }

    public function destroy(int $service)
    {
        return back();
    }
}


