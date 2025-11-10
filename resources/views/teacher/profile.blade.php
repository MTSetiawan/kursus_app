@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Profil Guru</h1>

<div class="bg-white shadow-sm rounded-xl p-6 max-w-xl">
  <div class="flex items-center gap-4 mb-4">
    <img id="profileImage" src="{{ $profile && $profile->profile_image_path ? Storage::url($profile->profile_image_path) : asset('images/default-avatar.png') }}"
      alt="Foto Profil"
      class="w-20 h-20 rounded-full object-cover ring-2 ring-indigo-500 transition duration-300">
    <div>
      <h2 id="profileName" class="text-xl font-semibold">{{ $user->name ?? 'Belum ada nama' }}</h2>
      <p id="profileWhatsapp" class="text-sm text-gray-600">📞 {{ $profile->whatsapp_number ?? '-' }}</p>
      <p id="profileLocation" class="text-sm text-gray-600">📍 {{ $profile->location ?? '-' }}</p>
    </div>
  </div>

  <div class="mb-4">
    <p id="profileBio" class="text-gray-700 whitespace-pre-line">{{ $profile->bio ?? 'Belum ada bio' }}</p>
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
  <div class="modal-card bg-white p-6 rounded-xl shadow-xl w-full max-w-2xl mx-4"
      role="dialog" aria-modal="true" aria-labelledby="edit-profile-title">
      <div class="flex justify-between items-center mb-4">
          <h2 id="edit-profile-title" class="text-lg font-semibold">Edit Profil</h2>
          <button class="text-gray-500 hover:text-gray-700 transition" type="button" data-close="edit-profile-modal">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
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
          m.removeAttribute('aria-hidden');
          document.body.style.overflow = 'hidden';
      }
  };
  
  const closeModal = id => {
      const m = document.querySelector('#' + id);
      if (m) {
          m.setAttribute('hidden', '');
          m.setAttribute('aria-hidden', 'true');
          document.body.style.overflow = '';
      }
  };

  // Close modal buttons
  document.querySelectorAll('[data-close="edit-profile-modal"]').forEach(btn => {
      btn.addEventListener('click', () => closeModal('edit-profile-modal'));
  });

  // Close on backdrop click
  document.querySelector('.modal-scrim[data-close="edit-profile-modal"]')?.addEventListener('click', () => {
      closeModal('edit-profile-modal');
  });

  // Open modal and load form
  if (openBtn && modalBody) {
      openBtn.addEventListener('click', async () => {
          openModal('edit-profile-modal');
          modalBody.innerHTML = '<div class="text-gray-500 text-sm">Memuat formulir…</div>';
          
          try {
              const res = await fetch("{{ route('teacher.profile.edit') }}", {
                  headers: { 'X-Requested-With': 'XMLHttpRequest' }
              });
              
              if (!res.ok) throw new Error('Failed to load');
              
              const html = await res.text();
              modalBody.innerHTML = html;
          } catch (e) {
              console.error('Error loading form:', e);
              modalBody.innerHTML = '<div class="text-red-600 text-sm">Gagal memuat formulir. Silakan coba lagi.</div>';
          }
      });
  }

  // Handle form submission
  modalBody.addEventListener('submit', async e => {
      if (e.target.tagName !== 'FORM') return;
      e.preventDefault();

      const form = e.target;
      const formData = new FormData(form);
      const submitBtn = form.querySelector('button[type="submit"]');
      
      // Disable submit button
      if (submitBtn) {
          submitBtn.disabled = true;
          const originalText = submitBtn.textContent;
          submitBtn.textContent = 'Menyimpan...';
          
          setTimeout(() => {
              if (submitBtn.disabled) {
                  submitBtn.disabled = false;
                  submitBtn.textContent = originalText;
              }
          }, 10000); // Safety timeout
      }

      try {
          const res = await fetch(form.action, {
              method: 'POST',
              headers: { 
                  'X-Requested-With': 'XMLHttpRequest',
                  'Accept': 'application/json'
              },
              body: formData
          });

          const json = await res.json();

          if (res.ok && json.success) {
              // Update profile display
              document.getElementById('profileName').textContent = json.name || 'Belum ada nama';
              document.getElementById('profileBio').textContent = json.bio || 'Belum ada bio';
              document.getElementById('profileWhatsapp').textContent = '📞 ' + (json.whatsapp_number || '-');
              document.getElementById('profileLocation').textContent = '📍 ' + (json.location || '-');
              
              if (json.img) {
                  document.getElementById('profileImage').src = json.img;
                  // Update navbar avatar too
                  document.querySelectorAll('img[alt="Foto Profil"]').forEach(img => {
                      img.src = json.img;
                  });
              }
              
              closeModal('edit-profile-modal');
              
              // Optional: Show success message
              const successMsg = document.createElement('div');
              successMsg.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
              successMsg.textContent = '✓ Profil berhasil diperbarui';
              document.body.appendChild(successMsg);
              setTimeout(() => successMsg.remove(), 3000);
              
          } else {
              alert(json.message || 'Gagal memperbarui profil.');
          }
      } catch (e) {
          console.error('Error submitting form:', e);
          alert('Terjadi kesalahan. Silakan coba lagi.');
      } finally {
          // Re-enable submit button
          if (submitBtn) {
              submitBtn.disabled = false;
              submitBtn.textContent = '💾 Simpan';
          }
      }
  });

  // ESC key to close modal
  document.addEventListener('keydown', e => {
      if (e.key === 'Escape') {
          const modal = document.querySelector('#edit-profile-modal:not([hidden])');
          if (modal) closeModal('edit-profile-modal');
      }
  });
});
</script>
@endsection