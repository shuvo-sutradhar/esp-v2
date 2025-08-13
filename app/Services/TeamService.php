<?php

namespace App\Services;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

class TeamService
{
    public function paginateStaff(string $search = '', int $perPage = 10): LengthAwarePaginator
    {
        return User::query()
            ->where('type', UserType::STAFF->value)
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'phone' => $u->phone,
                'role' => 'Staff',
            ]);
    }

    public function createStaff(array $data, ?UploadedFile $avatar = null): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'type' => UserType::STAFF->value,
            'password' => Hash::make($data['password'] ?? '12345678'),
        ]);

        if ($avatar) {
            $path = $avatar->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
        }

        if (function_exists('activity')) {
            activity()->performedOn($user)->causedBy(auth()->user())->withProperties(['user_id' => $user->id])->log('team.created');
        }

        return $user;
    }

    public function findStaffOrFail(int $id): User
    {
        return User::where('type', UserType::STAFF->value)->findOrFail($id);
    }

    public function updateStaff(User $member, array $data, ?UploadedFile $avatar = null): User
    {
        $member->name = $data['name'];
        $member->email = $data['email'];
        $member->phone = $data['phone'] ?? $member->phone;
        if (!empty($data['password'])) {
            $member->password = Hash::make($data['password']);
        }
        $member->save();

        if ($avatar) {
            $path = $avatar->store('avatars', 'public');
            $member->avatar = $path;
            $member->save();
        }

        if (function_exists('activity')) {
            activity()->performedOn($member)->causedBy(auth()->user())->withProperties(['user_id' => $member->id])->log('team.updated');
        }

        return $member;
    }

    public function deleteStaff(User $member): void
    {
        $member->delete();
        if (function_exists('activity')) {
            activity()->performedOn($member)->causedBy(auth()->user())->withProperties(['user_id' => $member->id])->log('team.deleted');
        }
    }

    public function bulkDelete(array $ids): void
    {
        $members = User::whereIn('id', $ids)->where('type', UserType::STAFF->value)->get();
        foreach ($members as $member) {
            $member->delete();
            if (function_exists('activity')) {
                activity()->performedOn($member)->causedBy(auth()->user())->withProperties(['user_id' => $member->id])->log('team.deleted');
            }
        }
    }
}


