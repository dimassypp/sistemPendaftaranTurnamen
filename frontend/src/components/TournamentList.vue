<template>
  <div>
    <div v-if="loading" class="state-box">
      <div class="spinner"></div>
      <span>Memuat turnamen...</span>
    </div>
    <div v-else-if="tournaments.length === 0" class="state-box">
      <span>Belum ada turnamen yang tersedia</span>
    </div>
    <div v-else class="grid-cards">
      <TournamentCard
        v-for="t in tournaments"
        :key="t.id"
        :tournament="t"
        :isAdmin="isAdmin"
        @select="$emit('select-tournament', $event)"
        @edit="$emit('edit-tournament', $event)"
        @delete="$emit('delete-tournament', $event.id)"
      />
    </div>
  </div>
</template>

<script>
import TournamentCard from './TournamentCard.vue'
export default {
  name: 'TournamentList',
  components: { TournamentCard },
  props: {
    tournaments: { type: Array, required: true },
    loading: { type: Boolean, required: true },
    isAdmin: { type: Boolean, required: true }
  },
  emits: ['select-tournament', 'edit-tournament', 'delete-tournament']
}
</script>

<style scoped>
.grid-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
@media (max-width: 900px) { .grid-cards { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 560px) { .grid-cards { grid-template-columns: 1fr; } }
</style>