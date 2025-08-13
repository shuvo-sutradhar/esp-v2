<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeamMemberRequest;
use App\Http\Requests\UpdateTeamMemberRequest;
use App\Enums\UserType;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TeamController extends Controller
{
    public function __construct(private readonly TeamService $teamService)
    {
    }

    public function index(Request $request)
    {
        $search = (string) $request->string('search');
        $members = $this->teamService->paginateStaff($search, 10);

        return Inertia::render('settings/team/Index', [
            'members' => $members,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('settings/team/Create');
    }

    public function store(StoreTeamMemberRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->teamService->createStaff($data, $request->file('avatar'));
        return to_route('settings.team.index');
    }

    public function edit(int $id)
    {
        $member = $this->teamService->findStaffOrFail($id);
        return Inertia::render('settings/team/Edit', [
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'email' => $member->email,
                'phone' => $member->phone,
            ],
        ]);
    }

    public function update(UpdateTeamMemberRequest $request, int $id): RedirectResponse
    {
        $member = $this->teamService->findStaffOrFail($id);
        $this->teamService->updateStaff($member, $request->validated(), $request->file('avatar'));
        return to_route('settings.team.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $member = $this->teamService->findStaffOrFail($id);
        $this->teamService->deleteStaff($member);
        return back();
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:users,id'],
        ]);
        $this->teamService->bulkDelete($validated['ids']);
        return back();
    }
}


