<template>
  <teleport to="body">
    <div class="overlay" @click.self="$emit('close')">
      <div class="detail-modal">

        <!-- Poster besar -->
        <div class="detail-poster">
          <img
            v-if="tournament.poster_image"
            :src="'http://localhost:8000/storage/' + tournament.poster_image"
            :alt="tournament.name"
          />
          <div v-else class="detail-poster-empty">Tidak ada poster</div>
          <button class="close-btn" @click="$emit('close')">✕</button>
        </div>

        <div class="detail-body">
          <!-- Info turnamen -->
          <div class="detail-info">
            <h2 class="detail-name">{{ tournament.name }}</h2>
            <p class="detail-date">{{ formatSchedule(tournament.schedule) }}</p>
            <p class="detail-desc">{{ tournament.description || 'Tidak ada deskripsi.' }}</p>
          </div>

          <!-- Form daftar (member only) -->
          <div v-if="showRegister" class="register-section">
            <div class="register-divider">
              <span>Daftarkan Tim Kamu</span>
            </div>

            <div class="field-group" :class="{ 'has-error': errors.teamName }">
              <label>Nama Tim</label>
              <input
                v-model="localTeamName"
                placeholder="Masukkan nama tim kamu"
                type="text"
                :class="{ 'input-error': errors.teamName }"
                @input="errors.teamName = ''"
              />
              <p v-if="errors.teamName" class="field-error">{{ errors.teamName }}</p>
            </div>

            <div class="field-group">
              <label>Logo Tim <span class="label-optional">— opsional</span></label>
              <div class="file-upload-area" :class="{ 'has-file': logoFile }" @click="triggerFileInput">
                <div v-if="!logoFile" class="file-upload-prompt">
                  <div class="file-upload-icon">↑</div>
                  <p class="file-upload-text">Klik untuk upload logo tim</p>
                  <p class="file-upload-hint">PNG, JPG, maks 2MB</p>
                </div>
                <div v-else class="file-preview">
                  <img :src="logoPreview" alt="Preview logo" />
                  <div class="file-preview-info">
                    <p class="file-name">{{ logoFile.name }}</p>
                    <button class="remove-file" @click.stop="removeFile">Hapus</button>
                  </div>
                </div>
                <input
                  ref="fileInput"
                  type="file"
                  accept="image/*"
                  style="display:none"
                  @change="onFileChange"
                />
              </div>
            </div>

            <div class="register-actions">
              <button class="btn-ghost" @click="$emit('close')">Batal</button>
              <button class="btn-primary btn-register" @click="handleSubmit" :disabled="loading">
                {{ loading ? 'Mendaftarkan...' : 'Daftar Sekarang' }}
              </button>
            </div>
          </div>

          <!-- Sudah terdaftar -->
          <div v-else-if="alreadyRegistered" class="already-registered">
            Tim kamu sudah terdaftar di turnamen ini.
          </div>

          <!-- Tombol tutup untuk admin/guest -->
          <div v-else class="detail-close-wrap">
            <button class="btn-ghost btn-full" @click="$emit('close')">Tutup</button>
          </div>
        </div>

      </div>
    </div>
  </teleport>
</template>

<script>
export default {
  name: 'TournamentDetailModal',
  props: {
    tournament: { type: Object, required: true },
    showRegister: { type: Boolean, default: false },
    alreadyRegistered: { type: Boolean, default: false },
    loading: { type: Boolean, default: false }
  },
  data() {
    return {
      localTeamName: '',
      logoFile: null,
      logoPreview: null,
      errors: { teamName: '' }
    }
  },
  mounted() {
    document.body.style.overflow = 'hidden'
  },
  unmounted() {
    document.body.style.overflow = ''
  },
  methods: {
    formatSchedule(schedule) {
      if (!schedule) return '-'
      try {
        return new Date(schedule).toLocaleString('id-ID', {
          weekday: 'long', day: 'numeric',
          month: 'long', year: 'numeric',
          hour: '2-digit', minute: '2-digit'
        })
      } catch { return schedule }
    },
    triggerFileInput() {
      this.$refs.fileInput.click()
    },
    onFileChange(e) {
      const file = e.target.files[0]
      if (!file) return
      this.logoFile = file
      this.logoPreview = URL.createObjectURL(file)
    },
    removeFile() {
      this.logoFile = null
      this.logoPreview = null
      this.$refs.fileInput.value = ''
    },
    handleSubmit() {
      this.errors.teamName = ''
      if (!this.localTeamName.trim()) {
        this.errors.teamName = 'Nama tim tidak boleh kosong'
        return
      }
      if (this.localTeamName.trim().length < 2) {
        this.errors.teamName = 'Nama tim minimal 2 karakter'
        return
      }
      this.$emit('register', {
        tournamentId: this.tournament.id,
        teamName: this.localTeamName.trim(),
        logo: this.logoFile
      })
    }
  }
}
</script>

