@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Profil Guru</h1>

<div class="bg-white shadow-sm rounded-xl p-6 max-w-xl">
  <div class="flex items-center gap-4 mb-4">
    <img src="{{ $profile && $profile->profile_image_path ? Storage::url($profile->profile_image_path) : asset('images/default-avatar.png') }}"
      alt="Foto Profil"
      class="w-20 h-20 rounded-full object-cover ring-2 ring-indigo-500 transition duration-300">
    <div>
      <h2 class="text-xl font-semibold">{{ $user->name ?? 'Belum ada nama' }}</h2>
      <p class="text-sm text-gray-600">📞 {{ $profile->whatsapp_number ?? '-' }}</p>
      <p class="text-sm text-gray-600">📍 {{ $profile->location ?? '-' }}</p>
    </div>
  </div>

  <div class="mb-4">
    <p class="text-gray-700 whitespace-pre-line">{{ $profile->bio ?? 'Belum ada bio' }}</p>
  </div>

  <div class="flex justify-end">
    <button id="openEditProfile"
      class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
      ✏️ Edit Profil
    </button>
  </div>
</div>

{{-- === MODAL === --}}
<div id="edit-profile-modal" class="modal-backdrop" hidden aria-hidden="true">
  <div class="modal-scrim" data-close="edit-profile-modal"></div>
  <div class="modal-card card pad bg-white p-6 rounded-xl shadow-xl w-full max-w-2xl"
      role="dialog" aria-modal="true" aria-labelledby="edit-profile-title">
      <div class="flex justify-between items-center mb-4">
          <h2 id="edit-profile-title" class="text-lg font-semibold">Edit Profil</h2>
          <button class="btn" type="button" data-close="edit-profile-modal">Tutup</button>
      </div>
      <div id="edit-profile-body">
          <div class="text-gray-500 text-sm">Memuat formulir…</div>
      </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const openBtn = document.querySelector('#openEditProfile');
  const modalBody = document.querySelector('#edit-profile-body');

  const openModal = id => {
      const m = document.querySelector('#' + id);
      if (m) {
          m.removeAttribute('hidden');
          document.body.style.overflow = 'hidden';
      }
  };
  const closeModal = id => {
      const m = document.querySelector('#' + id);
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

  if (openBtn && modalBody) {
      openBtn.addEventListener('click', async () => {
          openModal('edit-profile-modal');
          modalBody.innerHTML = '<div class="text-gray-500 text-sm">Memuat formulir…</div>';
          try {
              const res = await fetch("{{ route('teacher.profile.edit') }}", {
                  headers: { 'X-Requested-With': 'XMLHttpRequest' }
              });
              const html = await res.text();
              modalBody.innerHTML = html;
          } catch (e) {
              modalBody.innerHTML = '<div class="text-red-600 text-sm">Gagal memuat formulir.</div>';
          }
      });
  }

  modalBody.addEventListener('submit', async e => {
      if (e.target.tagName !== 'FORM') return;
      e.preventDefault();

      const form = e.target;
      const formData = new FormData(form);

      const res = await fetch(form.action, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: formData
      });

      if (res.ok) {
          const json = await res.json();
          if (json.success) {
              document.querySelector('h2.text-xl').textContent = json.name;
              document.querySelector('.text-gray-700.whitespace-pre-line').textContent = json.bio || 'Belum ada bio';
              const infos = document.querySelectorAll('.text-gray-600');
              infos[0].textContent = '📞 ' + (json.whatsapp_number || '-');
              infos[1].textContent = '📍 ' + (json.location || '-');
              document.querySelector('img.rounded-full').src = json.img;
              closeModal('edit-profile-modal');
          } else alert('Gagal memperbarui profil.');
      } else alert('Gagal memperbarui profil.');
  });
});
</script>
@endsection
