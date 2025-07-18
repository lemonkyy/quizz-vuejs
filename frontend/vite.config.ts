import { sentryVitePlugin } from "@sentry/vite-plugin";
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'
import { fileURLToPath, URL } from 'node:url'

const allowedHosts = process.env.VITE_ALLOWED_HOSTS?.split(',').map(h => h.trim()) || [];

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue(), tailwindcss(), sentryVitePlugin({
    org: process.env.VITE_SENTRY_ORG,
    project: "javascript-vue"
  })],

  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },

  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    watch: {
      usePolling: true,
    },
    hmr: {
      protocol: 'ws',
      host: 'localhost',
      port: 5173,
      clientPort: 8888,
    },
    allowedHosts,
  },

  build: {
    sourcemap: true
  }
})
