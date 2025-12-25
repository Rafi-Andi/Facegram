<script setup>
import url from '@/router/url'
import axios from 'axios'
import { onMounted, ref } from 'vue'

const dataPosts = ref([])
const page = ref(0)
const size = ref(10)
const loading = ref(false)
const hasMore = ref(true)
const username = localStorage.getItem('username')

const fetchDataPosts = async () => {
  if (loading.value || !hasMore.value) return
  loading.value = true
  const response = await axios.get(`${url}/api/v1/posts?page=${page.value}&size=${size.value}`, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`,
    },
  })

  const posts = response.data.posts

  dataPosts.value.push(...posts.data)
  if (page.value >= posts.last_page) {
    hasMore.value = false
  } else {
    page.value++
  }

  loading.value = false
}

const followRequest = ref(null)

const fetchFollowRequest = async () => {
  const response = await axios.get(`${url}/api/v1/users/${username}/followers`, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`,
    },
  })

  const data = response.data.followers
  followRequest.value = data.filter((d) => d.is_requested === true)
  console.log(response)
}

const acceptFollowRequest = async (username) => {
  try {
    const response = await axios.put(`${url}/api/v1/users/${username}/accept`, '', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    })
    const findIndex = followRequest.value.findIndex((f) => f.username === username)
    followRequest.value.splice(findIndex, 1)
    console.log(response)
    alert('Success Accept')
  } catch (error) {
    alert(error.response.data.message)
    console.log(error)
  }
}

const dataExploreUser = ref(null)

const fetchExplorUser = async () => {
  const response = await axios.get(`${url}/api/v1/users`, {
    headers: {
      Authorization: `Bearer ${localStorage.getItem('token')}`,
    },
  })

  dataExploreUser.value = response.data.users
  console.log(response)
  console.log(dataExploreUser.value)
}

const loadMoreRef = ref(null)

onMounted(() => {
  fetchDataPosts()

  const observer = new IntersectionObserver(
    (entries) => {
      if (entries[0].isIntersecting) {
        size.value = 7
        fetchDataPosts()
      }
    },
    {
      threshold: 1,
    },
  )

  observer.observe(loadMoreRef.value)
})

onMounted(() => {
  fetchFollowRequest()
  fetchExplorUser()
})

onMounted(() => {
    document.title = 'Home Page'
})
</script>

<template>
  <main class="mt-5">
    <div class="container py-5">
      <div class="row justify-content-between">
        <div class="col-md-8">
          <h5 class="mb-3">News Feed</h5>
          <div class="card mb-4" v-for="(item, index) in dataPosts" :key="item?.id">
            <div
              class="card-header d-flex align-items-center justify-content-between bg-transparent py-3"
            >
              <h6 class="mb-0">{{ item?.user?.full_name }}</h6>
              <small class="text-muted">{{ item?.user?.created_at }}</small>
            </div>
            <div class="card-body">
              <div class="card-images mb-2">
                <img
                  v-for="(image, index) in item?.attachments"
                  :key="index"
                  :src="'/' + image?.storage_path"
                  :alt="image?.storage_path"
                  class="w-100"
                />
              </div>
              <p class="mb-0 text-muted">
                <b
                  ><a href="user-profile.html">{{ item?.user?.username }}</a></b
                >
                {{ item?.caption }}
              </p>
            </div>
          </div>
          <div ref="loadMoreRef" class="text-center py-3">
            <span v-if="loading">Loading...</span>
            <span v-else-if="!hasMore">Tidak ada post lagi</span>
          </div>
        </div>
        <div class="col-md-4">
          <div class="request-follow mb-4">
            <h6 class="mb-3">Follow Requests</h6>
            <div class="request-follow-list">
              <div class="card mb-2">
                <div v-if="followRequest?.length === 0">
                  <p style="text-align: center">Tidak ada follow request</p>
                </div>
                <div
                  v-for="(item, index) in followRequest"
                  :key="index"
                  class="card-body d-flex align-items-center justify-content-between p-2"
                >
                  <a href="user-profile-private.html">@{{ item?.username }}</a>
                  <p
                    style="cursor: pointer"
                    @click="acceptFollowRequest(item?.username)"
                    href=""
                    class="btn btn-primary btn-sm"
                  >
                    Confirm
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="explore-people">
            <h6 class="mb-3">Explore People</h6>
            <div class="explore-people-list">
              <div class="card mb-2" v-for="(item, index) in dataExploreUser" :key="index">
                <div class="card-body p-2">
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
  </main>
</template>
