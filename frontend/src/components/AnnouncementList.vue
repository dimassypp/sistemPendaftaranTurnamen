<template>
  <div>
    <div v-if="loading" class="state-box">
      <div class="spinner"></div>
      <span>Memuat pengumuman...</span>
    </div>

    <div v-else-if="announcements.length === 0" class="state-box">
      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
      </svg>
      <span>Belum ada pengumuman</span>
    </div>

    <div v-else class="announcement-list">
      <div v-for="a in announcements" :key="a.id" class="announcement-card">
        <div class="ann-header">
          <div class="ann-dot"></div>
          <h3 class="ann-title">{{ a.title }}</h3>
          <span class="ann-author">{{ a.user ? a.user.name : '-' }}</span>
          <div v-if="isAdmin" class="ann-actions">
            <button class="btn-ghost btn-sm" @click="$emit('edit-announcement', a)">Edit</button>
            <button class="btn-danger btn-sm" @click="$emit('delete-announcement', a.id)">Hapus</button>
          </div>
        </div>
        <p class="ann-content">{{ a.content }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AnnouncementList',
  props: {
    announcements: { type: Array, required: true },
    loading: { type: Boolean, required: true },
    isAdmin: { type: Boolean, required: true }
  }
}
</script>

<style scoped>
.announcement-list { display: flex; flex-direction: column; gap: 10px; }
.announcement-card {
  background: var(--c-card);
  border: 1px solid var(--c-border);
  border-radius: 12px;
  padding: 14px 18px;
  transition: border-color 0.2s;
}
.announcement-card:hover { border-color: rgba(124,106,246,0.3); }
.ann-header {
  display: flex; align-items: center;
  gap: 10px; margin-bottom: 8px;
  flex-wrap: wrap;
}
.ann-dot {
  width: 7px; height: 7px;
  border-radius: 50%;
  background: var(--c-accent);
  flex-shrink: 0;
}
.ann-title {
  font-size: 14px; font-weight: 600;
  color: var(--c-text); flex: 1; margin: 0;
}
.ann-author {
  font-size: 11px; color: var(--c-muted);
  background: var(--c-surface);
  padding: 2px 8px; border-radius: 20px;
  border: 1px solid var(--c-border);
  flex-shrink: 0;
}
.ann-actions { display: flex; gap: 6px; flex-shrink: 0; }
.ann-content {
  font-size: 13px; color: var(--c-sub);
  line-height: 1.7; margin: 0;
}
</style>