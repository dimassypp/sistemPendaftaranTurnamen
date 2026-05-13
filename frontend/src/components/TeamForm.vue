<template>
  <div>
    <div class="field-group">
      <label>Pilih Turnamen</label>
      <select
        :value="tournamentId"
        @change="$emit('update:tournamentId', $event.target.value)"
      >
        <option value="">— Pilih turnamen —</option>
        <option v-for="t in tournaments" :key="t.id" :value="t.id">
          {{ t.name }}
        </option>
      </select>
    </div>
    <div class="field-group">
      <label>Nama Tim</label>
      <input
        :value="teamName"
        @input="$emit('update:teamName', $event.target.value)"
        placeholder="Masukkan nama tim kamu"
      />
    </div>
    <div class="field-group">
      <label>Logo Tim</label>
      <div class="file-input">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="16 16 12 12 8 16"/>
          <line x1="12" y1="12" x2="12" y2="21"/>
          <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
        </svg>
        <span>{{ logoLabel }}</span>
        <input type="file" accept="image/*" @change="onFileChange" />
      </div>
    </div>
    <div class="form-actions">
      <button class="btn-ghost" @click="$emit('cancel')">Batal</button>
      <button class="btn-primary" @click="$emit('submit')">Daftar Sekarang</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TeamForm',
  props: {
    tournamentId: { type: [String, Number], required: true },
    teamName: { type: String, required: true },
    tournaments: { type: Array, required: true }
  },
  data() {
    return { selectedFileName: '' }
  },
  computed: {
    logoLabel() {
      return this.selectedFileName || 'Upload logo tim (opsional)'
    }
  },
  methods: {
    onFileChange(e) {
      const file = e.target.files[0]
      if (file) {
        this.selectedFileName = file.name
        this.$emit('update:logo', file)
      }
    }
  }
}
</script>