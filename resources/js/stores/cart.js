import { defineStore } from 'pinia';
import { ref, computed, watch } from 'vue';

export const useCartStore = defineStore('cart', () => {
    // 💡 Инициализация: Пытаемся достать сохраненную корзину из LocalStorage
    const localCart = localStorage.getItem('bookstore_cart');
    const items = ref(localCart ? JSON.parse(localCart) : []);

    // 💡 Watcher: Следит за корзиной и автоматически сохраняет её при любых изменениях
    watch(items, (newItems) => {
        localStorage.setItem('bookstore_cart', JSON.stringify(newItems));
    }, { deep: true }); // deep: true нужен для отслеживания изменений внутри объектов (изменение quantity)

    // Вычисляемые свойства (Геттеры Pinia)
    const cartCount = computed(() => {
        return items.value.reduce((sum, item) => sum + item.quantity, 0);
    });

    const cartTotal = computed(() => {
        return items.value.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    });

    // Действия (Экшены Pinia)
    const addToCart = (book) => {
        const existingItem = items.value.find(i => i.id === book.id);
        if (existingItem) {
            if (existingItem.quantity < book.stock) {
                existingItem.quantity++;
            } else {
                alert('Нельзя добавить больше, чем есть на складе!');
            }
        } else {
            items.value.push({
                id: book.id,
                title: book.title,
                price: book.price,
                stock: book.stock,
                quantity: 1
            });
        }
    };

    const removeFromCart = (id) => {
        items.value = items.value.filter(item => item.id !== id);
    };

    const clearCart = () => {
        items.value = [];
    };

    return {
        items,
        cartCount,
        cartTotal,
        addToCart,
        removeFromCart,
        clearCart
    };
});
