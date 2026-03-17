import { defineConfig, loadEnv } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')
  const target = (env.VITE_API_BASE_URL || 'http://localhost:8080').replace(/\/$/, '')

  return {
    plugins: [
      react(),
      tailwindcss()
    ],
    server: {
      proxy: {
        '/api': target,
        '/uploads': target,
        '/products': target,
        '/product-detail': target,
        '/contact': target,
        '/login': target,
      }
    }
  }
})
