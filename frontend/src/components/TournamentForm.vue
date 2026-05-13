<template>
  <div>
    <div class="field-group">
      <label>Nama Turnamen</label>
      <input
        :value="name"
        @input="$emit('update:name', $event.target.value)"
        placeholder="Contoh: ML Championship 2025"
      />
    </div>

    <div class="form-grid">
      <div class="field-group">
        <label>Tanggal</label>
        <input
          :value="dateOnly"
          @input="onDateChange($event.target.value)"
          type="date"
        />
      </div>
      <div class="field-group">
        <label>Jam Mulai</label>
        <input
          :value="timeOnly"
          @input="onTimeChange($event.target.value)"
          type="time"
        />
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
      <button class="btn-primary" @click="$emit('submit')">
        {{ isEdit ? 'Simpan Perubahan' : 'Tambah Turnamen' }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TournamentForm',
  props: {
    name: { type: String, required: true },
    schedule: { type: String, required: true },
    description: { type: String, required: true },
    isEdit: { type: Boolean, required: true }
  },
  data() {
    return { selectedFileName: '' }
  },
  computed: {
    posterLabel() {
      return this.selectedFileName || 'Pilih gambar poster'
    },
    dateOnly() {
      if (!this.schedule) return ''
      // Handle both "YYYY-MM-DD HH:mm:ss" and "YYYY-MM-DDTHH:mm"
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
    }
  }
}
</script>