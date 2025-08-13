<?php

namespace App\Services;

use App\Enums\UserType;
use App\Mail\NewClientAdminNotification;
use App\Mail\WelcomeClientMail;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ClientService
{
    /**
     * Paginate clients with optional search.
     */
    public function paginateClients(string $search = '', int $perPage = 10): LengthAwarePaginator
    {
        return User::query()
            ->where('type', UserType::CLIENT->value)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->with(['profile:id,user_id,company_name'])
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString()
            ->through(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'company_name' => optional($user->profile)->company_name,
                    'created_at' => $user->created_at,
                    'last_login_at' => $user->last_login_at,
                ];
            });
    }

    /**
     * Create a new client with optional avatar/profile and welcome mail.
     */
    public function createClient(array $data, ?UploadedFile $avatar = null, bool $sendWelcome = false): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'] ?? '12345678'),
            'type' => UserType::CLIENT->value,
            'phone' => $data['phone'] ?? null,
        ]);

        if ($avatar) {
            $path = $avatar->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
        }

        $this->upsertProfile($user, $data);

        if ($sendWelcome) {
            Mail::to($user->email)->queue(new WelcomeClientMail($user));
            $adminEmails = User::where('type', UserType::ADMIN->value)->pluck('email')->all();
            if (!empty($adminEmails)) {
                Mail::to($adminEmails)->queue(new NewClientAdminNotification($user));
            }
        }

        if (function_exists('activity')) {
            activity()->performedOn($user)->causedBy(auth()->user())->withProperties(['user_id' => $user->id])->log('client.created');
        }

        return $user;
    }

    /**
     * Update an existing client with optional avatar/profile and welcome mail.
     */
    public function updateClient(User $client, array $data, ?UploadedFile $avatar = null, bool $sendWelcome = false): User
    {
        $client->name = $data['name'];
        $client->email = $data['email'];
        $client->phone = $data['phone'] ?? $client->phone;
        if (!empty($data['password'])) {
            $client->password = Hash::make($data['password']);
        }
        $client->type = UserType::CLIENT->value;
        $client->save();

        if ($avatar) {
            $path = $avatar->store('avatars', 'public');
            $client->avatar = $path;
            $client->save();
        }

        $this->upsertProfile($client, $data);

        if ($sendWelcome) {
            Mail::to($client->email)->queue(new WelcomeClientMail($client));
            $adminEmails = User::where('type', UserType::ADMIN->value)->pluck('email')->all();
            if (!empty($adminEmails)) {
                Mail::to($adminEmails)->queue(new NewClientAdminNotification($client));
            }
        }

        if (function_exists('activity')) {
            activity()->performedOn($client)->causedBy(auth()->user())->withProperties(['user_id' => $client->id])->log('client.updated');
        }

        return $client;
    }

    /**
     * Delete a client.
     */
    public function deleteClient(User $client): void
    {
        $client->delete();
        if (function_exists('activity')) {
            activity()->performedOn($client)->causedBy(auth()->user())->withProperties(['user_id' => $client->id])->log('client.deleted');
        }
    }

    /**
     * Bulk delete clients by ids.
     */
    public function bulkDelete(array $ids): void
    {
        $clients = User::whereIn('id', $ids)->where('type', UserType::CLIENT->value)->get();
        foreach ($clients as $client) {
            $client->delete();
            if (function_exists('activity')) {
                activity()->performedOn($client)->causedBy(auth()->user())->withProperties(['user_id' => $client->id])->log('client.deleted');
            }
        }
    }

    /**
     * Upsert the user's profile fields.
     */
    private function upsertProfile(User $user, array $data): void
    {
        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'address' => $data['address'] ?? null,
                'country_id' => $data['country_id'] ?? null,
                'state' => $data['state'] ?? null,
                'city' => $data['city'] ?? null,
                'post_code' => $data['post_code'] ?? null,
                'company_name' => $data['company_name'] ?? null,
                'tax_id' => $data['tax_id'] ?? null,
            ]
        );
    }
}


