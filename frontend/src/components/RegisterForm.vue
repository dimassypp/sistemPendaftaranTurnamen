<template>
  <div class="auth-card">

    <h2 class="auth-title">Buat Akun Baru</h2>
    <p class="auth-subtitle">Bergabung dan daftarkan timmu sekarang</p>

    <div class="field-group" :class="{ 'has-error': errors.name }">
      <label>Nama Lengkap</label>
      <input
        :value="name"
        @input="$emit('update:name', $event.target.value)"
        placeholder="Nama lengkapmu"
        type="text"
        :class="{ 'input-error': errors.name }"
      />
      <p v-if="errors.name" class="field-error">{{ errors.name }}</p>
    </div>

    <div class="field-group" :class="{ 'has-error': errors.email }">
      <label>Email</label>
      <input
        :value="email"
        @input="$emit('update:email', $event.target.value)"
        placeholder="nama@email.com"
        type="email"
        :class="{ 'input-error': errors.email }"
      />
      <p v-if="errors.email" class="field-error">{{ errors.email }}</p>
    </div>

    <div class="field-group" :class="{ 'has-error': errors.password }">
      <label>Password</label>
      <div class="input-wrap">
        <input
          :value="password"
          @input="$emit('update:password', $event.target.value)"
          :type="showPassword ? 'text' : 'password'"
          placeholder="Minimal 6 karakter"
          :class="{ 'input-error': errors.password }"
        />
        <button class="toggle-pass" type="button" @click="showPassword = !showPassword">
          {{ showPassword ? 'Sembunyikan' : 'Tampilkan' }}
        </button>
      </div>
      <p v-if="errors.password" class="field-error">{{ errors.password }}</p>
    </div>

    <div class="field-group" :class="{ 'has-error': errors.password_confirmation }">
      <label>Konfirmasi Password</label>
      <input
        :value="passwordConfirmation"
        @input="$emit('update:passwordConfirmation', $event.target.value)"
        :type="showPassword ? 'text' : 'password'"
        placeholder="Ulangi password"
        :class="{ 'input-error': errors.password_confirmation }"
      />
      <p v-if="errors.password_confirmation" class="field-error">{{ errors.password_confirmation }}</p>
    </div>

    <div v-if="errors.general" class="alert-error">
      {{ errors.general }}
    </div>

    <button class="btn-primary btn-full" @click="$emit('register')">Buat Akun</button>

    <p class="auth-switch">
      Sudah punya akun?
      <a href="#" @click.prevent="$emit('show-login')">Masuk di sini</a>
    </p>
  </div>
</template>

<script>
export default {
  name: 'RegisterForm',
  props: {
    name: { type: String, required: true },
    email: { type: String, required: true },
    password: { type: String, required: true },
    passwordConfirmation: { type: String, required: true },
    errors: { type: Object, default: () => ({}) }
  },
  data() {
    return { showPassword: false }
  }
}
</script>

<style scoped>
.auth-brand { text-align: center; margin-bottom: 24px; }
.auth-title { font-size: 20px; font-weight: 700; color: var(--c-text); margin-bottom: 6px; }
.auth-subtitle { font-size: 13px; color: var(--c-muted); margin-bottom: 28px; }

.input-wrap { position: relative; }
.input-wrap input { padding-right: 100px; }
.toggle-pass {
  position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
  background: none; border: none; color: var(--c-accent);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: 12px; font-weight: 600; cursor: pointer; padding: 4px 0;
}
.toggle-pass:hover { color: var(--c-accent-hov); }

.field-error { font-size: 12px; color: var(--c-error); margin-top: 6px; font-weight: 500; }
.input-error {
  border-color: var(--c-error) !important;
  box-shadow: 0 0 0 3px rgba(248,113,113,0.12) !important;
}

.alert-error {
  background: var(--c-error-bg);
  border: 1px solid rgba(248,113,113,0.25);
  color: var(--c-error);
  border-radius: 10px;
  padding: 12px 14px;
  font-size: 13px; font-weight: 500;
  margin-bottom: 16px;
}

.auth-switch { margin-top: 20px; text-align: center; font-size: 13px; color: var(--c-muted); }
.auth-switch a { color: var(--c-accent); text-decoration: none; font-weight: 600; }
.auth-switch a:hover { text-decoration: underline; }
</style>