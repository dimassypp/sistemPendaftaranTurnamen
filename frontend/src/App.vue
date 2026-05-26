<template>
  <div class="app">

    <!-- NAVBAR -->
    <nav class="navbar">
      <div class="nav-inner">
        <div class="nav-brand">
          <img src="/bionesco.png" alt="BIONESCO" class="brand-logo" />
          <span class="brand-name">BIONESCO Tournament</span>
        </div>
        <div v-if="user" class="nav-user">
          <span class="user-badge" :class="user.role">{{ user.role }}</span>
          <span class="user-name">{{ user.name }}</span>
          <button class="btn-ghost btn-sm" @click="logout">Keluar</button>
        </div>
      </div>
    </nav>

    <div class="main">

      <!-- TOAST -->
      <div v-if="message" class="toast toast-success">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <polyline points="20 6 9 17 4 12"/>
        </svg>
        {{ message }}
      </div>
      <div v-if="error" class="toast toast-error">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <circle cx="12" cy="12" r="10"/>
          <line x1="15" y1="9" x2="9" y2="15"/>
          <line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
        {{ error }}
      </div>

      <!-- AUTH SECTION -->
      <div v-if="!token" class="auth-wrapper">
        <div class="auth-hero">
          <img src="/bionesco.png" alt="BIONESCO" class="hero-logo" />
          <h1>Pendaftaran<br><span class="accent-text">Turnamen BIONESCO</span></h1>
          <p>Platform resmi pendaftaran dan pengelolaan turnamen gaming kompetitif.</p>
        </div>
        <RegisterForm
          v-if="showRegister"
          :name="registerName"
          :email="registerEmail"
          :password="registerPassword"
          :passwordConfirmation="registerPasswordConfirmation"
          :errors="registerErrors"
          @update:name="registerName = $event; registerErrors.name = ''"
          @update:email="registerEmail = $event; registerErrors.email = ''"
          @update:password="registerPassword = $event; registerErrors.password = ''"
          @update:passwordConfirmation="registerPasswordConfirmation = $event"
          @register="register"
          @show-login="showRegister = false"
        />
        <LoginForm
          v-else
          :email="loginEmail"
          :password="loginPassword"
          :errors="loginErrors"
          @update:email="loginEmail = $event; loginErrors.email = ''"
          @update:password="loginPassword = $event; loginErrors.password = ''"
          @login="login"
          @show-register="showRegister = true"
        />
      </div>

      <!-- CONTENT SECTION -->
      <div v-else class="content">

        <!-- PENGUMUMAN -->
        <section class="section">
          <div class="section-header">
            <div>
              <h2 class="section-title">Pengumuman</h2>
              <p class="section-sub">{{ announcements.length }} pengumuman terbaru</p>
            </div>
            <button v-if="isAdmin" class="btn-primary btn-add" @click="openModal('announcement')">
              + Tambah Pengumuman
            </button>
          </div>
          <AnnouncementList
            :announcements="announcements"
            :loading="loadingAnnouncements"
            :isAdmin="isAdmin"
            @select-announcement="selectedAnnouncementDetail = $event"
            @edit-announcement="openEditAnnouncement"
            @delete-announcement="deleteAnnouncement"
          />
        </section>

        <div class="divider"></div>

        <!-- TURNAMEN -->
        <section class="section">
          <div class="section-header">
            <div>
              <h2 class="section-title">Daftar Turnamen</h2>
              <p class="section-sub">{{ tournaments.length }} turnamen tersedia</p>
            </div>
            <button v-if="isAdmin" class="btn-primary btn-add" @click="openModal('tournament')">
              + Tambah Turnamen
            </button>
          </div>
          <TournamentList
            :tournaments="tournaments"
            :loading="loadingTournaments"
            :isAdmin="isAdmin"
            @select-tournament="openTournamentDetail"
            @edit-tournament="openEditTournament"
            @delete-tournament="deleteTournament"
          />
        </section>

        <div class="divider"></div>

        <!-- TIM TERDAFTAR -->
        <section class="section">
          <div class="section-header">
            <div>
              <h2 class="section-title">{{ isAdmin ? 'Semua Pendaftaran Tim' : 'Tim Saya' }}</h2>
              <p class="section-sub">{{ teams.length }} pendaftaran tercatat</p>
            </div>
            <p v-if="!isAdmin" class="section-hint">Klik card turnamen untuk mendaftar</p>
          </div>
          <TeamList
            :teams="teams"
            :loading="loadingTeams"
            :isAdmin="isAdmin"
            @delete-team="deleteTeam"
          />
        </section>

      </div>

      <!-- ── MODALS ── -->

      <Modal
        v-if="modal === 'tournament'"
        :title="isEditTournament ? 'Edit Turnamen' : 'Tambah Turnamen Baru'"
        @close="closeModal"
      >
        <TournamentForm
          :name="tournamentName"
          :schedule="tournamentSchedule"
          :description="tournamentDescription"
          :isEdit="isEditTournament"
          @update:name="tournamentName = $event"
          @update:schedule="tournamentSchedule = $event"
          @update:description="tournamentDescription = $event"
          @update:poster="tournamentPoster = $event"
          @submit="submitTournament"
          @cancel="closeModal"
        />
      </Modal>

      <Modal
        v-if="modal === 'announcement'"
        :title="isEditAnnouncement ? 'Edit Pengumuman' : 'Buat Pengumuman Baru'"
        @close="closeModal"
      >
        <AnnouncementForm
          :title="announcementTitle"
          :content="announcementContent"
          :isEdit="isEditAnnouncement"
          @update:title="announcementTitle = $event"
          @update:content="announcementContent = $event"
          @submit="submitAnnouncement"
          @cancel="closeModal"
        />
      </Modal>

      <TournamentDetailModal
        v-if="selectedTournamentDetail"
        :tournament="selectedTournamentDetail"
        :showRegister="!!token && !isAdmin"
        :alreadyRegistered="!isAdmin && isAlreadyRegistered(selectedTournamentDetail.id)"
        :loading="teamSubmitLoading"
        @close="selectedTournamentDetail = null"
        @register="submitTeamFromDetail"
      />
      <AnnouncementDetailModal
        v-if="selectedAnnouncementDetail"
        :announcement="selectedAnnouncementDetail"
        :isAdmin="isAdmin"
        @close="selectedAnnouncementDetail = null"
        @edit="openEditAnnouncementFromDetail"
        @delete="deleteAnnouncementFromDetail"
      />
    </div>
  </div>
