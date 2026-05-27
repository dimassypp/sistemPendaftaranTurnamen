<template>
  <div>
    <div class="field-group">
      <label>Judul Pengumuman</label>
      <input
        :value="title"
        @input="$emit('update:title', $event.target.value); errors.title = ''"
        placeholder="Judul pengumuman"
        :class="{ 'input-error': errors.title }"
      />
      <p v-if="errors.title" class="field-error">{{ errors.title }}</p>
    </div>
    <div class="field-group">
      <label>Isi Pengumuman</label>
      <textarea
        :value="content"
        @input="$emit('update:content', $event.target.value); errors.content = ''"
        placeholder="Tulis isi pengumuman di sini..."
        rows="5"
        :class="{ 'input-error': errors.content }"
      ></textarea>
      <p v-if="errors.content" class="field-error">{{ errors.content }}</p>
    </div>
    <div class="form-actions">
      <button class="btn-ghost" @click="$emit('cancel')">Batal</button>
      <button class="btn-primary" @click="handleSubmit">
        {{ isEdit ? 'Simpan Perubahan' : 'Publikasikan' }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AnnouncementForm',
  props: {
    title:   { type: String, required: true },
    content: { type: String, required: true },
    isEdit:  { type: Boolean, required: true }
  },
  data() {
    return {
      errors: { title: '', content: '' }
    }
  },
  methods: {
    handleSubmit() {
      this.errors = { title: '', content: '' }
      let valid = true

      if (!this.title.trim()) {
        this.errors.title = 'Judul pengumuman tidak boleh kosong'
        valid = false
      } else if (this.title.trim().length < 3) {
        this.errors.title = 'Judul minimal 3 karakter'
        valid = false
      }

      if (!this.content.trim()) {
        this.errors.content = 'Isi pengumuman tidak boleh kosong'
        valid = false
      } else if (this.content.trim().length < 10) {
        this.errors.content = 'Isi pengumuman minimal 10 karakter'
        valid = false
      }

      if (!valid) return
      this.$emit('submit')
    }
  }
}
</script>

<style scoped>
.field-error {
  font-size: 12px;
  color: var(--c-error);
  margin-top: 6px;
  font-weight: 500;
}
.input-error {
  border-color: var(--c-error) !important;
  box-shadow: 0 0 0 3px rgba(248, 113, 113, 0.12) !important;
}
</style>