<script setup lang="ts">
import HrAutomationIcon from '@bitrix24/b24icons-vue/main/HrAutomationIcon'
import UserCompanyIcon from '@bitrix24/b24icons-vue/common-b24/UserCompanyIcon'
import PersonIcon from '@bitrix24/b24icons-vue/main/PersonIcon'
import CompanyIcon from '@bitrix24/b24icons-vue/outline/CompanyIcon'
import * as z from 'zod'
import type { FormSubmitEvent } from '@bitrix24/b24ui-nuxt'

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


const items = [
  {
    label: 'Дни рождения',
    icon: PersonIcon,
    slot: 's1'
  },
  {
    label: 'Даты принятия на работу',
    icon: HrAutomationIcon,
    slot: 's2'
  },
  {
    label: 'Другие праздники компании',
    icon: CompanyIcon,
    slot: 's3'
  },
]
const schema = z.object({
  input: z.string().min(10),
  inputNumber: z.number().min(10),
  inputMenu: z.any().refine(option => option?.value === 'option-2', {
    message: 'Select Option 2'
  }),
  inputMenuMultiple: z.any().refine(values => !!values?.find((option: any) => option.value === 'option-2'), {
    message: 'Include Option 2'
  }),
  textarea: z.string().min(10),
  select: z.string().refine(value => value === 'option-2', {
    message: 'Select Option 2'
  }),
  selectMenu: z.any().refine(option => option?.value === 'option-2', {
    message: 'Select Option 2'
  }),
  selectMenuMultiple: z.any().refine(values => !!values?.find((option: any) => option.value === 'option-2'), {
    message: 'Include Option 2'
  }),
  chat: z.boolean().refine(value => value === true, {

  }),
  livefeed: z.boolean().refine(value => value === true, {

  }),
  radioGroup: z.string().refine(value => value === 'option-2', {
    message: 'Select Option 2'
  }),
  range: z.number().max(20, { message: 'Must be less than 20' }),
  timelivefeed: z.number().min(1).max(24)
})

type Schema = z.input<typeof schema>
const state = reactive<Partial<Schema>>({})

</script>

<template>
  <B24Tabs :items="items" size="lg" class="w-full" > 
    <template #s1="{ item }">
      <B24Form
      :state="state"
      class="space-y-4"
      @submit="onSubmit"
    >
      <B24FormField name="livefeed">
        <B24Switch v-model="state.livefeed" label="Отправлять поздравление в ленту компании" />
      </B24FormField>
      <div v-if="state.livefeed">
        <B24FormField name="livefeed" label="Текст поздравления">
          <B24Textarea v-model="state.textarea" placeholder="" />
        </B24FormField>
        <B24FormField name="livefeed" label="Время отправки поздравления">
          <B24InputNumber v-model="state.timelivefeed" defaultValue="24" min="1" max="24" />
        </B24FormField>
        <B24FormField name="livefeed" label="За сколько дней до события создавать поздравление">
          <B24InputNumber v-model="state.timelivefeed" defaultValue="0" min="0" max="30" />
        </B24FormField>
        <B24FormField name="livefeed" label="За сколько дней до события создавать поздравление">
          <B24InputNumber v-model="state.timelivefeed" defaultValue="0" min="0" max="30" />
        </B24FormField>
        <B24FormField name="livefeed" label="Кто будет видеть (отделы и пользователи)">
          <B24Input v-model="state.timelivefeed"  />
        </B24FormField>
      </div>
      <B24FormField name="chat">
        <B24Switch v-model="state.chat" label="Создавать чат" />
      </B24FormField>
      <div v-if="state.chat">
        <B24FormField name="text-chat" label="Текст для чата">
          <B24Textarea v-model="state.textarea" placeholder="" />
        </B24FormField>
      </div>
      </B24Form>
    </template>
    <template #s2="{ item }">
      <div class="">
      345
      </div>
    </template>
    <template #s3="{ item }">
      <div class="">
      678
      </div>
    </template>
  </B24Tabs>
</template>