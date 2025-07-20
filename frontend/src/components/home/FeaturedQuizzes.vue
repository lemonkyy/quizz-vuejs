<script setup lang="ts">
import Title from '../ui/atoms/Title.vue';
import QuizCard from './QuizCard.vue';

const quizzes = [
    {
        image: '/example_quiz1.png',
        title: 'Harry Potter Trivia',
        linkTo: { name: 'Create', query: { prompt: 'Harry Potter Trivia' } }
    },
    {
        image: '/example_quiz2.png',
        title: 'History of France',
        linkTo: { name: 'Create', query: { prompt: 'History of France' } }
    },
    {
        image: '/example_quiz3.png',
        title: 'Science & Nature',
        linkTo: { name: 'Create', query: { prompt: 'Science & Nature' } }
    }
]
</script>

<template>
    <section>
        <Title :level="1" class="mb-7">Featured Quizzes</Title>
        <div class="flex flex-row flex-wrap gap-3 items-center">
            <QuizCard 
                v-for="(quiz, index) in quizzes" 
                :key="index"
                :image="quiz.image"
                :title="quiz.title"
                :linksTo="quiz.linkTo"
            />
        </div>
    </section>
</template>
