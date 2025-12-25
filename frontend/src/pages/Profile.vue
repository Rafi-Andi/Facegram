<script setup>
import url from '@/router/url'
import axios from 'axios'
import { onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
const route = useRoute()
const username = ref(route.params.username)
const dataDetailUser = ref(null)
const dataFollower = ref(null)
const dataFollowing = ref(null)

const fetchDetailUser = async () => {
  const response = await axios.get(`${url}/api/v1/users/${username.value}`, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`,
    },
  })

  dataDetailUser.value = response.data
  console.log(dataDetailUser.value)
}

const fetchFollower = async () => {
  const response = await axios.get(`${url}/api/v1/users/${username.value}/followers`, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`,
    },
  })
  dataFollower.value = response.data.followers
}
const fetchFollowing = async () => {
  const response = await axios.get(`${url}/api/v1/users/${username.value}/following`, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`,
    },
  })
  dataFollowing.value = response.data.following
}

const deletePost = async (id) => {
  try {
    const response = await axios.delete(`${url}/api/v1/posts/${id}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    })
    const index = dataDetailUser.value.posts.findIndex((post) => post.id === id)
    dataDetailUser.value.posts.splice(index, 1)
    alert('Success delete post')
  } catch (error) {
    alert(error.response.data.message)
  }
}

const toggleFollow = async () => {
  if (dataDetailUser.value.following_status === 'not-following') {
    try {
      const response = await axios.post(
        `${url}/api/v1/users/${dataDetailUser.value.username}/follow`,
        '',
        {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        },
      )
      console.log(response)
      dataDetailUser.value.following_status = response.data.status
      alert(response.data.message)
      dataDetailUser.value.followers_count += 1
    } catch (error) {
      alert(error.response.data.message)
    }
  } else {
    try {
      const response = await axios.delete(
        `${url}/api/v1/users/${dataDetailUser.value.username}/unfollow`,
        {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        },
      )
      console.log(response)
      dataDetailUser.value.following_status = response.data.status
      alert(response.data.message)
      dataDetailUser.value.followers_count -= 1
    } catch (error) {
      alert(error.response.data.message)
    }
  }
}

const isLoading = ref(false)
const fetchAll = async () => {
  try {
    isLoading.value = true
    await Promise.all([fetchDetailUser(), fetchFollower(), fetchFollowing()])
  } catch (error) {
    alert(error.response.data.message)
  } finally {
    isLoading.value = false
  }
}

watch(
  () => route.params.username,
  (newUsername) => {
    username.value = newUsername
    fetchAll()
  },
)

onMounted(() => {
  fetchAll()
})

onMounted(() => {
    document.title = 'Profile'
})
</script>

<template>
  <main class="mt-5" style="display: flex; justify-content: center; align-items: center;">
    <h2 style="margin-top: 200px;" v-if="isLoading">Loading...</h2>
    <div v-else class="container py-5" >
      <div class="px-5 py-4 bg-light mb-4 d-flex align-items-center justify-content-between">
        <div>
          <div class="d-flex align-items-center gap-2 mb-1">
            <h5 class="mb-0">{{ dataDetailUser?.full_name }}</h5>
            <span>@{{ dataDetailUser?.username }}</span>
          </div>
          <small class="mb-0 text-muted">
            {{ dataDetailUser?.bio }}
          </small>
        </div>
        <div>
          <p
            @click="toggleFollow()"
            class="btn btn-primary w-100 mb-2"
            :class="{
              'btn-secondary':
                dataDetailUser?.following_status === 'following' ||
                dataDetailUser?.following_status === 'requested',
            }"
            v-if="!dataDetailUser?.is_your_account"
          >
            {{
              dataDetailUser?.following_status === 'following'
                ? 'Following'
                : dataDetailUser?.following_status === 'requested'
                  ? 'Request'
                  : 'Follow'
            }}
          </p>
          <router-link :to="{ name: 'CreatePost' }" class="btn btn-primary w-100 mb-2" v-else
            >+ Create Post</router-link
          >
          <div class="d-flex gap-3">
            <div>
              <div class="profile-label">
                <b>{{ dataDetailUser?.posts_count }}</b> posts
              </div>
            </div>
            <div class="profile-dropdown">
              <div class="profile-label">
                <b>{{ dataDetailUser?.followers_count }}</b> followers
              </div>
              <div class="profile-list">
                <div class="card">
                  <div class="card-body">
                    <div class="profile-user" v-for="(item, index) in dataFollower" :key="index">
                      <router-link :to="{ name: 'Profile', params: { username: item?.username } }"
                        >@{{ item?.username }}</router-link
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="profile-dropdown">
              <div class="profile-label">
                <b>{{ dataDetailUser?.following_count }}</b> following
              </div>
              <div class="profile-list">
                <div class="card">
                  <div class="card-body">
                    <div class="profile-user" v-for="(item, index) in dataFollowing" :key="index">
                      <router-link :to="{ name: 'Profile', params: { username: item?.username } }"
                        >@{{ item?.username }}</router-link
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        class="row justify-content-center"
        v-if="
          dataDetailUser?.is_private == 0 ||
          (dataDetailUser?.is_private == 1 && dataDetailUser?.following_status === 'following') ||
          dataDetailUser?.is_your_account
        "
      >
        <h3 v-if="dataDetailUser?.posts.length == 0" style="text-align: center">No posts</h3>
        <div class="col-md-4" v-for="post in dataDetailUser?.posts" :key="post.id">
          <div
            v-if="dataDetailUser?.is_your_account == 0"
            class="card-header d-flex align-items-center justify-content-between bg-transparent py-3"
          >
            <h6 class="mb-0">{{ dataDetailUser?.full_name }}</h6>
            <small class="text-muted">{{ dataDetailUser?.created_at }}</small>
          </div>
          <div class="card mb-4">
            <div class="card-body">
              <div class="card-images mb-2">
                <img
                  v-for="att in post.attachments"
                  :key="att.id"
                  :src="'/' + att.storage_path"
                  alt="image"
                  class="w-100"
                />
              </div>
              <p class="mb-0 text-muted">
                <b v-if="dataDetailUser?.is_your_account == 0" style="color: black">{{
                  dataDetailUser?.username
                }}</b>
                {{ post.caption }}
              </p>
              <button v-if="dataDetailUser?.is_your_account" @click.prevent="deletePost(post.id)">
                delete post
              </button>
            </div>
          </div>
        </div>
      </div>
      <div
        v-if="
          (dataDetailUser?.is_private === 1 &&
            dataDetailUser?.following_status === 'not-following' &&
            !dataDetailUser?.is_your_account) ||
          (dataDetailUser?.is_private === 1 &&
            dataDetailUser?.following_status === 'requested' &&
            !dataDetailUser?.is_your_account)
        "
        class="card py-4"
      >
        <div class="card-body text-center">&#128274; This account is private</div>
      </div>
    </div>
  </main>
</template>