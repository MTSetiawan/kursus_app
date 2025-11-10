<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeacherProfileController extends Controller
{
    /**
     * Display the profile page
     */
    public function show()
    {
        $user = auth()->user();
        $profile = $user->teacherProfile;

        return view('teacher.profile', compact('user', 'profile'));
    }

    /**
     * Show the form for editing the profile (returns partial for AJAX)
     */
    public function edit(Request $request)
    {
        $user = auth()->user();
        $profile = $user->teacherProfile;

        // If AJAX request, return only the form partial
        if ($request->ajax() || $request->wantsJson()) {
            return view('teacher.partials.edit-profile-form', compact('user', 'profile'));
        }

        // Otherwise, return the full profile page
        return view('teacher.profile', compact('user', 'profile'));
    }

    /**
     * Store a newly created profile
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'whatsapp_number' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();
        $profile = $user->teacherProfile ?? $user->teacherProfile()->create([]);

        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($profile->profile_image_path) {
                Storage::disk('public')->delete($profile->profile_image_path);
            }
            $validated['profile_image_path'] = $request->file('profile_image')->store('profiles', 'public');
        }

        $profile->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'name' => $user->name,
                'bio' => $profile->bio,
                'whatsapp_number' => $profile->whatsapp_number,
                'location' => $profile->location,
                'img' => $profile->profile_image_path 
                    ? Storage::url($profile->profile_image_path) 
                    : asset('images/default-avatar.png')
            ]);
        }

        return redirect()->route('teacher.profile')->with('success', 'Profile updated successfully');
    }

    /**
     * Update the profile
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();
        $user->update(['name' => $validated['name']]);

        $profile = $user->teacherProfile ?? $user->teacherProfile()->create([]);

        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($profile->profile_image_path) {
                Storage::disk('public')->delete($profile->profile_image_path);
            }
            $validated['profile_image_path'] = $request->file('profile_image')->store('profiles', 'public');
        }

        unset($validated['name']);
        $profile->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'name' => $user->name,
                'bio' => $profile->bio,
                'whatsapp_number' => $profile->whatsapp_number,
                'location' => $profile->location,
                'img' => $profile->profile_image_path 
                    ? Storage::url($profile->profile_image_path) 
                    : asset('images/default-avatar.png')
            ]);
        }

        return redirect()->route('teacher.profile')->with('success', 'Profile updated successfully');
    }
}