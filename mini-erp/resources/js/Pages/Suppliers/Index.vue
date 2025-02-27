<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

// Recebendo fornecedores do backend
defineProps({
    suppliers: Array,
});


// Controle do modal
const showModal = ref(false);

// Formulário de criação de fornecedor
const form = ref({
    name: '',
    cnpj: '',
    zip_code: '',
});

// Criar fornecedor
const createSupplier = () => {
    router.post('/api/suppliers', form.value, {
        headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
        onSuccess: () => {
            showModal.value = false; // Fecha o modal após sucesso
            form.value = { name: '', cnpj: '', zip_code: '' }; // Limpa o formulário
            suppliers.value.push(response.props.supplier); // Adiciona dinamicamente
        },
    });
};

// Deletar fornecedor
const deleteSupplier = (id) => {
    if (confirm('Are you sure?')) {
        router.delete(`/api/suppliers/${id}`,{
            headers: {
                Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
        });
    }
};
</script>

<template>
    <Head title="Suppliers" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Suppliers</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between mb-4">
                        <h1 class="text-2xl font-bold">Supplier List</h1>
                        <button
                            @click="showModal = true"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                        >
                            New Supplier
                        </button>
                    </div>

                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border border-gray-300 px-4 py-2">Name</th>
                                <th class="border border-gray-300 px-4 py-2">CNPJ</th>
                                <th class="border border-gray-300 px-4 py-2">Address</th>
                                <th class="border border-gray-300 px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="supplier in suppliers" :key="supplier.id">
                                <td class="border border-gray-300 px-4 py-2">{{ supplier.name }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ supplier.cnpj }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ supplier.address }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <button
                                        @click="deleteSupplier(supplier.id)"
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
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
            <div class="bg-white p-6 rounded shadow-lg w-96">
                <h2 class="text-xl font-bold mb-4">New Supplier</h2>
                <form @submit.prevent="createSupplier">
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="Name"
                        class="border w-full p-2 mb-2"
                        required
                    />
                    <input
                        v-model="form.cnpj"
                        type="text"
                        placeholder="CNPJ"
                        class="border w-full p-2 mb-2"
                        required
                    />
                    <input
                        v-model="form.zip_code"
                        type="text"
                        placeholder="Zip Code"
                        class="border w-full p-2 mb-4"
                        required
                    />
                    <div class="flex justify-end">
                        <button type="button" @click="showModal = false" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
