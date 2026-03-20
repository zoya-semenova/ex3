<script setup lang="ts">
import HrAutomationIcon from '@bitrix24/b24icons-vue/main/HrAutomationIcon'
import UserCompanyIcon from '@bitrix24/b24icons-vue/common-b24/UserCompanyIcon'
import PersonIcon from '@bitrix24/b24icons-vue/main/PersonIcon'
import CompanyIcon from '@bitrix24/b24icons-vue/outline/CompanyIcon'
import * as z from 'zod'
import type { FormSubmitEvent } from '@bitrix24/b24ui-nuxt'
import { onMounted, onUnmounted } from 'vue'
import {
	initializeB24Frame,
	B24Frame,
} from '@bitrix24/b24jssdk'

const confetti = useConfetti()
let $b24: B24Frame

onMounted(async () => {
	try
	{
		$b24 = await initializeB24Frame()
    confetti.fire()
    $b24.installFinish()
	}
	catch (error)
	{
		console.error(error)
	}
})

onUnmounted(() => {
	$b24?.destroy()
})

useHead({
  title: 'Install'
})

export interface ExampleProps {
  title?: string
}

withDefaults(defineProps<ExampleProps>(), {
  title: 'Install'
})

definePageMeta({
  layout: 'full'
})
</script>

<template>
  <B24Alert
    color="success"
    title="Установка успешно завершена!"
  />
</template>