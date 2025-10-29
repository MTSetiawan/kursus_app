<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\TeacherProfile;
use App\Models\UserPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherProfileController extends Controller
{
    public function store(Request $request)
    {
        abort_if(auth()->guard('web')->user()->teacherProfile, 403, 'Profil sudah ada, gunakan update.');

        $data = $request->validate([
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $data['user_id'] = auth()->guard('web')->id();

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');
            $data['profile_image_path'] = $path;
        }

        $profile = TeacherProfile::create($data);

        // auto give Bronze plan
        if (!auth()->guard('web')->user()->activePlan) {
            $free = Plan::where('name', 'Bronze')->first();
            if ($free) {
                UserPlan::create([
                    'user_id' => auth()->id(),
                    'plan_id' => $free->id,
                    'start_date' => now(),
                    'end_date' => now()->addDays($free->duration_days),
                    'status' => 'active',
                ]);
            }
        }

        return redirect()->route('teacher.dashboard')->with('success', 'Profil guru berhasil dibuat.');
    }

    public function edit()
    {
        $user = auth()->guard('web')->user();
        $profile = $user->teacherProfile;
        return view('teacher.partials.edit-profile-form', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = auth()->guard('web')->user();
        $profile = $user->teacherProfile;
        abort_unless($profile, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'location' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        // update user name
        $user->update(['name' => $data['name']]);

        // handle upload
        if ($request->hasFile('profile_image')) {
            if ($profile->profile_image_path) {
                Storage::disk('public')->delete($profile->profile_image_path);
            }
            $path = $request->file('profile_image')->store('profiles', 'public');
            $data['profile_image_path'] = $path;
        }

        unset($data['profile_image']);
        unset($data['name']);

        $profile->update($data);

        // === AJAX ===
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'name' => $user->name,
                'bio' => $profile->bio,
                'whatsapp_number' => $profile->whatsapp_number,
                'location' => $profile->location,
                'img' => $profile->profile_image_path
                    ? asset('storage/'.$profile->profile_image_path)
                    : asset('images/default-avatar.png'),
            ]);
        }

        return redirect()->route('teacher.dashboard')->with('success', 'Profil guru diperbarui.');
    }
}
