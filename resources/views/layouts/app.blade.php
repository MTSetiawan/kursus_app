<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Teacher Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-800">
    {{-- ===== HEADER / NAVBAR ===== --}}
    <header class="sticky top-0 bg-white border-b shadow-sm z-40">
        <div class="container mx-auto flex items-center justify-between py-3 px-4">
            <p class="text-xl font-semibold text-indigo-600">Teacher</p>

            <div class="flex items-center gap-3 relative">
                {{-- (Removed + New Listing button here) --}}

                @php
                    $user = auth()->guard('web')->user();
                    $profile = $user?->teacherProfile;
                    $img =
                        $profile && $profile->profile_image_path
                            ? Storage::url($profile->profile_image_path)
                            : asset('images/default-avatar.png');
                @endphp

                {{-- Avatar (Dropdown Toggle) --}}
                <button id="avatarBtn" type="button" aria-haspopup="true" aria-expanded="false"
                    class="focus:outline-none">
                    <img src="{{ $img }}" alt="Foto Profil"
                        class="h-10 w-10 rounded-full object-cover ring-2 ring-indigo-500">
                </button>

                {{-- Dropdown --}}
                <div id="profileMenu" hidden
                    class="absolute top-12 right-0 bg-white border border-gray-200 rounded-xl shadow-lg w-60 animate-fade-in">
                    <div class="flex items-center gap-3 p-3 border-b">
                        <img src="{{ $img }}" alt="" class="h-10 w-10 rounded-full">
                        <div class="min-w-0">
                            <div class="font-semibold truncate">{{ $user?->name ?? 'Pengguna' }}</div>
                            <div class="text-sm text-gray-500 truncate">{{ $user?->email ?? '' }}</div>
                        </div>
                    </div>

                    <div class="p-2 space-y-1">
                        <a href="{{ route('teacher.profile.edit') }}"
                            class="block px-3 py-2 text-sm rounded-md hover:bg-gray-100">
                            🧑‍🏫 Profil
                        </a>

                        <form id="logoutForm" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-3 py-2 text-sm rounded-md bg-red-500 text-white hover:bg-red-600 transition">
                                ⎋ Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="container mx-auto px-4 py-6">
        @yield('content')
    </main>

    {{-- ===== CREATE LISTING MODAL ===== --}}
    <div id="create-listing-modal" class="modal-backdrop" hidden aria-hidden="true">
        <div class="modal-scrim" data-close="create-listing-modal"></div>
        <div class="modal-card card pad bg-white p-6 rounded-xl shadow-xl w-full max-w-2xl"
            role="dialog" aria-modal="true" aria-labelledby="create-title">
            <div class="flex justify-between items-center mb-4">
                <h2 id="create-title" class="text-lg font-semibold">Buat Listing</h2>
                <button class="btn" type="button" data-close="create-listing-modal">Tutup</button>
            </div>
            <div id="create-listing-body">
                <div class="text-gray-500 text-sm">Memuat formulir…</div>
            </div>
        </div>
    </div>

    {{-- ===== UPGRADE MODAL (used by plan banner) ===== --}}

    {{-- ===== JS (Modal + Dropdown) ===== --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const qs = s => document.querySelector(s);

            const openModal = id => {
                const m = qs('#' + id);
                if (m) {
                    m.removeAttribute('hidden');
                    document.body.style.overflow = 'hidden';
                }
            };
            const closeModal = id => {
                const m = qs('#' + id);
                if (m) {
                    m.setAttribute('hidden', '');
                    document.body.style.overflow = '';
                }
            };

            document.querySelectorAll('[data-close]').forEach(btn => {
                btn.addEventListener('click', () => closeModal(btn.getAttribute('data-close')));
            });
            document.querySelectorAll('.modal-backdrop').forEach(m => {
                m.addEventListener('click', e => {
                    if (e.target.classList.contains('modal-scrim')) closeModal(m.id);
                });
            });

            // Upgrade modal trigger
            document.querySelectorAll('[data-open="upgrade-modal"]').forEach(b => {
                b.addEventListener('click', () => openModal('upgrade-modal'));
            });
            if (location.hash === '#upgrade') openModal('upgrade-modal');

            // Create Listing modal (still works from dashboard)
            const openBtn = qs('#openCreateListing');
            const modalBody = qs('#create-listing-body');

            if (openBtn && modalBody) {
                openBtn.addEventListener('click', async () => {
                    openModal('create-listing-modal');

                    if (!modalBody.dataset.loaded) {
                        try {
                            const res = await fetch("{{ route('listings.create') }}", {
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            const html = await res.text();
                            const temp = document.createElement('div');
                            temp.innerHTML = html;
                            let form = Array.from(temp.querySelectorAll('form')).find(f =>
                                f.querySelector('input[name="title"]') &&
                                (f.querySelector('input[name="category"]') || f.querySelector('select[name="category"]')) &&
                                f.querySelector('select[name="region_id"]') &&
                                f.querySelector('textarea[name="description"]')
                            );
                            if (!form) {
                                const forms = Array.from(temp.querySelectorAll('form')).filter(f => !(f.getAttribute('action') || '').toLowerCase().includes('/logout'));
                                form = forms.sort((a, b) => b.innerHTML.length - a.innerHTML.length)[0];
                            }
                            if (!form) {
                                modalBody.innerHTML = '<div class="text-red-600 text-sm">Form listing tidak ditemukan.</div>';
                                return;
                            }
                            modalBody.innerHTML = '';
                            modalBody.appendChild(form.cloneNode(true));
                            modalBody.dataset.loaded = '1';
                        } catch (e) {
                            modalBody.innerHTML = '<div class="text-red-600 text-sm">Gagal memuat formulir.</div>';
                        }
                    }
                });
            }

            // Avatar dropdown
            const avatarBtn = document.getElementById('avatarBtn');
            const profileMenu = document.getElementById('profileMenu');
            function toggleMenu() {
                if (profileMenu.hidden) {
                    profileMenu.hidden = false;
                    avatarBtn.setAttribute('aria-expanded', 'true');
                } else {
                    profileMenu.hidden = true;
                    avatarBtn.setAttribute('aria-expanded', 'false');
                }
            }

            if (avatarBtn && profileMenu) {
                avatarBtn.addEventListener('click', e => {
                    e.stopPropagation();
                    toggleMenu();
                });
                document.addEventListener('click', e => {
                    if (!profileMenu.hidden && !profileMenu.contains(e.target) && !avatarBtn.contains(e.target)) {
                        profileMenu.hidden = true;
                    }
                });
                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape' && !profileMenu.hidden) {
                        profileMenu.hidden = true;
                        avatarBtn.focus();
                    }
                });
            }
        });
    </script>

    {{-- ===== Basic styling tweaks ===== --}}
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in .15s ease-out;
        }
        .modal-backdrop {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.35);
            backdrop-filter: blur(4px);
            z-index: 50;
        }
    </style>
</body>
</html>
