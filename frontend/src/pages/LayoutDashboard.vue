<script setup>
import router from '@/router'
import url from '@/router/url'
import axios from 'axios'

const token = localStorage.getItem('token')
const username = localStorage.getItem('username')
if (!token) {
  router.push({ name: 'Login' })
}

const handleLogout = async () => {
  try {
    const response = await axios.post(`${url}/api/v1/auth/logout`, '', {
        headers: {
            Authorization: `Bearer ${token}`
        }
    })
    alert(response.data.message)
    localStorage.removeItem('token')
    router.push({name: 'Login'})
  } catch (error) {
    console.log(error)
    alert(error.response.data.message)
  }
}

</script>

<template>
  <nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top">
    <div class="container">
      <router-link class="navbar-brand" :to="{name:'Homepage'}">Facegram</router-link>
      <div class="navbar-nav">
        <router-link :to="{name: 'Profile', params: {username: username}}" class="nav-link">{{ username }}</router-link>
        <p @click="handleLogout()" style="cursor: pointer;" class="nav-link" href="index.html">Logout</p>
      </div>
    </div>
  </nav>
  <router-view></router-view>
</template>
