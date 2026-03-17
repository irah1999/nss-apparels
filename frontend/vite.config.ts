import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    react(),
    tailwindcss()
  ],
  server: {
    proxy: {
      '/api': 'http://localhost:8080',
      '/uploads': 'http://localhost:8080',
      '/products': 'http://localhost:8080',
      '/product-detail': 'http://localhost:8080',
      '/contact': 'http://localhost:8080',
      '/login': 'http://localhost:8080',
    }
  }
})
