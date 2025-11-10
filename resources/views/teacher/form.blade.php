{{-- Controller returns view('teacher/form') --}}
@include('teacher.profile', ['user' => auth()->user(), 'profile' => $profile ?? null])
