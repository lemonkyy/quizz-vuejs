<script setup lang="ts">
defineProps({
    linksTo: {type: String, required: true}
})
</script>

<template>
    <li><router-link :to="linksTo" class="hover:underline text-menu-text font-bold"><slot /></router-link></li>
</template>
