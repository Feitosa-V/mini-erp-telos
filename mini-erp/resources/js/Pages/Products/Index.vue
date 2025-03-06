<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import WarningButton from '@/Components/WarningButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';

// Recebendo produtos do backend
defineProps({
    products: Array,
    suppliers: Array,
});

// Controle dos modais
const showModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const productToDelete = ref(null);
const productToEdit = ref(null);

const confirmDelete = (id) => {
    productToDelete.value = id;
    showDeleteModal.value = true;
};

const confirmEdit = (product) => {
    productToEdit.value = { ...product };
    showEditModal.value = true;
};

// Formulário de criação e edição
const form = ref({
    supplier_id: '',
    reference: '',
    name: '',
    color: '',
    price: '',
});

const createProduct = async () => {
    try {
        await axios.post('/api/products', form.value, {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
        });
        showModal.value = false;
        form.value = { supplier_id: '', reference: '', name: '', color: '', price: '' };
        router.reload({ only: ['products'] });
    } catch (error) {
        console.error('Erro ao criar produto:', error.response.data);
    }
};

const updateProduct = async () => {
    try {
        await axios.put(`/api/products/${productToEdit.value.id}`, productToEdit.value, {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
        });
        showEditModal.value = false;
        router.reload({ only: ['products'] });
    } catch (error) {
        console.error('Erro ao atualizar produto:', error.response.data);
    }
};

const deleteProduct = async () => {
    if (!productToDelete.value) return;
    try {
        await axios.delete(`/api/products/${productToDelete.value}`, {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
        });
        showDeleteModal.value = false;
        router.reload({ only: ['products'] });
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
                <div class="bg-white shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between mb-4">
                        <h1 class="text-2xl font-bold">Lista de Produtos</h1>
                        <PrimaryButton @click="showModal = true">
                            <Plus class="w-4 h-4 mr-2" /> Novo Produto
                        </PrimaryButton>
                    </div>

                    <table class="w-full border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-4 py-2">Nome</th>
                                <th class="border px-4 py-2">Cor</th>
                                <th class="border px-4 py-2">Preço</th>
                                <th class="border px-4 py-2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in products" :key="product.id" class="text-center">
                                <td class="border px-4 py-2">{{ product.name }}</td>
                                <td class="border px-4 py-2">{{ product.color }}</td>
                                <td class="border px-4 py-2">R$ {{ product.price }}</td>
                                <td class="border px-4 py-2 space-x-2">
                                    <WarningButton @click="confirmEdit(product)">
                                        <Pencil class="w-4 h-4 mr-1" /> Editar
                                    </WarningButton>
                                    <DangerButton @click="confirmDelete(product.id)" class="ms-3">
                                        <Trash2 class="w-4 h-4 mr-1" /> Excluir
                                    </DangerButton>
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
                <form @submit.prevent="createProduct">
                    <div class="mt-4">
                        <InputLabel value="Fornecedor:" />
                        <select v-model="form.supplier_id" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="" disabled>Selecione um fornecedor</option>
                            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                {{ supplier.name }}
                            </option>
                        </select>
                    </div>
                    <div class="mt-4">
                        <InputLabel value="Referência:" />
                        <TextInput v-model="form.reference" type="text" class="mt-1 block w-full" required />
                    </div>
                    <div class="mt-4">
                        <InputLabel value="Nome:" />
                        <TextInput v-model="form.name" type="text" class="mt-1 block w-full" required />
                    </div>
                    <div class="mt-4">
                        <InputLabel value="Cor:" />
                        <TextInput v-model="form.color" type="text" class="mt-1 block w-full" required />
                    </div>
                    <div class="mt-4">
                        <InputLabel value="Preço:" />
                        <TextInput v-model="form.price" type="number" class="mt-1 block w-full" required />
                    </div>
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

        <!-- Modal de Edição -->
        <Modal :show="showEditModal" @close="showEditModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Editar Produto</h2>
                <form @submit.prevent="updateProduct">
                    <div class="mt-4">
                        <InputLabel value="Nome:" />
                        <TextInput v-model="productToEdit.name" type="text" class="block w-full" required />
                    </div>
                    <div class="mt-4">
                        <InputLabel value="Cor:" />
                        <TextInput v-model="productToEdit.color" type="text" class="block w-full" required />
                    </div>
                    <div class="mt-4">
                        <InputLabel value="Preço:" />
                        <TextInput v-model="productToEdit.price" type="number" class="block w-full" required />
                    </div>
                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="showEditModal = false">Cancelar</SecondaryButton>
                        <PrimaryButton type="submit" class="ms-3">Salvar</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
