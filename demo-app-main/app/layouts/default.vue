<script setup lang="ts">
import { B24Button } from '#components';
import Bitrix24Icon from '@bitrix24/b24icons-vue/common-service/Bitrix24Icon'
import MoneyIcon from '@bitrix24/b24icons-vue/outline/MoneyIcon'
import RecordVideoIcon from '@bitrix24/b24icons-vue/main/RecordVideoIcon'
import SmartProcessIcon from '@bitrix24/b24icons-vue/main/SmartProcessIcon'
import SunIcon from '@bitrix24/b24icons-vue/main/SunIcon'
import MoonIcon from '@bitrix24/b24icons-vue/main/MoonIcon'
import { initializeB24Frame, useB24Helper, LoadDataType } from '@bitrix24/b24jssdk'
const { initB24Helper, getB24Helper } = useB24Helper()

export interface ExampleProps {
  title?: string
}

const $b24 = await initializeB24Frame();

const confetti = useConfetti()

const isLoad = ref(false)
const profile = ref({})
const balance = ref(null)

onMounted(async () => {
  fetch('https://ai.app.ipgpromo.ru/balance?member_id=362c1663e59da74887e85513efa10a6a')
  .then(response => {
    if (!response.ok) throw new Error('Ошибка сети');
    return response.json(); // Преобразуем в JSON
  })
  .then(data => {
    balance.value = data
    //console.log('Данные:', data);
  })
  .catch(error => {
    console.error('Ошибка:', error);
  });

  initB24Helper($b24, [LoadDataType.Profile])  
  .then(data => {
    //console.log(data);
    profile.value = getB24Helper().profileInfo.data
    isLoad.value = true
  })
  .catch(error => {
    console.error('Ошибка:', error);
  });
})
</script>

<template>
  <header class="bg-white">
    <div class="flex justify-between items-center">
      <!-- Left side: Logo and app info -->
      <NuxtLink to="/" class="flex items-center gap-4">
        <img src="/logo.png" alt="День Х" class="h-20 my-1 ml-4">
        <div>
          <h1 class="font-semibold text-h3">
            День <span class="text-blue-500">X</span>
          </h1>
          <p class="text-base-600 text-xs">Поздравление с праздниками</p>
        </div>
      </NuxtLink>

      <!-- Right side: Instructions and support -->
      <div class="flex items-center gap-6 mr-4">
        <B24Button
          to="https://vkvideo.ru/playlist/-24162245_45684532"
          :icon="RecordVideoIcon"
          color="link"
          target="_blank"
          class="normal-case text-lg font-regular text-base-600"
          label="Инструкция"
        />
        <div class="flex items-center gap-4 bg-support px-6 py-3 rounded-sm">
          <div>
            <p class="text-base-600">Тех. поддержка</p>
            <NuxtLink href="mailto:ka@ipgpromo.ru" target="_blank" class="text-[#3BC8F5]">ka@ipgpromo.ru</NuxtLink>
          </div>
          <support/>
        </div>
      </div>
    </div>
  </header>
  <B24Container class="my-6 bg-white py-4 rounded-xl">
    <slot />
  </B24Container>
</template> 

<style>
    .gap-16-cust {
        column-gap: 16px;
    }
    .gap-40-cust {
        column-gap: 40px;
    }
    .text-h3 {
        font-size: 20px;
    }
    .blue-530 {
        color: #3BC8F5;
    }
    
    .asistents-and-bots {
        display: block;
        border-bottom: 4px solid transparent;
        padding: 8px 0;
    }
    .border-bottom {
        border-bottom-color:#3BC8F5;
    }
    .right-border {
        border-right: 1px solid #E8E8E8;
    }
    .bg-support {
        background-color: #3BC8F512;
    }
    @media(max-width: 1390px) {
        header .flex.flex-wrap {
            justify-content: center;
        }
        .gap-40-cust {
            row-gap: 10px;
        }
    }
</style>