import tailwindcss from '@tailwindcss/vite'

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  /**
   * @memo App work under frame
   * Nuxt DevTools: Failed to check parent window
   * SecurityError: Failed to read a named property '__NUXT_DEVTOOLS_DISABLE__' from 'Window'
   */
  devtools: { enabled: false },

  modules: [
    '@bitrix24/b24ui-nuxt',
    `@bitrix24/b24jssdk-nuxt`,
    '@nuxt/eslint'
  ],

  colorMode: {
    preference: 'light',
    fallback: 'light',
    classSuffix: '',
    storageKey: 'nuxt-color-mode',
    disableTransition: true
  },

  css: ['~/assets/css/main.css'],

  //ssr: false,

  vite: {
    server: {
      // allow incoming requests from this host
      allowedHosts: [
        'simple-firm-possum.ngrok-free.app',
        'dotingly-prospering-hawfinch.cloudpub.ru'
      ],
      // and don't forget CORS, if needed:
      cors: true
    },
    plugins: [
      tailwindcss()
    ]
  },

  future: {
    compatibilityVersion: 4
  },

  compatibilityDate: '2024-11-27'
})