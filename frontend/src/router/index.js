import CreatePost from '@/pages/CreatePost.vue'
import Homepage from '@/pages/Homepage.vue'
import LayoutDashboard from '@/pages/LayoutDashboard.vue'
import Login from '@/pages/Login.vue'
import Profile from '@/pages/Profile.vue'
import Register from '@/pages/Register.vue'
import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Login',
      component: Login,
    },
    {
      path: '/register',
      name: 'Register',
      component: Register
    },
    {
      path: '/dashboard',
      name: 'LayoutDashboard',
      component: LayoutDashboard,
      children: [
        {
          path: 'homepage',
          name: 'Homepage',
          component: Homepage 
        },
        {
          path: 'profile/:username',
          name: 'Profile',
          component: Profile
        },
        {
          path: 'create-post',
          name: 'CreatePost',
          component: CreatePost,
        }
      ]
    }
  ],
})

export default router
