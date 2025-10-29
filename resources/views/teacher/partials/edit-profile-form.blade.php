<form action="{{ route('teacher.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
  @csrf
  @method('PUT')

  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
    <input type="text" name="name" value="{{ old('name', $user->name) }}"
      class="w-full border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 transition" required>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
    <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $profile->whatsapp_number ?? '') }}"
      class="w-full border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 transition">
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
    <input type="text" name="location" value="{{ old('location', $profile->location ?? '') }}"
      class="w-full border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 transition">
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
    <textarea name="bio" rows="3"
      class="w-full border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 transition resize-none">{{ old('bio', $profile->bio ?? '') }}</textarea>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
    <input type="file" name="profile_image"
      class="block w-full text-sm text-gray-600 border border-gray-300 rounded-lg cursor-pointer bg-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 hover:file:bg-indigo-100 transition">
  </div>

  <div class="flex justify-end gap-3 pt-3">
    <button type="button" data-close="edit-profile-modal"
      class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">Batal</button>
    <button type="submit"
      class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">💾 Simpan</button>
  </div>
</form>
