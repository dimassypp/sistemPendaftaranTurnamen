<template>
  <div class="t-card" @click="$emit('select', tournament)" role="button">
    <div class="t-poster">
      <img
        v-if="tournament.poster_image"
        :src="'http://localhost:8000/storage/' + tournament.poster_image"
        :alt="tournament.name"
      />
      <div v-else class="t-poster-empty">Tidak ada poster</div>
    </div>
    <div class="t-body">
      <h3 class="t-title">{{ tournament.name }}</h3>
      <p class="t-date">{{ formatSchedule(tournament.schedule) }}</p>
      <p class="t-desc">{{ tournament.description || 'Tidak ada deskripsi.' }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TournamentCard',
  props: {
    tournament: { type: Object, required: true },
    isAdmin: { type: Boolean, required: true }
  },
  methods: {
    formatSchedule(schedule) {
      if (!schedule) return '-'
      try {
        return new Date(schedule).toLocaleString('id-ID', {
          day: 'numeric', month: 'long', year: 'numeric',
          hour: '2-digit', minute: '2-digit'
        })
      } catch { return schedule }
    }
  }
}
</script>

<style scoped>
.t-card {
  background: var(--c-card);
  border: 1px solid var(--c-border);
  border-radius: 14px;
  overflow: hidden;
  cursor: pointer;
  display: flex; flex-direction: column;
  transition: border-color 0.2s, transform 0.2s;
}
.t-card:hover { border-color: rgba(124,106,246,0.4); transform: translateY(-3px); }

.t-poster { width: 100%; height: 140px; overflow: hidden; background: var(--c-surface); flex-shrink: 0; }
.t-poster img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.3s; }
.t-card:hover .t-poster img { transform: scale(1.04); }
.t-poster-empty {
  height: 100%; display: flex; align-items: center; justify-content: center;
  font-size: 12px; color: var(--c-muted);
}

.t-body { padding: 16px; flex: 1; display: flex; flex-direction: column; gap: 6px; }
.t-title { font-size: 15px; font-weight: 700; color: var(--c-text); margin: 0; line-height: 1.4; }
.t-date { font-size: 12px; color: var(--c-accent); font-weight: 500; margin: 0; }
.t-desc {
  font-size: 12px; color: var(--c-sub); line-height: 1.6; margin: 0; flex: 1;
  overflow: hidden; display: -webkit-box;
  -webkit-line-clamp: 2; -webkit-box-orient: vertical;
}
.t-admin-actions { display: flex; gap: 6px; margin-top: auto; padding-top: 10px; }
</style>