</template>

<script>
import RegisterForm from './components/RegisterForm.vue'
import LoginForm from './components/LoginForm.vue'
import TournamentForm from './components/TournamentForm.vue'
import TournamentList from './components/TournamentList.vue'
import TeamForm from './components/TeamForm.vue'
import TeamList from './components/TeamList.vue'
import AnnouncementForm from './components/AnnouncementForm.vue'
import AnnouncementList from './components/AnnouncementList.vue'
import AnnouncementDetailModal from './components/AnnouncementDetailModal.vue'
import Modal from './components/Modal.vue'
import TournamentDetailModal from './components/TournamentDetailModal.vue'

const BASE = 'http://localhost:8000/api'

export default {
  name: 'App',
  components: {
    RegisterForm, LoginForm,
    TournamentForm, TournamentList,
    TeamForm, TeamList,
    AnnouncementForm, AnnouncementList,
    Modal, TournamentDetailModal,AnnouncementDetailModal
  },

  data() {
    return {
      showRegister: false,
      registerName: '', registerEmail: '',
      registerPassword: '', registerPasswordConfirmation: '',
      loginEmail: '', loginPassword: '',
      loginErrors: {}, registerErrors: {},

      token: localStorage.getItem('token') || '',
      user: JSON.parse(localStorage.getItem('user')) || null,

      tournaments: [], loadingTournaments: false,
      tournamentName: '', tournamentSchedule: '',
      tournamentDescription: '', tournamentPoster: null,
      selectedTournament: null,
      isEditTournament: false, selectedTournamentId: null,

      selectedTournamentDetail: null,
      teamSubmitLoading: false,

      teams: [], loadingTeams: false,

      announcements: [], loadingAnnouncements: false,
      announcementTitle: '', announcementContent: '',
      isEditAnnouncement: false, selectedAnnouncementId: null,
      selectedAnnouncementDetail: null,

      modal: null,
      message: '', error: ''
    }
  },
  computed: {
    isAdmin() { return this.user && this.user.role === 'admin' }
  },
  methods: {
    setMessage(msg) {
      this.message = msg; this.error = ''
      setTimeout(() => { this.message = '' }, 3500)
    },
    setError(err) {
      this.error = err; this.message = ''
      setTimeout(() => { this.error = '' }, 4000)
    },
    authHeaders() { return { 'Authorization': `Bearer ${this.token}` } },
    jsonHeaders() {
      return {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${this.token}`,
        'Accept': 'application/json'
      }
    },
    openModal(type) {
      this.modal = type
    },
    closeModal() {
      this.modal = null
      this.cancelEditTournament()
      this.cancelEditAnnouncement()
    },
    openEditTournament(t) {
      this.startEditTournament(t)
      this.modal = 'tournament'
    },
    
    openEditAnnouncement(a) {
      this.startEditAnnouncement(a)
      this.modal = 'announcement'
    },
    async register() {
      this.registerErrors = {}
      try {
        const res = await fetch(`${BASE}/register`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({
            name: this.registerName,
            email: this.registerEmail,
            password: this.registerPassword,
            password_confirmation: this.registerPasswordConfirmation
          })
        })
        const result = await res.json()
        if (!res.ok) {
          if (result.errors) {
            this.registerErrors = {
              name: result.errors.name?.[0],
              email: result.errors.email?.[0],
              password: result.errors.password?.[0]
            }
          } else {
            this.registerErrors.general = result.message || 'Register gagal'
          }
          return
        }
        this.setMessage('Akun berhasil dibuat! Silakan login.')
        this.showRegister = false
      } catch {
        this.registerErrors.general = 'Tidak dapat terhubung ke server'
      }
    },
    async login() {
      this.loginErrors = {}
      try {
        const res = await fetch(`${BASE}/login`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({ email: this.loginEmail, password: this.loginPassword })
        })
        const result = await res.json()
        if (!res.ok) {
          if (result.errors) {
            this.loginErrors = result.errors
          } else {
            this.loginErrors.general = result.message || 'Email atau password salah'
          }
          return
        }
        this.token = result.token
        this.user = result.user
        localStorage.setItem('token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))
        this.setMessage('Login berhasil!')
        this.fetchAll()
      } catch {
        this.loginErrors.general = 'Tidak dapat terhubung ke server'
      }
    },
    async logout() {
      try {
        await fetch(`${BASE}/logout`, { method: 'POST', headers: this.authHeaders() })
      } finally {
        this.token = ''; this.user = null
        this.tournaments = []; this.teams = []; this.announcements = []
        localStorage.removeItem('token'); localStorage.removeItem('user')
        this.setMessage('Logout berhasil.')
      }
    },
    fetchAll() {
      this.fetchTournaments(); this.fetchAnnouncements()
      if (this.token) this.fetchTeams()
    },
    async fetchTournaments() {
      this.loadingTournaments = true
      try {
        const res = await fetch(`${BASE}/tournaments`, {
          headers: { 'Accept': 'application/json', ...this.authHeaders() }
        })
        const result = await res.json()
        this.tournaments = result.data || []
        console.log('Tournaments loaded:', this.tournaments)
      } catch { this.setError('Gagal mengambil data turnamen') }
      finally { this.loadingTournaments = false }
    },
    async submitTournament() {
      try {
        // Client-side validation (extra safety)
        if (!this.tournamentName.trim() || this.tournamentName.trim().length < 3) {
          this.setError('Nama turnamen minimal 3 karakter')
          return
        }
        if (!this.tournamentSchedule.trim()) {
          this.setError('Jadwal turnamen harus diisi')
          return
        }
        if (this.tournamentName.trim().length > 100) {
          this.setError('Nama turnamen maksimal 100 karakter')
          return
        }

        const url = this.isEditTournament
          ? `${BASE}/tournaments/${this.selectedTournamentId}`
          : `${BASE}/tournaments`
        
        const formData = new FormData()
        formData.append('name', this.tournamentName.trim())
        formData.append('schedule', this.tournamentSchedule.trim())
        formData.append('description', this.tournamentDescription.trim() || '')
        
        // Only append poster if it's a new file (not existing)
        if (this.tournamentPoster instanceof File) {
          formData.append('poster_image', this.tournamentPoster)
        }
        
        if (this.isEditTournament) {
          formData.append('_method', 'PUT')
        }
        
        const res = await fetch(url, { 
          method: 'POST', 
          headers: this.authHeaders(), 
          body: formData 
        })
        
        const result = await res.json()
        
        if (!res.ok) { 
          // Handle Laravel validation errors
          const errorMsg = result.message || 
                          result.errors?.name?.[0] || 
                          result.errors?.schedule?.[0] ||
                          'Gagal memproses turnamen'
          this.setError(errorMsg)
          return 
        }
        
        this.setMessage(this.isEditTournament ? 'Turnamen diupdate.' : 'Turnamen ditambahkan.')
        this.closeModal()
        this.fetchTournaments()
        
      } catch (e) { 
        console.error('Submit tournament error:', e)
        this.setError('Tidak dapat terhubung ke server') 
      }
    },
    

    startEditTournament(t) {
      this.tournamentName = t.name
      this.tournamentSchedule = t.schedule
      this.tournamentDescription = t.description || ''
      this.tournamentPoster = null // Don't preload file, just show preview
      this.selectedTournamentId = t.id
      this.selectedTournament = t // Store for existingPoster prop
      this.isEditTournament = true
    },

    cancelEditTournament() {
      this.tournamentName = ''
      this.tournamentSchedule = ''
      this.tournamentDescription = ''
      this.tournamentPoster = null
      this.selectedTournamentId = null
      this.selectedTournament = null
      this.isEditTournament = false
    },
    async deleteTournament(id) {
      if (!confirm('Hapus turnamen ini?')) return
      try {
        const res = await fetch(`${BASE}/tournaments/${id}`, { method: 'DELETE', headers: this.authHeaders() })
        const result = await res.json()
        if (!res.ok) { this.setError(result.message || 'Gagal'); return }
        this.setMessage('Turnamen dihapus.'); this.fetchTournaments()
      } catch { this.setError('Tidak dapat terhubung ke server') }
    },
    async fetchTeams() {
      this.loadingTeams = true
      try {
        const endpoint = this.isAdmin ? `${BASE}/teams` : `${BASE}/teams/my`
        const res = await fetch(endpoint, { headers: this.authHeaders() })
        const result = await res.json()
        this.teams = Array.isArray(result) ? result : (result.data || [])
        console.log('Teams loaded:', this.teams)
      } catch { this.setError('Gagal mengambil data tim') }
      finally { this.loadingTeams = false }
    },
    async submitTeam() {
      try {
        if (!this.teamTournamentId) {
          this.setError('Pilih turnamen terlebih dahulu')
          return
        }
        if (!this.teamName.trim() || this.teamName.trim().length < 2) {
          this.setError('Nama tim minimal 2 karakter')
          return
        }

        const formData = new FormData()
        formData.append('tournament_id', this.teamTournamentId)
        formData.append('team_name', this.teamName.trim())
        if (this.teamLogo instanceof File) {
          formData.append('logo', this.teamLogo)
        }
        
        const res = await fetch(`${BASE}/teams`, { 
          method: 'POST', 
          headers: this.authHeaders(), 
          body: formData 
        })
        const result = await res.json()
        
        if (!res.ok) { 
          this.setError(result.message || result.errors?.team_name?.[0] || 'Gagal mendaftar')
          return 
        }
        
        this.setMessage('Tim berhasil didaftarkan!')
        this.teamTournamentId = ''
        this.teamName = ''
        this.teamLogo = null
        this.fetchTeams()
        this.closeModal()
      } catch (e) { 
        console.error('Submit team error:', e)
        this.setError('Tidak dapat terhubung ke server') 
      }
    },
    async deleteTeam(id) {
      if (!confirm('Hapus pendaftaran ini?')) return
      try {
        const res = await fetch(`${BASE}/teams/${id}`, { method: 'DELETE', headers: this.authHeaders() })
        const result = await res.json()
        if (!res.ok) { this.setError(result.message || 'Gagal'); return }
        this.setMessage('Pendaftaran dihapus.'); this.fetchTeams()
      } catch { this.setError('Tidak dapat terhubung ke server') }
    },
    async fetchAnnouncements() {
      this.loadingAnnouncements = true
      try {
        const res = await fetch(`${BASE}/announcements`, {
          headers: { 'Accept': 'application/json', ...this.authHeaders() }
        })
        const result = await res.json()
        this.announcements = result.data || []
      } catch { this.setError('Gagal mengambil pengumuman') }
      finally { this.loadingAnnouncements = false }
    },
    async submitAnnouncement() {
      try {
        const url = this.isEditAnnouncement
          ? `${BASE}/announcements/${this.selectedAnnouncementId}`
          : `${BASE}/announcements`
        const method = this.isEditAnnouncement ? 'PUT' : 'POST'
        const res = await fetch(url, {
          method, headers: this.jsonHeaders(),
          body: JSON.stringify({ title: this.announcementTitle, content: this.announcementContent })
        })
        const result = await res.json()
        if (!res.ok) { this.setError(result.message || 'Gagal'); return }
        this.setMessage(this.isEditAnnouncement ? 'Pengumuman diupdate.' : 'Pengumuman dibuat.')
        this.closeModal()
        this.fetchAnnouncements()
      } catch { this.setError('Tidak dapat terhubung ke server') }
    },
    startEditAnnouncement(a) {
      this.announcementTitle = a.title; this.announcementContent = a.content
      this.selectedAnnouncementId = a.id; this.isEditAnnouncement = true
    },
    cancelEditAnnouncement() {
      this.announcementTitle = ''; this.announcementContent = ''
      this.selectedAnnouncementId = null; this.isEditAnnouncement = false
    },
    async deleteAnnouncement(id) {
      if (!confirm('Hapus pengumuman ini?')) return
      try {
        const res = await fetch(`${BASE}/announcements/${id}`, { method: 'DELETE', headers: this.authHeaders() })
        const result = await res.json()
        if (!res.ok) { this.setError(result.message || 'Gagal'); return }
        this.setMessage('Pengumuman dihapus.'); this.fetchAnnouncements()
      } catch { this.setError('Tidak dapat terhubung ke server') }
    },
    openEditAnnouncementFromDetail(a) {
      this.selectedAnnouncementDetail = null
      this.$nextTick(() => {
        this.openEditAnnouncement(a)
      })
    },
    async deleteAnnouncementFromDetail(id) {
      if (!confirm('Hapus pengumuman ini?')) return
      try {
        const res = await fetch(`${BASE}/announcements/${id}`, {
          method: 'DELETE', headers: this.authHeaders()
        })
        const result = await res.json()
        if (!res.ok) { this.setError(result.message || 'Gagal'); return }
        this.setMessage('Pengumuman dihapus.')
        this.selectedAnnouncementDetail = null
        this.fetchAnnouncements()
      } catch { this.setError('Tidak dapat terhubung ke server') }
    },
    openTournamentDetail(tournament) {
      this.selectedTournamentDetail = tournament
    },

    isAlreadyRegistered(tournamentId) {
      return this.teams.some(t =>
        t.tournament && t.tournament.id == tournamentId
      )
    },

    async submitTeamFromDetail({ tournamentId, teamName, logo }) {
      this.teamSubmitLoading = true
      try {
        const formData = new FormData()
        formData.append('tournament_id', tournamentId)
        formData.append('team_name', teamName)
        if (logo) formData.append('logo', logo)

        const res = await fetch(`${BASE}/teams`, {
          method: 'POST',
          headers: this.authHeaders(),
          body: formData
        })
        const result = await res.json()
        if (!res.ok) {
          this.setError(result.message || 'Gagal mendaftar')
          return
        }
        this.setMessage('Tim berhasil didaftarkan!')
        this.selectedTournamentDetail = null
        this.fetchTeams()
      } catch {
        this.setError('Tidak dapat terhubung ke server')
      } finally {
        this.teamSubmitLoading = false
      }
    }
  },
  mounted() {
    this.fetchTournaments(); this.fetchAnnouncements()
    if (this.token) this.fetchTeams()
  }
}
</script>

<style>
/* ── RESET & FONT ── */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ── TOKENS ── */
:root {
  --c-bg:         #0D0D14;
  --c-surface:    #111119;
  --c-card:       #13131E;
  --c-border:     #1E1E2E;
  --c-accent:     #7C6AF6;
  --c-accent-dim: rgba(124,106,246,0.15);
  --c-accent-hov: #6A57E8;
  --c-text:       #F0F0FA;
  --c-sub:        #A0A0C0;
  --c-muted:      #555575;
  --c-success:    #10B981;
  --c-success-bg: rgba(16,185,129,0.12);
  --c-error:      #F87171;
  --c-error-bg:   rgba(248,113,113,0.12);
  --c-danger:     #EF4444;
  --c-danger-bg:  rgba(239,68,68,0.12);
  --c-danger-hov: rgba(239,68,68,0.20);
}

/* ── BASE ── */
html, body { background: var(--c-bg); color: var(--c-text); }

body {
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14px;
  line-height: 1.6;
  -webkit-font-smoothing: antialiased;
}

/* ── APP SHELL ── */
.app { min-height: 100vh; background: var(--c-bg); }

/* ── NAVBAR ── */
.navbar {
  background: rgba(13,13,20,0.85);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--c-border);
  position: sticky; top: 0; z-index: 100;
}
.nav-inner {
  max-width: 1100px; margin: 0 auto;
  padding: 0 24px;
  height: 60px;
  display: flex; align-items: center; justify-content: space-between;
}
.nav-brand { display: flex; align-items: center; gap: 10px; }
.brand-icon {
  width: 36px; height: 36px;
  background: var(--c-accent-dim);
  border: 1px solid rgba(124,106,246,0.3);
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  color: var(--c-accent);
}
.brand-name { font-size: 15px; font-weight: 700; color: var(--c-text); }
.brand-logo {
  height: 50px;
  width: auto;
  object-fit: contain;
}
.nav-user { display: flex; align-items: center; gap: 10px; }
.user-name { font-size: 13px; color: var(--c-sub); }
.user-badge {
  font-size: 11px; font-weight: 600;
  padding: 3px 10px; border-radius: 20px;
  text-transform: uppercase; letter-spacing: 0.06em;
}
.user-badge.admin {
  background: var(--c-accent-dim);
  color: var(--c-accent);
  border: 1px solid rgba(124,106,246,0.3);
}
.user-badge.member {
  background: rgba(16,185,129,0.12);
  color: var(--c-success);
  border: 1px solid rgba(16,185,129,0.25);
}

/* ── MAIN ── */
.main { max-width: 1100px; margin: 0 auto; padding: 32px 24px 80px; }

/* ── TOAST ── */
.toast {
  display: flex; align-items: center; gap: 10px;
  padding: 12px 16px;
  border-radius: 10px;
  font-size: 13px; font-weight: 500;
  margin-bottom: 20px;
  border: 1px solid transparent;
}
.toast-success {
  background: var(--c-success-bg);
  color: var(--c-success);
  border-color: rgba(16,185,129,0.25);
}
.toast-error {
  background: var(--c-error-bg);
  color: var(--c-error);
  border-color: rgba(248,113,113,0.25);
}

/* ── AUTH ── */
.auth-wrapper {
  min-height: calc(100vh - 120px);
  display: flex; align-items: center; justify-content: center;
  gap: 80px;
}
.auth-hero { max-width: 400px; }
.auth-hero h1 { font-size: 36px; font-weight: 700; line-height: 1.2; margin-bottom: 16px; }
.auth-hero p { color: var(--c-sub); font-size: 15px; line-height: 1.7; }
.hero-logo {
  height: 60px;
  width: auto;
  object-fit: contain;
  margin-bottom: 20px;
  display: block;
}
.accent-text { color: var(--c-accent); }

.auth-card {
  background: var(--c-card);
  border: 1px solid var(--c-border);
  border-radius: 20px;
  padding: 32px;
  width: 100%; max-width: 400px;
}
.auth-header { display: flex; align-items: center; gap: 14px; margin-bottom: 28px; }
.auth-icon {
  width: 44px; height: 44px;
  background: var(--c-accent-dim);
  border: 1px solid rgba(124,106,246,0.3);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  color: var(--c-accent); flex-shrink: 0;
}
.auth-header h2 { font-size: 18px; font-weight: 700; margin-bottom: 2px; }
.auth-subtitle { font-size: 13px; color: var(--c-muted); }
.auth-switch { margin-top: 20px; text-align: center; font-size: 13px; color: var(--c-muted); }
.auth-switch a { color: var(--c-accent); text-decoration: none; font-weight: 500; }
.auth-switch a:hover { text-decoration: underline; }

/* ── CONTENT ── */
.content { display: flex; flex-direction: column; gap: 0; }
.divider { height: 1px; background: var(--c-border); margin: 40px 0; }

/* ── SECTION ── */
.section { margin-bottom: 8px; }
.section-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; }
.section-title { font-size: 20px; font-weight: 700; color: var(--c-text); margin-bottom: 4px; }
.section-sub { font-size: 13px; color: var(--c-muted); }
.section-wrap { display: flex; flex-direction: column; gap: 0; }

.section-hint {
  font-size: 12px;
  color: var(--c-muted);
  font-style: italic;
  align-self: center;
}

/* ── FORM CARD ── */
.form-card {
  background: var(--c-card);
  border: 1px solid var(--c-border);
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 24px;
}
.form-title {
  font-size: 16px; font-weight: 700;
  color: var(--c-text);
  margin-bottom: 20px;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--c-border);
}
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.form-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 4px; }

/* ── FIELD ── */
.field-group { margin-bottom: 16px; }
.field-group label {
  display: block;
  font-size: 12px; font-weight: 600;
  color: var(--c-sub);
  text-transform: uppercase; letter-spacing: 0.06em;
  margin-bottom: 8px;
}
.field-group input,
.field-group textarea,
.field-group select {
  width: 100%;
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: 10px;
  color: var(--c-text);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14px;
  padding: 10px 14px;
  outline: none;
  transition: border-color 0.2s;
  appearance: none;
}
.field-group input:focus,
.field-group textarea:focus,
.field-group select:focus {
  border-color: var(--c-accent);
  box-shadow: 0 0 0 3px var(--c-accent-dim);
}
.field-group input::placeholder,
.field-group textarea::placeholder { color: var(--c-muted); }
.field-group textarea { resize: vertical; min-height: 90px; }
input[type="date"],
input[type="time"] {
  color-scheme: dark;
}

/* ── FILE INPUT ── */
.file-input {
  position: relative;
  display: flex; align-items: center; gap: 10px;
  background: var(--c-surface);
  border: 1px dashed var(--c-border);
  border-radius: 10px;
  padding: 12px 14px;
  color: var(--c-muted); font-size: 13px;
  cursor: pointer;
  transition: border-color 0.2s;
}
.file-input:hover { border-color: var(--c-accent); color: var(--c-sub); }
.file-input input[type="file"] {
  position: absolute; inset: 0;
  opacity: 0; cursor: pointer;
  width: 100%; height: 100%;
  border: none; background: none;
  padding: 0;
}
.hint { font-size: 12px; color: var(--c-muted); margin-top: 6px; }

/* ── BUTTONS ── */
.btn-primary {
  background: var(--c-accent);
  color: #fff; border: none;
  border-radius: 10px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14px; font-weight: 600;
  padding: 10px 20px;
  cursor: pointer;
  transition: background 0.2s, transform 0.1s;
}
.btn-primary:hover { background: var(--c-accent-hov); }
.btn-primary:active { transform: scale(0.98); }
.btn-primary.btn-full { width: 100%; padding: 12px; }

.btn-ghost {
  background: transparent;
  color: var(--c-sub);
  border: 1px solid var(--c-border);
  border-radius: 10px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 14px; font-weight: 500;
  padding: 10px 20px;
  cursor: pointer;
  transition: background 0.2s, border-color 0.2s;
}
.btn-ghost:hover { background: var(--c-surface); border-color: var(--c-accent-dim); color: var(--c-text); }

.btn-danger {
  background: var(--c-danger-bg);
  color: var(--c-danger);
  border: 1px solid rgba(239,68,68,0.2);
  border-radius: 8px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 13px; font-weight: 500;
  padding: 7px 14px;
  cursor: pointer;
  transition: background 0.2s;
}
.btn-danger:hover { background: var(--c-danger-hov); }

.btn-sm { padding: 6px 12px !important; font-size: 13px !important; }
.btn-sm.btn-ghost {
  display: flex; align-items: center; gap: 5px;
  padding: 6px 12px;
}

.btn-add {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 9px 16px;
  font-size: 13px;
  white-space: nowrap;
  flex-shrink: 0;
}

/* ── STATE BOX ── */
.state-box {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 12px;
  padding: 48px 24px;
  background: var(--c-card);
  border: 1px dashed var(--c-border);
  border-radius: 16px;
  color: var(--c-muted);
  font-size: 14px;
}

/* ── SPINNER ── */
.spinner {
  width: 28px; height: 28px;
  border: 2px solid var(--c-border);
  border-top-color: var(--c-accent);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ── SCROLLBAR ── */
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: var(--c-bg); }
::-webkit-scrollbar-thumb { background: var(--c-border); border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: var(--c-muted); }

/* ── RESPONSIVE ── */
@media (max-width: 768px) {
  .auth-wrapper { flex-direction: column; gap: 32px; padding: 20px 0; }
  .auth-hero h1 { font-size: 26px; }
  .form-grid { grid-template-columns: 1fr; }
  .nav-user .user-name { display: none; }
}
</style>