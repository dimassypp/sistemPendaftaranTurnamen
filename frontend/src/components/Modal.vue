<template>
  <teleport to="body">
    <div class="modal-overlay" @click.self="$emit('close')">
      <div class="modal-box" :style="{ maxWidth: width }">
        <div class="modal-header">
          <h3 class="modal-title">{{ title }}</h3>
          <button class="modal-close" @click="$emit('close')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
          </button>
        </div>
        <div class="modal-body">
          <slot />
        </div>
      </div>
    </div>
  </teleport>
</template>

<script>
export default {
  name: 'Modal',
  props: {
    title: { type: String, required: true },
    width: { type: String, default: '520px' }
  },
  emits: ['close'],
  mounted() {
    document.body.style.overflow = 'hidden'
  },
  unmounted() {
    document.body.style.overflow = ''
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(4px);
  z-index: 999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}
.modal-box {
  background: var(--c-card);
  border: 1px solid var(--c-border);
  border-radius: 18px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  animation: modal-in 0.2s ease;
}
@keyframes modal-in {
  from { opacity: 0; transform: scale(0.95) translateY(8px); }
  to   { opacity: 1; transform: scale(1) translateY(0); }
}
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px 16px;
  border-bottom: 1px solid var(--c-border);
}
.modal-title { font-size: 16px; font-weight: 700; color: var(--c-text); }
.modal-close {
  background: var(--c-surface);
  border: 1px solid var(--c-border);
  border-radius: 8px;
  width: 32px; height: 32px;
  display: flex; align-items: center; justify-content: center;
  color: var(--c-muted);
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
  flex-shrink: 0;
}
.modal-close:hover { background: var(--c-border); color: var(--c-text); }
.modal-body { padding: 20px 24px 24px; }
</style>