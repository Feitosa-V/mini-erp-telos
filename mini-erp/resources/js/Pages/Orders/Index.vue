<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';

// Recebendo dados do backend
defineProps({
    orders: Array,
    suppliers: Array,
    products: Array,
});

// Controle do modal
const showModal = ref(false);
const showDeleteModal = ref(false);
const selectedOrderId = ref(null);

// Formulário de criação de pedido
const form = ref({
    supplier_id: '',
    products: [],
    notes: '',
});

// Adicionar novo produto ao pedido
const addProduct = () => {
    form.value.products.push({ id: '', quantity: 1, unit_price: 0 });
};

// Remover produto do pedido
const removeProduct = (index) => {
    form.value.products.splice(index, 1);
};

// Atualizar preço unitário ao selecionar um produto
const updateUnitPrice = (index) => {
    const selectedProduct = form.value.products[index];
    const product = products.find(p => p.id === parseInt(selectedProduct.id));
    if (product) {
        selectedProduct.unit_price = product.price;
    }
};

// Calcular o valor total do pedido
const totalValue = computed(() => {
    return form.value.products.reduce((total, product) => {
        return total + (product.quantity * product.unit_price);
    }, 0);
});

// Criar pedido
const createOrder = () => {
    axios.post('/api/orders', { ...form.value, total_value: totalValue.value }, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    }).then(() => {
        showModal.value = false;
        form.value = { supplier_id: '', products: [], notes: '' };
        router.reload();
    });
};

// Abrir modal de exclusão
const confirmDelete = (id) => {
    selectedOrderId.value = id;
    showDeleteModal.value = true;
};

// Deletar pedido
const deleteOrder = () => {
    axios.delete(`/api/orders/${selectedOrderId.value}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    }).then(() => {
        showDeleteModal.value = false;
        router.reload();
    });
};
</script>

<template>
    <Head title="Pedidos" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pedidos</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between mb-4">
                        <h1 class="text-2xl font-bold">Lista de Pedidos</h1>
                        <button @click="showModal = true" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Novo Pedido</button>
                    </div>
                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-4 py-2">Fornecedor</th>
                                <th class="border px-4 py-2">Data</th>
                                <th class="border px-4 py-2">Total</th>
                                <th class="border px-4 py-2">Status</th>
                                <th class="border px-4 py-2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="order in orders" :key="order.id">
                                <td class="border px-4 py-2">{{ order.supplier.name }}</td>
                                <td class="border px-4 py-2">{{ order.order_date }}</td>
                                <td class="border px-4 py-2">R$ {{ order.total_value }}</td>
                                <td class="border px-4 py-2">{{ order.status }}</td>
                                <td class="border px-4 py-2">
                                    <button @click="confirmDelete(order.id)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">Excluir</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal de criação -->
        <Modal :show="showModal" @close="showModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Novo Pedido</h2>
                <form @submit.prevent="createOrder" class="mt-4">
                    <select v-model="form.supplier_id" class="block w-full mt-1 border-gray-300 rounded-lg">
                        <option value="" disabled>Selecione um fornecedor</option>
                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">{{ supplier.name }}</option>
                    </select>
                    <h3 class="mt-4">Produtos:</h3>
                    <div v-for="(product, index) in form.products" :key="index" class="mt-2">
                        <select v-model="product.id" @change="updateUnitPrice(index)" class="block w-full border-gray-300 rounded-lg">
                            <option value="" disabled>Selecione um produto</option>
                            <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
                        </select>
                        <label for="">Quantidade:</label>
                        <TextInput v-model="product.quantity" type="number" placeholder="Quantidade" class="mt-2 block w-full" required />
                        <label for="">Preço Unitário:</label>
                        <TextInput v-model="product.unit_price" type="number" placeholder="Preço Unitário" class="mt-2 block w-full" required disabled />
                        <button @click="removeProduct(index)" class="bg-red-500 text-white px-2 py-1 rounded mt-2">Remover</button>
                    </div>
                    <button @click="addProduct" class="bg-green-500 text-white px-2 py-1 rounded mt-2">Adicionar Produto</button>
                    <p class="mt-4 font-bold">Total: R$ {{ totalValue }}</p>
                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="showModal = false">Cancelar</SecondaryButton>
                        <PrimaryButton type="submit" class="ml-3">Salvar</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
