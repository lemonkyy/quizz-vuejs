<script setup lang="ts">
import Button from '@/components/ui/atoms/Button.vue';
import Input from '@/components/ui/atoms/Input.vue';
import Select from '@/components/ui/atoms/Select.vue';
import Checkbox from '@/components/ui/atoms/Checkbox.vue';
import Radio from '@/components/ui/atoms/Radio.vue';
import Title from '@/components/ui/atoms/Title.vue';
import { ref } from 'vue';

const selectOptions = [
  { value: 'option1', text: 'Option 1' },
  { value: 'option2', text: 'Option 2' },
  { value: 'option3', text: 'Option 3' },
];

let input1 = ref<string>('');
let input2 = ref<string>('');
let input3 = ref<string>('');

let select1 = ref<string>('option1');
let select2 = ref<string>('');
let select3 = ref<string>('');

let checkbox1 = ref<boolean>(false);
let checkbox2 = ref<boolean>(false);
let checkbox3 = ref<boolean>(false);

let radio = ref<string>('');

function handleClick() {
  console.log('Button clicked');
}

</script>

<template>
  <div class="p-6 space-y-12">
    <Title :level="1"> UI Preview </Title>
    <div class="flex flex-row gap-8 flex-wrap">
      <Button theme="primary" type="button" rounded="sm"> Primary button </Button>
      <Button theme="primary" type="button" transparent> Primary button </Button>
      <Button theme="primary" type="button" rounded="md"> Primary button </Button>
      <Button theme="primary" type="button" rounded="lg"> <RouterLink to="/">button</RouterLink> </Button>
    </div>
    <Button theme="secondary" type="button" rounded="sm" xl> Secondary button </Button>
    <div class="flex flex-row gap-8 flex-wrap">
      <Input id="input1" label="primary input" theme="primary" type="text" placeholder="Enter text" v-model="input1" rounded="sm" />
      <Input id="input2" theme="primary" type="text" placeholder="Enter text" dashedBorder v-model="input2" rounded="lg" />
      <Input id="input3" label="secondary input" theme="secondary" type="text" placeholder="Enter text" v-model="input3" withoutBorder rounded="sm" />
    </div>
    <div class="flex flex-row gap-8 flex-wrap">
      <Select v-model="select1" label="primary select" theme="primary" id="select1" withoutBorder>
        <option v-for="option in selectOptions" :key="option.value" :value="option.value">
          {{ option.text }}
        </option>
      </Select>
      <Select v-model="select2" label="primary select" theme="primary" id="select3" dashedBorder>
        <option v-for="option in selectOptions" :key="option.value" :value="option.value">
          {{ option.text }}
        </option>
      </Select>
      <Select v-model="select3" label="secondary select" theme="secondary" id="select4">
        <option v-for="option in selectOptions" :key="option.value" :value="option.value">
          {{ option.text }}
        </option>
      </Select>
    </div>
    <div class="flex flex-row gap-8 flex-wrap">
      <Checkbox id="checkbox1" label="primary checkbox" theme="primary" v-model="checkbox1" />
      <Checkbox id="checkbox2" label="" v-model="checkbox2" />
      <Checkbox id="checkbox3" label="primary checkbox" theme="secondary" v-model="checkbox3" checkboxLeft />
    </div>
    <div class="flex flex-col gap-8 flex-wrap">
      <Radio id="radio1" label="option 1" theme="primary" v-model="radio" name="radio" value="a" />
      <Radio id="radio2" label="option 2" v-model="radio" name="radio" value="b" />
      <Radio id="radio3" label="option 3" theme="secondary" v-model="radio" radioLeft name="radio" value="c" />
    </div>
  </div>
</template>
