<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Teacher Dashboard' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <header class="navbar">
        <div class="nav-wrap container">
            <p class="brand">Teacher</p>

            <div style="display:flex;gap:10px;align-items:center;position:relative">
                {{-- Tombol New Listing --}}
                <button class="btn primary" type="button" id="openCreateListing">+ New Listing</button>

                @php
                    $user = auth()->guard('web')->user();
                    $profile = $user?->teacherProfile;
                    $img =
                        $profile && $profile->profile_image_path
                            ? Storage::url($profile->profile_image_path)
                            : asset('images/default-avatar.png');
                @endphp

                {{-- AVATAR BUTTON (toggle dropdown) --}}
                <button id="avatarBtn" type="button" aria-haspopup="true" aria-expanded="false"
                    style="border:none;background:transparent;padding:0;cursor:pointer">
                    <img src="{{ $img }}" alt="Foto Profil" width="36" height="36"
                        class="rounded-full object-cover h-10 w-10">
                </button>

                {{-- DROPDOWN MENU --}}
                <div id="profileMenu" role="menu" aria-labelledby="avatarBtn" hidden
                    style="position:absolute; top:48px; right:0; min-width:240px; background:#fff; border:1px solid #e5e7eb; border-radius:12px; box-shadow:0 10px 20px rgba(0,0,0,.08); padding:10px; z-index:50">
                    <div
                        style="display:flex; gap:10px; align-items:center; padding:8px 8px 10px 8px; border-bottom:1px solid #f3f4f6">
                        <img src="{{ $img }}" alt="" width="40" height="40"
                            class="rounded-full object-cover h-10 w-10">
                        <div style="min-width:0">
                            <div style="font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $user?->name ?? 'Pengguna' }}
                            </div>
                            <div
                                style="font-size:12px; color:#6b7280; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $user?->email ?? '' }}
                            </div>
                        </div>
                    </div>

                    <div style="display:flex; flex-direction:column; padding:6px">
                        <a href="{{ route('teacher.profile.edit') }}" class="btn"
                            style="text-align:left; padding:8px 10px; border-radius:8px; text-decoration:none; color:#111827;">
                            🧑‍🏫 Profil
                        </a>

                        {{-- Logout form (POST) --}}
                        <form id="logoutForm" action="{{ route('logout') }}" method="POST" style="margin-top:6px">
                            @csrf
                            <button type="submit" class="btn"
                                style="width:100%; text-align:left; padding:8px 10px; border-radius:8px; background:#ef4444; color:#fff;">
                                ⎋ Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>


    <main class="container">
        @yield('content')
    </main>

    {{-- ===== Create Listing Modal ===== --}}
    <div id="create-listing-modal" class="modal-backdrop" hidden aria-hidden="true">
        <div class="modal-scrim" data-close="create-listing-modal"></div>

        <div class="modal-card card pad" role="dialog" aria-modal="true" aria-labelledby="create-title">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                <h2 id="create-title" class="h2" style="margin:0">Buat Listing</h2>
                <button class="btn" type="button" data-close="create-listing-modal">Tutup</button>
            </div>

            <div id="create-listing-body">
                <div class="muted">Memuat formulir…</div>
            </div>
        </div>
    </div>

    {{-- ===== Upgrade Modal (already used by <x-plan-banner>) ===== --}}


    {{-- ===== JS for both modals & form fetch ===== --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const qs = s => document.querySelector(s);

            // helpers
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

            // Open Upgrade modal when buttons have data-open="upgrade-modal"
            document.querySelectorAll('[data-open="upgrade-modal"]').forEach(b => {
                b.addEventListener('click', () => openModal('upgrade-modal'));
            });
            if (location.hash === '#upgrade') openModal('upgrade-modal');

            // Open Create Listing modal and lazy-load /listings/create
            const openBtn = qs('#openCreateListing');
            const modalBody = qs('#create-listing-body');

            if (openBtn && modalBody) {
                openBtn.addEventListener('click', async () => {
                    openModal('create-listing-modal');

                    // Fetch once per page load
                    // Fetch once per page load
                    if (!modalBody.dataset.loaded) {
                        try {
                            const res = await fetch("{{ route('listings.create') }}", {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });
                            const html = await res.text();

                            const temp = document.createElement('div');
                            temp.innerHTML = html;

                            // === Cari form listing secara spesifik (bukan form logout) ===
                            let form = Array.from(temp.querySelectorAll('form')).find(f =>
                                f.querySelector('input[name="title"]') &&
                                (f.querySelector('input[name="category"]') || f.querySelector(
                                    'select[name="category"]')) &&
                                f.querySelector('select[name="region_id"]') &&
                                f.querySelector('textarea[name="description"]')
                            );

                            // Fallback 1: form dengan action mengarah ke /listings (store/update)
                            if (!form) {
                                form = Array.from(temp.querySelectorAll('form')).find(f => {
                                    const action = (f.getAttribute('action') || '')
                                        .toLowerCase();
                                    return action.includes('/listings') && !action.includes(
                                        '/logout');
                                });
                            }

                            // Fallback 2: ambil form terpanjang yang bukan logout
                            if (!form) {
                                const forms = Array.from(temp.querySelectorAll('form')).filter(f => {
                                    const action = (f.getAttribute('action') || '')
                                        .toLowerCase();
                                    return !action.includes('/logout');
                                });
                                form = forms.sort((a, b) => (b.innerHTML.length - a.innerHTML.length))[
                                    0];
                            }

                            if (!form) {
                                modalBody.innerHTML =
                                    '<div class="muted" style="color:#b91c1c">Form listing tidak ditemukan.</div>';
                                return;
                            }

                            modalBody.innerHTML = '';
                            modalBody.appendChild(form.cloneNode(true));
                            modalBody.dataset.loaded = '1';
                        } catch (e) {
                            modalBody.innerHTML =
                                '<div class="muted" style="color:#b91c1c">Gagal memuat formulir.</div>';
                        }
                    }

                });
            }
        });

        document.addEventListener('DOMContentLoaded', () => {

            const avatarBtn = document.getElementById('avatarBtn');
            const profileMenu = document.getElementById('profileMenu');

            function openMenu() {
                profileMenu.hidden = false;
                avatarBtn.setAttribute('aria-expanded', 'true');
            }

            function closeMenu() {
                profileMenu.hidden = true;
                avatarBtn.setAttribute('aria-expanded', 'false');
            }

            function toggleMenu() {
                profileMenu.hidden ? openMenu() : closeMenu();
            }

            if (avatarBtn && profileMenu) {
                // Toggle on click
                avatarBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggleMenu();
                });

                // Click-away close
                document.addEventListener('click', (e) => {
                    if (!profileMenu.hidden) {
                        const clickInsideMenu = profileMenu.contains(e.target);
                        const clickOnButton = avatarBtn.contains(e.target);
                        if (!clickInsideMenu && !clickOnButton) {
                            closeMenu();
                        }
                    }
                });

                // Esc to close
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !profileMenu.hidden) {
                        closeMenu();
                        avatarBtn.focus();
                    }
                });
            }
        });
    </script>
</body>

</html>
