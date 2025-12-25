<script setup>
import router from '@/router'
import url from '@/router/url'
import axios from 'axios'
import { onMounted, ref } from 'vue'

const formLogin = ref({
  username: '',
  password: '',
})

const isLoading = ref(false)
const token = localStorage.getItem('token')
const username = localStorage.getItem('username')

if (token) {
  router.push({ name: 'Profile', params: { username: username } })
}
const errors = ref(null)

const handleLogin = async () => {
  try {
    isLoading.value = true
    const response = await axios.post(`${url}/api/v1/auth/login`, formLogin.value)

    localStorage.setItem('token', response.data.token)
    localStorage.setItem('username', response.data.user.username)
    localStorage.setItem('is_private', response.data.user.is_private)
    alert(response.data.message)
    router.push({ name: 'Homepage' })
  } catch (error) {
    alert(error.response.data.message)
    errors.value = error.response.data.errors
  } finally {
    isLoading.value = false
  }
}
onMounted(() => {
  document.title = 'Login User'
})
</script>

<template>
  <nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top">
    <div class="container">
      <a class="navbar-brand m-auto" href="index.html">Facegram</a>
    </div>
  </nav>

  <main class="mt-5">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-md-5">
          <div class="card">
            <div
              class="card-header d-flex align-items-center justify-content-between bg-transparent py-3"
            >
              <h5 class="mb-0">Login</h5>
            </div>
            <div class="card-body">
              <form @submit.prevent="handleLogin()">
                <div class="mb-2">
                  <label for="username">Username</label>
                  <input
                    v-model="formLogin.username"
                    type="text"
                    class="form-control"
                    id="username"
                    name="username"
                  />
                   <p class="text-danger" v-if="errors?.username?.[0]">
                    {{ errors?.username?.[0] }} </p>
                </div>

                <div class="mb-3">
                  <label for="password">Password</label>
                  <input
                    v-model="formLogin.password"
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                  />
                   <p class="text-danger" v-if="errors?.password?.[0]">
                    {{ errors?.password?.[0] }}</p>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                  {{ isLoading ? 'Loading..' : 'Login' }}
                </button>
              </form>
            </div>
          </div>

          <div class="text-center mt-4">
            Don't have account? <router-link :to="{ name: 'Register' }">Register</router-link>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>
