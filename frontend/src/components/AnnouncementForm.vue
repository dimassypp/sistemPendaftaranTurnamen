<template>
  <div>
    <div class="field-group">
      <label>Judul Pengumuman</label>
      <input
        :value="title"
        @input="$emit('update:title', $event.target.value)"
        placeholder="Judul pengumuman"
      />
    </div>
    <div class="field-group">
      <label>Isi Pengumuman</label>
      <textarea
        :value="content"
        @input="$emit('update:content', $event.target.value)"
        placeholder="Tulis isi pengumuman di sini..."
        rows="5"
      ></textarea>
    </div>
    <div class="form-actions">
      <button class="btn-ghost" @click="$emit('cancel')">Batal</button>
      <button class="btn-primary" @click="$emit('submit')">
        {{ isEdit ? 'Simpan Perubahan' : 'Publikasikan' }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AnnouncementForm',
  props: {
    title: { type: String, required: true },
    content: { type: String, required: true },
    isEdit: { type: Boolean, required: true }
  }
}
</script>