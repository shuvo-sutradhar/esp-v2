<?php

namespace App\Http\Controllers;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{

    /**
     * Display a listing of client users with optional text search filtering.
     */
    public function index(Request $request): Response
    {
        $search = (string) $request->string('search');

        $clients = User::query()
            ->where('type', UserType::CLIENT->value)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'email', 'created_at', 'updated_at']);

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
        return Inertia::render('clients/Create');
    }

    /**
     * Store a newly created client user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'type' => UserType::CLIENT->value,
        ]);

        return to_route('clients.index');
    }

    public function edit(User $client): Response
    {
        return Inertia::render('clients/Edit', [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'email_verified_at' => $client->email_verified_at,
                'created_at' => $client->created_at,
                'updated_at' => $client->updated_at,
            ],
        ]);
    }

    /**
     * Update the specified client user in storage.
     */
    public function update(Request $request, User $client): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$client->id],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $client->name = $validated['name'];
        $client->email = $validated['email'];
        if (!empty($validated['password'])) {
            $client->password = Hash::make($validated['password']);
        }
        $client->type = UserType::CLIENT->value;
        $client->save();

        return to_route('clients.index');
    }

    /**
     * Remove the specified client user from storage.
     */
    public function destroy(User $client): RedirectResponse
    {
        $client->delete();
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

        User::whereIn('id', $validated['ids'])
            ->where('type', UserType::CLIENT->value)
            ->delete();

        return back();
    }
}


