<script setup lang="ts">
import { ref, computed } from 'vue';
import Title from '@/components/ui/atoms/Title.vue';

interface QuizHistoryItem {
  id: string;
  quizName: string;
  date: string;
  score: number;
  totalQuestions: number;
}

const quizHistory = ref<QuizHistoryItem[]>([
  {
    id: '1',
    quizName: 'JavaScript Fundamentals',
    date: '2024-07-18',
    score: 8,
    totalQuestions: 10
  },
  {
    id: '2',
    quizName: 'Vue.js Advanced Concepts',
    date: '2024-07-17',
    score: 6,
    totalQuestions: 8
  },
  {
    id: '3',
    quizName: 'CSS Grid & Flexbox',
    date: '2024-07-16',
    score: 9,
    totalQuestions: 10
  },
  {
    id: '4',
    quizName: 'React Hooks Deep Dive',
    date: '2024-07-15',
    score: 7,
    totalQuestions: 12
  },
  {
    id: '5',
    quizName: 'Node.js Basics',
    date: '2024-07-14',
    score: 5,
    totalQuestions: 8
  },
  {
    id: '6',
    quizName: 'Database Design Principles',
    date: '2024-07-13',
    score: 10,
    totalQuestions: 10
  }
]);

const averageScore = computed(() => {
  const total = quizHistory.value.reduce((sum, quiz) => sum + (quiz.score / quiz.totalQuestions) * 100, 0);
  return Math.round(total / quizHistory.value.length);
});

const performanceData = computed(() => {
  return quizHistory.value
    .map(quiz => ({
      date: quiz.date,
      percentage: Math.round((quiz.score / quiz.totalQuestions) * 100)
    }))
    .sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime());
});

const getDifficultyColor = (difficulty: string) => {
  switch (difficulty) {
    case 'Easy': return 'text-green-600 bg-green-100';
    case 'Medium': return 'text-yellow-600 bg-yellow-100';
    case 'Hard': return 'text-red-600 bg-red-100';
    default: return 'text-gray-600 bg-gray-100';
  }
};

const getScoreColor = (score: number, total: number) => {
  const percentage = (score / total) * 100;
  if (percentage >= 80) return 'text-green-600';
  if (percentage >= 60) return 'text-yellow-600';
  return 'text-red-600';
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
};
</script>

<template>
  <div class="max-w-6xl mx-auto p-6">
    <Title :level="1" class="mb-8">Quiz History</Title>
    
    <!-- Insights Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <!-- Average Score Card -->
      <div class="bg-white rounded-lg shadow-md p-6 border">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Average Score</h3>
        <div class="flex items-center">
          <span class="text-3xl font-bold text-blue-600">{{ averageScore }}%</span>
          <span class="ml-2 text-gray-500">across {{ quizHistory.length }} quizzes</span>
        </div>
      </div>
      
      <!-- Performance Curve Card -->
      <div class="bg-white rounded-lg shadow-md p-6 border">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Performance Curve</h3>
        <div class="h-20 flex items-end space-x-1">
          <div 
            v-for="(data, index) in performanceData" 
            :key="index"
            class="bg-blue-500 rounded-t min-w-[8px] flex-1"
            :style="{ height: `${(data.percentage / 100) * 80}px` }"
            :title="`${formatDate(data.date)}: ${data.percentage}%`"
          ></div>
        </div>
        <div class="text-xs text-gray-500 mt-2">Recent performance trend</div>
      </div>
    </div>

    <!-- Quiz History Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden border">
      <div class="px-6 py-4 border-b bg-gray-50">
        <h2 class="text-xl font-semibold text-gray-800">Quiz History</h2>
      </div>
      
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quiz Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difficulty</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="quiz in quizHistory" :key="quiz.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ quiz.quizName }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ formatDate(quiz.date) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium" :class="getScoreColor(quiz.score, quiz.totalQuestions)">
                  {{ quiz.score }}/{{ quiz.totalQuestions }}
                  <span class="text-xs text-gray-500 ml-1">
                    ({{ Math.round((quiz.score / quiz.totalQuestions) * 100) }}%)
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ quiz.duration }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getDifficultyColor(quiz.difficulty)">
                  {{ quiz.difficulty }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ quiz.category }}</div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>