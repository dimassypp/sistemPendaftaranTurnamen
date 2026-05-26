<template>
  <teleport to="body">
    <div class="overlay" @click.self="$emit('close')">
      <div class="ann-modal">

        <div class="ann-modal-header">
          <div class="ann-modal-meta">
            <div class="ann-dot"></div>
            <span class="ann-modal-author">{{ announcement.user ? announcement.user.name : '-' }}</span>
          </div>
          <div class="ann-modal-actions">
            <template v-if="isAdmin">
              <button class="btn-ghost btn-sm" @click="$emit('edit', announcement)">Edit</button>
              <button class="btn-danger btn-sm" @click="$emit('delete', announcement.id)">Hapus</button>
            </template>
            <button class="close-btn" @click="$emit('close')">✕</button>
          </div>
        </div>

        <div class="ann-modal-body">
          <h2 class="ann-modal-title">{{ announcement.title }}</h2>
          <p class="ann-modal-content">{{ announcement.content }}</p>
        </div>

        <div class="ann-modal-footer">
          <button class="btn-ghost" @click="$emit('close')">Tutup</button>
        </div>

      </div>
    </div>
  </teleport>
</template>

<script>
export default {
  name: 'AnnouncementDetailModal',
  props: {
    announcement: { type: Object, required: true },
    isAdmin: { type: Boolean, required: true }
  },
  mounted() { document.body.style.overflow = 'hidden' },
  unmounted() { document.body.style.overflow = '' }
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
.ann-modal {
  background: var(--c-card);
  border: 1px solid var(--c-border);
  border-radius: 20px;
  width: 100%; max-width: 600px;
  max-height: 85vh;
  display: flex; flex-direction: column;
  animation: pop-in 0.2s ease;
}
@keyframes pop-in {
  from { opacity: 0; transform: scale(0.95) translateY(8px); }
  to   { opacity: 1; transform: scale(1) translateY(0); }
}

.ann-modal-header {
  display: flex; align-items: center;
  justify-content: space-between;
  padding: 18px 20px 14px;
  border-bottom: 1px solid var(--c-border);
  flex-shrink: 0;
}
.ann-modal-meta { display: flex; align-items: center; gap: 10px; }
.ann-dot {
  width: 8px; height: 8px;
  border-radius: 50%; background: var(--c-accent);
}
.ann-modal-author {
  font-size: 12px; color: var(--c-muted);
  background: var(--c-surface);
  padding: 3px 10px; border-radius: 20px;
  border: 1px solid var(--c-border);
}
.ann-modal-actions { display: flex; align-items: center; gap: 8px; }
.close-btn {
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: 8px;
  width: 30px; height: 30px;
  color: var(--c-muted);
  font-size: 13px; font-weight: 700;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: background 0.2s, color 0.2s;
}
.close-btn:hover { background: var(--c-border); color: var(--c-text); }

.ann-modal-body {
  padding: 24px 24px 16px;
  overflow-y: auto; flex: 1;
}
.ann-modal-title {
  font-size: 22px; font-weight: 700;
  color: var(--c-text);
  line-height: 1.3;
  margin: 0 0 20px;
}
.ann-modal-content {
  font-size: 15px; color: var(--c-sub);
  line-height: 1.8; margin: 0;
  white-space: pre-wrap;
}

.ann-modal-footer {
  padding: 14px 20px;
  border-top: 1px solid var(--c-border);
  display: flex; justify-content: flex-end;
  flex-shrink: 0;
}
</style>