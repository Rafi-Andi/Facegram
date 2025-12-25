<script setup>
import router from '@/router'
import url from '@/router/url'
import axios from 'axios'
import { onMounted, ref } from 'vue'

const formRegister = ref({
  full_name: '',
  username: '',
  password: '',
  bio: '',
  is_private: false,
})
const isLoading = ref(false)

const token = localStorage.getItem('token')
const username = localStorage.getItem('username')
if (token) {
  router.push({ name: 'Profile', params: { username: username } })
}
const handleRegister = async () => {
  try {
    console.log(formRegister.value)
    isLoading.value = true
    const response = await axios.post(`${url}/api/v1/auth/register`, formRegister.value)

    localStorage.setItem('token', response.data.token)
    localStorage.setItem('username', response.data.user.username)
    alert(response.data.message)
    router.push({ name: 'Homepage' })
  } catch (error) {
    alert(error.response.data.message)
    console.log(error)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  document.title = 'Register User'
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
              <h5 class="mb-0">Register</h5>
            </div>
            <div class="card-body">
              <form @submit.prevent="handleRegister()">
                <div class="mb-2">
                  <label for="full_name">Full Name</label>
                  <input
                    v-model="formRegister.full_name"
                    type="text"
                    class="form-control"
                    id="full_name"
                    name="full_name"
                  />
                </div>

                <div class="mb-2">
                  <label for="username">Username</label>
                  <input
                    type="text"
                    v-model="formRegister.username"
                    class="form-control"
                    id="username"
                    name="username"
                  />
                </div>

                <div class="mb-3">
                  <label for="password">Password</label>
                  <input
                    type="password"
                    v-model="formRegister.password"
                    class="form-control"
                    id="password"
                    name="password"
                  />
                </div>

                <div class="mb-3">
                  <label for="bio">Bio</label>
                  <textarea
                    name="bio"
                    id="bio"
                    v-model="formRegister.bio"
                    cols="30"
                    rows="3"
                    class="form-control"
                  ></textarea>
                </div>

                <div class="mb-3 d-flex align-items-center gap-2">
                  <input
                    type="checkbox"
                    v-model="formRegister.is_private"
                    id="is_private"
                    name="is_private"
                  />
                  <label for="is_private">Private Account</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">Register</button>
              </form>
            </div>
          </div>

          <div class="text-center mt-4">
            Already have an account?
            <router-link :to="{name: 'Login'}">Login</router-link>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>
