<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

// Recebendo fornecedores do backend
defineProps({
    suppliers: Array,
});

// Controle do modal
const showModal = ref(false);
const showDeleteModal = ref(false);
const supplierToDelete = ref(null);

const confirmDelete = (id) => {
    supplierToDelete.value = id;
    showDeleteModal.value = true;
};

// Formulário de criação de fornecedor
const form = ref({
    name: '',
    cnpj: '',
    zip_code: '',
});

// const fetchSuppliers = async () => {
//     try {
//         const response = await axios.get('/api/suppliers', {
//             headers: {
//                 Authorization: `Bearer ${localStorage.getItem("token")}`,
//                 Accept: 'application/json'
//             }
//         });

//         suppliers.value = response.data;
//     } catch (error) {
//         console.error('Erro ao buscar fornecedores:', error.response?.data || error.message);
//     }
// };

// Criar fornecedor
const createSupplier = async () => {
    try {
        await axios.post('/api/suppliers', form.value, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                Accept: 'application/json'
            }
        });

        showModal.value = false;
        form.value = { name: '', cnpj: '', zip_code: '' };

        router.reload({ only: ['suppliers'] });
    } catch (error) {
        console.error('Erro ao criar fornecedor:', error.response.data);
    }
};

// Deletar fornecedor
const deleteSupplier = async () => {
    if (!supplierToDelete.value) return;

    try {
        await axios.delete(`/api/suppliers/${supplierToDelete.value}`, {
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
                Accept: 'application/json',
            },
        });

        // Fecha o modal e atualiza a lista
        showDeleteModal.value = false;
        router.reload({ only: ['suppliers'] });
    } catch (error) {
        console.error('Erro ao excluir fornecedor:', error.response?.data || error.message);
    }
};
</script>

<template>
    <Head title="Fornecedores" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Fornecedores</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between mb-4">
                        <h1 class="text-2xl font-bold">Lista de Fornecedores</h1>
                        <button
                            @click="showModal = true"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                        >
                            Novo Fornecedor
                        </button>
                    </div>

                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-gray-300 px-4 py-2">Nome</th>
                                <th class="border border-gray-300 px-4 py-2">CNPJ</th>
                                <th class="border border-gray-300 px-4 py-2">Endereço</th>
                                <th class="border border-gray-300 px-4 py-2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="supplier in suppliers" :key="supplier.id">
                                <td class="border border-gray-300 px-4 py-2">{{ supplier.name }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ supplier.cnpj }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ supplier.address }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <button
                                        @click="confirmDelete(supplier.id)"
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

        <!-- Modal -->
        <Modal :show="showModal" @close="showModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Novo Fornecedor</h2>

                <form @submit.prevent="createSupplier" class="mt-4">
                    <TextInput
                        v-model="form.name"
                        type="text"
                        placeholder="Nome"
                        class="mt-1 block w-full"
                        required
                    />

                    <TextInput
                        v-model="form.cnpj"
                        type="text"
                        placeholder="CNPJ"
                        class="mt-4 block w-full"
                        required
                    />

                    <TextInput
                        v-model="form.zip_code"
                        type="text"
                        placeholder="CEP"
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

        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Confirmar Exclusão</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Tem certeza de que deseja excluir este fornecedor? Essa ação não pode ser desfeita.
                </p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="showDeleteModal = false">Cancelar</SecondaryButton>
                    <PrimaryButton @click="deleteSupplier" class="ms-3 bg-red-600 hover:bg-red-700">
                        Excluir
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
