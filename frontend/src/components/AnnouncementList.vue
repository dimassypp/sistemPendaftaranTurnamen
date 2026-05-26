<template>
  <div>
    <div v-if="loading" class="state-box">
      <div class="spinner"></div>
      <span>Memuat pengumuman...</span>
    </div>

    <div v-else-if="announcements.length === 0" class="state-box">
      <span>Belum ada pengumuman</span>
    </div>

    <div v-else class="announcement-list">
      <div
        v-for="a in announcements"
        :key="a.id"
        class="announcement-card"
        @click="$emit('select-announcement', a)"
      >
        <div class="ann-header">
          <div class="ann-dot"></div>
          <h3 class="ann-title">{{ a.title }}</h3>
          <span class="ann-author">{{ a.user ? a.user.name : '-' }}</span>
          <div v-if="isAdmin" class="ann-actions" @click.stop>
            <button class="btn-ghost btn-sm" @click="$emit('edit-announcement', a)">Edit</button>
            <button class="btn-danger btn-sm" @click="$emit('delete-announcement', a.id)">Hapus</button>
          </div>
        </div>
        <p class="ann-preview">{{ a.content }}</p>
        <span class="ann-read-more">Baca selengkapnya</span>
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
  cursor: pointer;
  transition: border-color 0.2s, transform 0.15s;
}
.announcement-card:hover {
  border-color: rgba(124,106,246,0.35);
  transform: translateY(-1px);
}

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

.ann-preview {
  font-size: 13px; color: var(--c-sub);
  line-height: 1.6; margin: 0 0 8px;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}
.ann-read-more {
  font-size: 12px; color: var(--c-accent);
  font-weight: 600;
}
</style>