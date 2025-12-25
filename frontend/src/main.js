import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

const app = createApp(App)

app.use(router)
import '../public/bootstrap.css'
import '../public/style.css'

app.mount('#app')
