<template>
    <div class="min-h-screen bg-slate-100 py-6 px-4 sm:px-6 lg:px-8 pb-32 md:pb-12">
        <Head title="Shop" />

        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-800 mb-1">📚 BookStore</h1>
                <h5 class="text-sm font-medium text-slate-500 uppercase tracking-wider">книжный интернет-магазин</h5>
            </div>


            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Сетка книг (занимает 3 колонки на десктопе) -->
                <div class="lg:col-span-3 flex flex-col justify-between">
                    <div v-if="books" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                        <div v-for="book in books" :key="book.id" class="bg-white rounded-xl shadow-md border border-slate-200 p-5 flex flex-col justify-between hover:shadow-lg hover:border-blue-300 transition-all duration-200">
                            <div>
                                <h3 class="text-base font-bold text-slate-900 mb-1 line-clamp-2 h-12">{{ book.title }}</h3>
                                <p class="text-xs text-slate-500 mb-4">Автор: {{ book.author }}</p>
                            </div>
                            <div class="mt-2">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-lg font-black text-blue-600">{{ book.price }} ₽</span>
                                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">В наличии: {{ book.stock }} шт</span>
                                </div>
                                <button
                                    @click="addToCart(book)"
                                    :disabled="book.stock <= 0"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition-colors disabled:bg-slate-300 disabled:text-slate-500"
                                >
                                    {{ book.stock > 0 ? 'В корзину' : 'Нет в наличии' }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Постраничная навигация (Пагинация) -->
                    <div class="flex justify-center items-center gap-4 mt-8 bg-white py-3 px-6 rounded-xl shadow-sm border border-slate-100 w-fit mx-auto">
                        <button
                            @click="changePage(pagination.current_page - 1)"
                            :disabled="pagination.current_page === 1"
                            class="px-3 py-1.5 rounded-lg border text-sm font-medium text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:hover:bg-white"
                        >
                            ← Назад
                        </button>
                        <span class="text-sm font-semibold text-slate-700">
                            Страница {{ pagination.current_page }} из {{ pagination.last_page }}
                        </span>
                        <button
                            @click="changePage(pagination.current_page + 1)"
                            :disabled="pagination.current_page === pagination.last_page"
                            class="px-3 py-1.5 rounded-lg border text-sm font-medium text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:hover:bg-white"
                        >
                            Вперед →
                        </button>
                    </div>
                </div>

                <!-- Блок корзины (Умный адаптив) -->
                <!-- На мобилках (до md): фиксированная плашка внизу экрана. На десктопе (от lg): блок справа -->
                <div class="fixed bottom-0 left-0 right-0 bg-white shadow-[0_-8px_30px_rgb(0,0,0,0.1)] border-t p-4 z-50 md:relative md:bottom-auto md:left-auto md:right-auto md:bg-white md:rounded-xl md:shadow-md md:border md:border-slate-200 md:p-5 h-fit md:sticky md:top-6">
                    <div class="flex justify-between items-center mb-3 md:block">
                        <h2 class="text-lg font-black text-slate-800 md:mb-4">🛒 Корзина ({{ cartCount }} шт)</h2>
                        <div class="text-right md:hidden">
                            <span class="text-xs text-slate-500 block">Итого:</span>
                            <span class="text-lg font-black text-blue-600">{{ cartTotal }} ₽</span>
                        </div>
                    </div>

                    <!-- Десктопный список товаров (скрыт на мобилках для компактности плашки) -->
                    <div class="hidden md:block max-h-60 overflow-y-auto mb-4 custom-scrollbar">
                        <div v-if="cart.length === 0" class="text-slate-400 text-xs py-4 text-center">
                            Корзина пуста
                        </div>
                        <div v-for="item in cart" :key="item.id" class="flex justify-between items-center mb-2 pb-2 border-b border-slate-50 text-xs">
                            <div class="max-w-[75%]">
                                <p class="font-semibold text-slate-700 truncate">{{ item.title }}</p>
                                <p class="text-[10px] text-slate-400">{{ item.price }} ₽ x {{ item.quantity }}</p>
                            </div>
                            <button @click="removeFromCart(item.id)" class="text-rose-500 hover:text-rose-700 font-bold text-base px-1">×</button>
                        </div>
                    </div>

                    <!-- Сводка и Кнопка заказа -->
                    <div>
                        <div class="hidden md:flex justify-between font-bold text-sm text-slate-800 mb-4 pt-2 border-t">
                            <span>Итого:</span>
                            <span class="text-blue-600 text-base">{{ cartTotal }} ₽</span>
                        </div>
                        <button
                            @click="checkout"
                            :disabled="cart.length === 0"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 text-white font-bold py-2.5 md:py-3 px-4 rounded-xl text-sm shadow-sm transition-colors"
                        >
                            {{ cart.length === 0 ? 'Добавьте книги' : 'Оформить заказ' }}
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { Head } from '@inertiajs/vue3';
import { storeToRefs } from 'pinia';
import { useCartStore } from '../stores/cart'; // 💡 Импортируем наш стор

// Инициализируем глобальное хранилище корзины
const cartStore = useCartStore();

// 💡 Важно: Извлекаем реактивные переменные через storeToRefs, чтобы не потерять реактивность
const { items: cart, cartCount, cartTotal } = storeToRefs(cartStore);
const { addToCart, removeFromCart, clearCart } = cartStore;

const books = ref([]);
const pagination = ref({ current_page: 1, last_page: 1 });

// Загрузка книг с учетом конкретной страницы
const fetchBooks = async (page = 1) => {
    try {
        const response = await axios.get(`/api/books?page=${page}`);

        // 💡 СИНХРОНИЗИРОВАНО: Проверяем ваш флаг success
        if (response.data && response.data.success) {

            // 💡 СИНХРОНИЗИРОВАНО: Массив книг теперь лежит в response.data.catalog.data
            books.value = response.data.catalog.data;

            // 💡 СИНХРОНИЗИРОВАНО: Метаданные пагинации берем из объекта catalog
            pagination.value = {
                current_page: response.data.catalog.current_page,
                last_page: response.data.catalog.last_page
            };
        }
    } catch (error) {
        console.error("Ошибка при получении каталога книг:", error);
    }
};


const changePage = (page) => {
    if (page >= 1 && page <= pagination.value.last_page) {
        fetchBooks(page);
    }
};

const checkout = async () => {
    try {
        const orderData = {
            items: cart.value.map(item => ({
                book_id: item.id,
                quantity: item.quantity
            }))
        };

        const response = await axios.post('/api/orders', orderData);

        if (response.data && response.data.success) {
            alert('Заказ успешно оформлен!');
            clearCart(); // 💡 Вызываем экшен очистки корзины из стора
            await fetchBooks(pagination.value.current_page);
        }
    } catch (error) {
        alert('Произошла ошибка при создании заказа.');
    }
};

onMounted(() => {
    fetchBooks();
});
</script>


<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 4px;
}
</style>
