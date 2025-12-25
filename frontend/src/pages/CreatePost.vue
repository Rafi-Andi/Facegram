<script setup>
import router from '@/router'
import url from '@/router/url'
import axios from 'axios'
import { onMounted, ref } from 'vue'

const images = ref(null)
const caption = ref('')

const isLoading = ref(false)
const createPost = async () => {
  try {
    isLoading.value = true
    const formData = new FormData()
    formData.append('caption', caption.value)
    const files = images.value.files

    for (let i = 0; i < files.length; i++) {
      formData.append('attachments[]', files[i])
    }
    const response = await axios.post(`${url}/api/v1/posts`, formData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`,
      },
    })

    alert(response.data.message)
    router.push({name: 'Profile', params: {username: localStorage.getItem('username')}})
  } catch (error) {
    alert(error.response.data.message)
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
    document.title = 'Create Post'
})
</script>

<template>
  <main class="mt-5">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-md-5">
          <div class="card">
            <div
              class="card-header d-flex align-items-center justify-content-between bg-transparent py-3"
            >
              <h5 class="mb-0">Create new post</h5>
            </div>
            <div class="card-body">
              <form @submit.prevent="createPost()">
                <div class="mb-2">
                  <label for="caption">Caption</label>
                  <textarea
                    class="form-control"
                    name="caption"
                    id="caption"
                    cols="30"
                    rows="3"
                    v-model="caption"
                  ></textarea>
                </div>

                <div class="mb-3">
                  <label for="attachments">Image(s)</label>
                  <input
                    ref="images"
                    type="file"
                    class="form-control"
                    id="attachments"
                    name="attachments"
                    multiple
                  />
                </div>

                <button type="submit" class="btn btn-primary w-100">{{ isLoading ? 'Loading..' : 'Share' }}</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>
