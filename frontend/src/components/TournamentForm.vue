<template>
  <div>
    <div class="field-group" :class="{ 'has-error': errors.name }">
      <label>Nama Turnamen</label>
      <input
        :value="name"
        @input="$emit('update:name', $event.target.value); errors.name = ''"
        placeholder="Contoh: ML Championship 2025"
        :class="{ 'input-error': errors.name }"
      />
      <p v-if="errors.name" class="field-error">{{ errors.name }}</p>
    </div>

    <div class="form-grid">
      <div class="field-group" :class="{ 'has-error': errors.date }">
        <label>Tanggal</label>
        <input
          :value="dateOnly"
          @input="onDateChange($event.target.value); errors.date = ''"
          type="date"
          :class="{ 'input-error': errors.date }"
        />
        <p v-if="errors.date" class="field-error">{{ errors.date }}</p>
      </div>
      <div class="field-group" :class="{ 'has-error': errors.time }">
        <label>Jam Mulai</label>
        <input
          :value="timeOnly"
          @input="onTimeChange($event.target.value); errors.time = ''"
          type="time"
          :class="{ 'input-error': errors.time }"
        />
        <p v-if="errors.time" class="field-error">{{ errors.time }}</p>
      </div>
    </div>

    <div class="field-group">
      <label>Deskripsi</label>
      <textarea
        :value="description"
        @input="$emit('update:description', $event.target.value)"
        placeholder="Deskripsi singkat turnamen..."
        rows="3"
      ></textarea>
    </div>

    <div class="field-group">
      <label>Poster Turnamen</label>
      <div class="file-input">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="16 16 12 12 8 16"/>
          <line x1="12" y1="12" x2="12" y2="21"/>
          <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
        </svg>
        <span>{{ posterLabel }}</span>
        <input type="file" accept="image/*" @change="onFileChange" @click.stop />
      </div>
      <p v-if="isEdit" class="hint">Kosongkan jika tidak ingin mengganti poster lama.</p>
    </div>

    <div class="form-actions">
      <button class="btn-ghost" @click="$emit('cancel')">Batal</button>
      <button class="btn-primary" @click="handleSubmit">
        {{ isEdit ? 'Simpan Perubahan' : 'Tambah Turnamen' }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TournamentForm',
  props: {
    name:        { type: String, required: true },
    schedule:    { type: String, required: true },
    description: { type: String, required: true },
    isEdit:      { type: Boolean, required: true }
  },
  data() {
    return {
      selectedFileName: '',
      errors: { name: '', date: '', time: '' }
    }
  },
  computed: {
    posterLabel() {
      return this.selectedFileName || 'Pilih gambar poster'
    },
    dateOnly() {
      if (!this.schedule) return ''
      return this.schedule.slice(0, 10)
    },
    timeOnly() {
      if (!this.schedule) return ''
      const t = this.schedule.includes('T')
        ? this.schedule.slice(11, 16)
        : this.schedule.slice(11, 16)
      return t || ''
    }
  },
  methods: {
    onDateChange(date) {
      const time = this.timeOnly || '00:00'
      this.$emit('update:schedule', `${date} ${time}:00`)
    },
    onTimeChange(time) {
      const date = this.dateOnly || ''
      if (date) this.$emit('update:schedule', `${date} ${time}:00`)
    },
    onFileChange(e) {
      const file = e.target.files[0]
      if (file) {
        this.selectedFileName = file.name
        this.$emit('update:poster', file)
      }
    },
    handleSubmit() {
      // Reset semua error
      this.errors = { name: '', date: '', time: '' }
      let valid = true

      if (!this.name.trim()) {
        this.errors.name = 'Nama turnamen tidak boleh kosong'
        valid = false
      } else if (this.name.trim().length < 3) {
        this.errors.name = 'Nama turnamen minimal 3 karakter'
        valid = false
      } else if (this.name.trim().length > 100) {
        this.errors.name = 'Nama turnamen maksimal 100 karakter'
        valid = false
      }

      if (!this.dateOnly) {
        this.errors.date = 'Tanggal harus diisi'
        valid = false
      }

      if (!this.timeOnly) {
        this.errors.time = 'Jam mulai harus diisi'
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