<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

// Recebendo produtos do backend
defineProps({
    products: Array,
    suppliers: Array,
});

// Controle dos modais
const showModal = ref(false);
const showDeleteModal = ref(false);
const selectedProductId = ref(null);

// Formulário de criação de produto
const form = ref({
    supplier_id: '',
    reference: '',
    name: '',
    color: '',
    price: '',
});

// Criar produto
const createProduct = async () => {
    try {
        await axios.post('/api/products', form.value, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                Accept: 'application/json',
            },
        });

        showModal.value = false;
        form.value = { supplier_id: '',reference: '',name: '', color: '', price: '' };
        router.reload(); // Recarrega os dados
    } catch (error) {
        console.error('Erro ao criar produto:', error.response?.data || error.message);
    }
};

// Abrir modal de exclusão
const confirmDelete = (id) => {
    selectedProductId.value = id;
    showDeleteModal.value = true;
};

// Deletar produto
const deleteProduct = async () => {
    try {
        await axios.delete(`/api/products/${selectedProductId.value}`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                Accept: 'application/json',
            },
        });

        showDeleteModal.value = false;
        router.reload(); // Recarrega os dados
    } catch (error) {
        console.error('Erro ao excluir produto:', error.response?.data || error.message);
    }
};
</script>

<template>
    <Head title="Produtos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Produtos</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between mb-4">
                        <h1 class="text-2xl font-bold">Lista de Produtos</h1>
                        <button
                            @click="showModal = true"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                        >
                            Novo Produto
                        </button>
                    </div>

                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-gray-300 px-4 py-2">Nome</th>
                                <th class="border border-gray-300 px-4 py-2">Cor</th>
                                <th class="border border-gray-300 px-4 py-2">Preço</th>
                                <th class="border border-gray-300 px-4 py-2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in products" :key="product.id">
                                <td class="border border-gray-300 px-4 py-2">{{ product.name }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ product.color }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ product.price }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <button
                                        @click="confirmDelete(product.id)"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded"
                                    >
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal de Criação -->
        <Modal :show="showModal" @close="showModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Novo Produto</h2>

                <form @submit.prevent="createProduct" class="mt-4">
                    <select v-model="form.supplier_id" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="" disabled>Selecione um fornecedor</option>
                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                            {{ supplier.name }}
                        </option>
                    </select>
                    <TextInput v-model="form.reference" type="text" placeholder="Referência" class="mt-1 block w-full" required />
                    <TextInput v-model="form.name" type="text" placeholder="Nome" class="mt-1 block w-full" required />
                    <TextInput v-model="form.color" type="text" placeholder="Cor" class="mt-4 block w-full" required />
                    <TextInput v-model="form.price" type="number" placeholder="Preço" class="mt-4 block w-full" required />

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="showModal = false">Cancelar</SecondaryButton>
                        <PrimaryButton type="submit" class="ms-3">Salvar</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal de Confirmação de Exclusão -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Tem certeza?</h2>
                <p class="mt-2 text-gray-600">Você deseja realmente excluir este produto? Esta ação não pode ser desfeita.</p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="showDeleteModal = false">Cancelar</SecondaryButton>
                    <PrimaryButton class="ms-3 bg-red-600 hover:bg-red-700" @click="deleteProduct">
                        Excluir
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
