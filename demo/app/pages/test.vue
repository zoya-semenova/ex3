<script setup lang="ts">
useHead({
  title: 'Test'
})

export interface ExampleProps {
  title?: string
}

withDefaults(defineProps<ExampleProps>(), {
  title: 'Heads up!'
})

definePageMeta({
  layout: 'default'
})
</script>

<template>
    <B24Alert
      class="mt-3 mb-3"
      color="ai"
      title="Баланс"
      :avatar="{ icon: MoneyIcon }"
      close
    >
      <template #description >
          <div v-if="balance">
            {{ balance.token }}
          </div>
          <div v-else>
            <B24Skeleton class="h-4 w-[100px] bg-ai-400" />
          </div>
          <B24Button label="Тест" to="test" color="success" size="sm" class="mr-3 mt-3" />
          <B24Button label="Вспышка" @click="confetti.fire()" color="collab" size="sm" class="mr-3 mt-3" />
          <B24Slideover class="mr-3 mt-3"
            :title="title"
          >
            <B24Button label="Open" color="primary" depth="dark" size="sm"/>

            <template #body>
              <Placeholder class="h-full" />
            </template>
          </B24Slideover>
      </template>
    </B24Alert>
    <div class="flex items-center gap-4">
        <template v-if="isLoad">
          <img 
            :src="profile.photo" 
            :alt="`${profile.name} ${profile.lastName}`"
            class="h-12 w-12 rounded-full object-cover"
          />
          <div class="grid gap-2">
            <div class="text-base font-medium">
              {{ profile.name }} {{ profile.lastName }}
            </div>
            <div class="text-sm text-gray-500">
              {{ profile.isAdmin ? 'Administrator' : 'User' }}
            </div>
          </div>
        </template>
        <template v-else>
          <B24Skeleton class="h-12 w-12 rounded-full" />
          <div class="grid gap-2">
            <B24Skeleton class="h-4 w-[250px]" />
            <B24Skeleton class="h-4 w-[200px]" />
          </div>
        </template>
    </div>
</template>
