<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Models\User;
use App\Models\Country;
use App\Services\ClientService;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function __construct(private readonly ClientService $clientService)
    {
    }

    /**
     * Display a listing of client users with optional text search filtering.
     */
    public function index(Request $request): Response
    {
        $search = (string) $request->string('search');
        $clients = $this->clientService->paginateClients($search, 10);

        return Inertia::render('clients/Index', [
            'clients' => $clients,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new client user.
     */
    public function create(): Response
    {
        $countries = Country::orderBy('name')->get(['id', 'name']);

        return Inertia::render('clients/Create', [
            'countries' => $countries,
        ]);
    }

    /**
     * Store a newly created client user in storage.
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->clientService->createClient($data, $request->file('avatar'), (bool) ($data['send_welcome'] ?? false));

        return to_route('clients.index');
    }

    public function edit(User $client): Response
    {
        $client->load('profile');
        $countries = Country::orderBy('name')->get(['id', 'name']);

        return Inertia::render('clients/Edit', [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'avatar' => $client->avatar,
                'email_verified_at' => $client->email_verified_at,
                'created_at' => $client->created_at,
                'updated_at' => $client->updated_at,
                'profile' => [
                    'address' => $client->profile->address ?? null,
                    'country_id' => $client->profile->country_id ?? null,
                    'state' => $client->profile->state ?? null,
                    'city' => $client->profile->city ?? null,
                    'post_code' => $client->profile->post_code ?? null,
                    'company_name' => $client->profile->company_name ?? null,
                    'tax_id' => $client->profile->tax_id ?? null,
                ],
            ],
            'countries' => $countries,
        ]);
    }

    /**
     * Update the specified client user in storage.
     */
    public function update(UpdateClientRequest $request, User $client): RedirectResponse
    {
        $data = $request->validated();
        $this->clientService->updateClient($client, $data, $request->file('avatar'), (bool) ($data['send_welcome'] ?? false));

        return to_route('clients.index');
    }

    /**
     * Remove the specified client user from storage.
     */
    public function destroy(User $client): RedirectResponse
    {
        $this->clientService->deleteClient($client);
        return back();
    }

    /**
     * Bulk delete selected client users.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:users,id'],
        ]);

        app(ClientService::class)->bulkDelete($validated['ids']);

        return back();
    }
}


