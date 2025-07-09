<script setup lang="ts">
import Title from '../ui/atoms/Title.vue';
import QuizCard from './QuizCard.vue';

const quizzes = [
    {
        image: '/example_quiz1.png',
        title: 'General Knowledge #1',
        linkTo: '/quiz/1'
    },
    {
        image: '/example_quiz2.png',
        title: 'History of France',
        linkTo: '/quiz/2'
    },
    {
        image: '/example_quiz3.png',
        title: 'Science & Nature',
        linkTo: '/quiz/3'
    }
]
</script>

<template>
    <section>
        <Title :level="1" class-name="mb-7">Featured Quizzes</Title>
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
