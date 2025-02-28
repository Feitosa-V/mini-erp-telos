<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

// Recebendo usuários do backend
defineProps({
    users: Array
});

// Controle dos modais
const showCreateModal = ref(false);
const showDeleteModal = ref(false);
const selectedUserId = ref(null);

// Formulário de criação de usuário
const form = ref({ name: '', email: '', password: '', role: 'user' });

// Criar usuário
const createUser = () => {
    axios.post('/api/users', form.value, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
    }).then(() => {
        showCreateModal.value = false;
        form.value = { name: '', email: '', password: '', role: 'user' };
        location.reload(); // Atualiza a lista
    });
};

// Abrir modal de exclusão
const confirmDelete = (id) => {
    selectedUserId.value = id;
    showDeleteModal.value = true;
};

// Deletar usuário
const deleteUser = () => {
    axios.delete(`/api/users/${selectedUserId.value}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
    }).then(() => {
        showDeleteModal.value = false;
        location.reload();
    });
};
</script>

<template>
    <Head title="Usuários" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Usuários</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between mb-4">
                        <h1 class="text-2xl font-bold">Gerenciar Usuários</h1>
                        <button @click="showCreateModal = true" class="bg-blue-500 text-white px-4 py-2 rounded">
                            Novo Usuário
                        </button>
                    </div>

                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-4 py-2">Nome</th>
                                <th class="border px-4 py-2">Email</th>
                                <th class="border px-4 py-2">Função</th>
                                <th class="border px-4 py-2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users" :key="user.id">
                                <td class="border px-4 py-2">{{ user.name }}</td>
                                <td class="border px-4 py-2">{{ user.email }}</td>
                                <td class="border px-4 py-2">{{ user.role }}</td>
                                <td class="border px-4 py-2">
                                    <button @click="confirmDelete(user.id)" class="bg-red-500 text-white px-2 py-1 rounded">
                                        Deletar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal de criação -->
        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Novo Usuário</h2>

                <form @submit.prevent="createUser" class="mt-4">
                    <TextInput v-model="form.name" type="text" placeholder="Nome" class="mt-2 block w-full" required />
                    <TextInput v-model="form.email" type="email" placeholder="Email" class="mt-2 block w-full" required />
                    <TextInput v-model="form.password" type="password" placeholder="Senha" class="mt-2 block w-full" required />
                    
                    <label class="block mt-4">Função:</label>
                    <select v-model="form.role" class="border w-full p-2 rounded">
                        <option value="user">Usuário</option>
                        <option value="admin">Admin</option>
                    </select>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="showCreateModal = false">Cancelar</SecondaryButton>
                        <PrimaryButton type="submit" class="ml-3">Salvar</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal de exclusão -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Excluir Usuário</h2>
                <p class="mt-2">Tem certeza que deseja excluir este usuário? Essa ação não pode ser desfeita.</p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="showDeleteModal = false">Cancelar</SecondaryButton>
                    <PrimaryButton @click="deleteUser" class="ml-3 bg-red-500">Excluir</PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
