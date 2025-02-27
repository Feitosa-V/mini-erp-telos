<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

// Recebendo fornecedores do backend
defineProps({
    products: Array,
});


// Controle do modal
const showModal = ref(false);

// Formulário de criação de fornecedor
const form = ref({
    name: '',
    color: '',
    price: '',
});

// Criar fornecedor
const createProduct = () => {
    router.post('/api/products', form.value, {
        headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
        onSuccess: () => {
            showModal.value = false; // Fecha o modal após sucesso
            // form.value = { name: '', cnpj: '', zip_code: '' }; // Limpa o formulário
            // suppliers.value.push(response.props.supplier); // Adiciona dinamicamente
        },
    });
};

// Deletar fornecedor
const deleteSupplier = (id) => {
    if (confirm('Are you sure?')) {
        router.delete(`/api/products/${id}`,{
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
        });
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
                                <th class="border border-gray-300 px-4 py-2">Name</th>
                                <th class="border border-gray-300 px-4 py-2">Cor</th>
                                <th class="border border-gray-300 px-4 py-2">Preço</th>
                                <th class="border border-gray-300 px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in products" :key="product.id">
                                <td class="border border-gray-300 px-4 py-2">{{ product.name }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ product.color }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ product.price }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <button
                                        @click="deleteSupplier(product.id)"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <Modal :show="showModal" @close="showModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Novo Produto</h2>

                <form @submit.prevent="createProduct" class="mt-4">
                    <TextInput
                        v-model="form.name"
                        type="text"
                        placeholder="Name"
                        class="mt-1 block w-full"
                        required
                    />

                    <TextInput
                        v-model="form.color"
                        type="text"
                        placeholder="Cor"
                        class="mt-4 block w-full"
                        required
                    />

                    <TextInput
                        v-model="form.price"
                        type="number"
                        placeholder="Preço"
                        class="mt-4 block w-full"
                        required
                    />

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="showModal = false"> Cancelar </SecondaryButton>

                        <PrimaryButton type="submit" class="ms-3">Salvar</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
