<script setup lang="ts">
useHead({
  bodyAttrs: {
    class: 'text-base-master dark:text-base-150 bg-base-50 dark:bg-base-dark font-b24-system antialiased',
  }
})
</script>

<template>
  <ClientOnly>
    <NuxtLayout>
      <B24App>
        <NuxtPage />
      </B24App>
    </NuxtLayout>
  </ClientOnly>
</template>