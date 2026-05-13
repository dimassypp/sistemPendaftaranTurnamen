<template>
  <div>
    <div v-if="loading" class="state-box">
      <div class="spinner"></div>
      <span>Memuat data tim...</span>
    </div>

    <div v-else-if="teams.length === 0" class="state-box">
      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
      </svg>
      <span>Belum ada pendaftaran tim</span>
    </div>

    <div v-else class="team-list">
      <div v-for="team in teams" :key="team.id" class="team-row">

        <!-- Logo -->
        <div class="team-logo">
          <img
            v-if="team.logo"
            :src="'http://localhost:8000/storage/' + team.logo"
            :alt="team.team_name"
          />
          <div v-else class="team-logo-placeholder">
            {{ team.team_name ? team.team_name[0].toUpperCase() : '?' }}
          </div>
        </div>

        <!-- Info utama -->
        <div class="team-info">
          <p class="team-name">{{ team.team_name }}</p>
          <p class="team-tournament">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
            {{ team.tournament ? team.tournament.name : 'Turnamen tidak ditemukan' }}
          </p>
        </div>

        <!-- Pendaftar (admin only) -->
        <div v-if="isAdmin" class="team-registrant">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          {{ team.user ? team.user.name : '-' }}
        </div>

        <!-- Status
        <div class="team-status">
          <span class="status-badge" :class="team.status || 'pending'">
            {{ statusLabel(team.status) }}
          </span>
        </div> -->

        <!-- Hapus (admin only) -->
        <div v-if="isAdmin">
          <button class="btn-danger btn-sm" @click="$emit('delete-team', team.id)">Hapus</button>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TeamList',
  props: {
    teams: { type: Array, required: true },
    loading: { type: Boolean, required: true },
    isAdmin: { type: Boolean, required: true }
  },
  methods: {
    // statusLabel(status) {
    //   const map = { pending: 'Menunggu', accepted: 'Diterima', rejected: 'Ditolak' }
    //   return map[status] || 'Menunggu'
    // }
  }
}
</script>

<style scoped>
.team-list { display: flex; flex-direction: column; gap: 8px; }
.team-row {
  display: flex; align-items: center; gap: 14px;
  background: var(--c-card);
  border: 1px solid var(--c-border);
  border-radius: 12px;
  padding: 12px 16px;
  transition: border-color 0.2s;
}
.team-row:hover { border-color: rgba(124,106,246,0.3); }

.team-logo { width: 44px; height: 44px; border-radius: 10px; overflow: hidden; flex-shrink: 0; }
.team-logo img { width: 100%; height: 100%; object-fit: cover; }
.team-logo-placeholder {
  width: 100%; height: 100%;
  background: var(--c-accent-dim);
  color: var(--c-accent);
  display: flex; align-items: center; justify-content: center;
  font-weight: 700; font-size: 18px;
}

.team-info { flex: 1; min-width: 0; }
.team-name { font-size: 14px; font-weight: 600; color: var(--c-text); margin: 0 0 3px; }
.team-tournament {
  display: flex; align-items: center; gap: 5px;
  font-size: 12px; color: var(--c-muted); margin: 0;
}

.team-registrant {
  display: flex; align-items: center; gap: 5px;
  font-size: 12px; color: var(--c-sub);
  background: var(--c-surface);
  padding: 5px 10px; border-radius: 20px;
  border: 1px solid var(--c-border);
  flex-shrink: 0;
}

.status-badge {
  font-size: 11px; font-weight: 600;
  padding: 4px 10px; border-radius: 20px;
  letter-spacing: 0.04em; flex-shrink: 0;
}
.status-badge.pending {
  background: rgba(245,158,11,0.12);
  color: #F59E0B;
  border: 1px solid rgba(245,158,11,0.25);
}
.status-badge.accepted {
  background: rgba(16,185,129,0.12);
  color: #10B981;
  border: 1px solid rgba(16,185,129,0.25);
}
.status-badge.rejected {
  background: rgba(239,68,68,0.12);
  color: #EF4444;
  border: 1px solid rgba(239,68,68,0.25);
}
</style>