<style scoped>
.overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.75);
  backdrop-filter: blur(6px);
  z-index: 999;
  display: flex; align-items: center; justify-content: center;
  padding: 20px;
}
.detail-modal {
  background: var(--c-card);
  border: 1px solid var(--c-border);
  border-radius: 20px;
  width: 100%; max-width: 560px;
  max-height: 90vh; overflow-y: auto;
  animation: pop-in 0.22s ease;
}
@keyframes pop-in {
  from { opacity: 0; transform: scale(0.94) translateY(10px); }
  to   { opacity: 1; transform: scale(1) translateY(0); }
}

.detail-poster {
  position: relative;
  width: 100%; height: 220px;
  overflow: hidden; flex-shrink: 0;
}
.detail-poster img { width: 100%; height: 100%; object-fit: cover; display: block; }
.detail-poster-empty {
  height: 100%; background: var(--c-surface);
  display: flex; align-items: center; justify-content: center;
  font-size: 13px; color: var(--c-muted);
}
.close-btn {
  position: absolute; top: 12px; right: 12px;
  background: rgba(0,0,0,0.55);
  border: none; border-radius: 50%;
  width: 32px; height: 32px;
  color: #fff; font-size: 14px; font-weight: 700;
  cursor: pointer; display: flex;
  align-items: center; justify-content: center;
  backdrop-filter: blur(4px);
  transition: background 0.2s;
}
.close-btn:hover { background: rgba(0,0,0,0.8); }

.detail-body { padding: 20px 24px 24px; }

.detail-info { margin-bottom: 20px; }
.detail-name { font-size: 22px; font-weight: 700; color: var(--c-text); margin: 0 0 8px; }
.detail-date { font-size: 13px; color: var(--c-accent); font-weight: 600; margin: 0 0 12px; }
.detail-desc { font-size: 14px; color: var(--c-sub); line-height: 1.7; margin: 0; }

.register-divider {
  display: flex; align-items: center; gap: 12px;
  margin-bottom: 20px;
}
.register-divider::before,
.register-divider::after {
  content: ''; flex: 1;
  height: 1px; background: var(--c-border);
}
.register-divider span {
  font-size: 12px; font-weight: 700; color: var(--c-muted);
  text-transform: uppercase; letter-spacing: 0.08em;
  white-space: nowrap;
}

.label-optional { font-weight: 400; color: var(--c-muted); text-transform: none; letter-spacing: 0; }

.file-upload-area {
  border: 1.5px dashed var(--c-border);
  border-radius: 12px;
  cursor: pointer;
  transition: border-color 0.2s, background 0.2s;
  overflow: hidden;
}
.file-upload-area:hover { border-color: var(--c-accent); background: var(--c-accent-dim); }
.file-upload-area.has-file { border-style: solid; border-color: rgba(124,106,246,0.4); }
.file-upload-prompt {
  padding: 28px 20px; text-align: center;
}
.file-upload-icon { font-size: 24px; color: var(--c-muted); margin-bottom: 8px; }
.file-upload-text { font-size: 14px; font-weight: 600; color: var(--c-sub); margin: 0 0 4px; }
.file-upload-hint { font-size: 12px; color: var(--c-muted); margin: 0; }
.file-preview { display: flex; align-items: center; gap: 14px; padding: 14px 16px; }
.file-preview img { width: 52px; height: 52px; border-radius: 8px; object-fit: cover; flex-shrink: 0; }
.file-preview-info { flex: 1; min-width: 0; }
.file-name { font-size: 13px; font-weight: 600; color: var(--c-text); margin: 0 0 4px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.remove-file { background: none; border: none; color: var(--c-error); font-size: 12px; font-weight: 600; cursor: pointer; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }

.field-error { font-size: 12px; color: var(--c-error); margin-top: 6px; font-weight: 500; }
.input-error { border-color: var(--c-error) !important; box-shadow: 0 0 0 3px rgba(248,113,113,0.12) !important; }

.register-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 8px; }
.btn-register { padding: 10px 28px; }
.btn-register:disabled { opacity: 0.6; cursor: not-allowed; }

.already-registered {
  background: var(--c-success-bg);
  border: 1px solid rgba(16,185,129,0.25);
  color: var(--c-success);
  border-radius: 10px; padding: 14px 16px;
  font-size: 14px; font-weight: 500;
  text-align: center;
}
.detail-close-wrap { margin-top: 4px; }
</style>