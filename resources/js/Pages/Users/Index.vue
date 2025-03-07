<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import WarningButton from '@/Components/WarningButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import { Plus, Pencil, Ban, CheckCircle, Users } from 'lucide-vue-next';

// Recebendo usuários do backend
defineProps({
    users: Array
});

// Controle dos modais
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedUser = ref(null);

// Formulário de criação e edição
const form = ref({ name: '', email: '', password: '', role: '' });

const confirmEdit = (user) => {
    selectedUser.value = { ...user, password: '' }; // Limpa a senha para edição
    showEditModal.value = true;
};

// Criar usuário
const createUser = () => {
    axios.post('/api/users', form.value, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
    }).then(() => {
        showCreateModal.value = false;
        form.value = { name: '', email: '', password: '', role: '' };
        router.reload();
    });
};

// Atualizar usuário
const updateUser = () => {
    axios.put(`/api/users/${selectedUser.value.id}`, selectedUser.value, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
    }).then(() => {
        showEditModal.value = false;
        router.reload();
    });
};

// Bloquear/desbloquear usuário
const toggleUserStatus = (user) => {
    axios.put(`/api/users/${user.id}/toggle-status`, {}, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
    }).then(() => {
        router.reload();
    });
};
</script>

<template>
    <Head title="Usuários" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">
                <Users class="w-6 h-6 mr-2 text-gray-700" />
                Usuários
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between mb-4 items-center">
                        <h1 class="text-2xl font-bold text-gray-800">Gerenciar Usuários</h1>
                        <PrimaryButton @click="showCreateModal = true">
                            <Plus class="w-4 h-4 mr-2" /> Novo Usuário
                        </PrimaryButton>
                    </div>

                    <table class="w-full border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-200 text-gray-700">
                                <th class="border px-4 py-3 text-left">Nome</th>
                                <th class="border px-4 py-3 text-left">Email</th>
                                <th class="border px-4 py-3 text-left">Função</th>
                                <th class="border px-4 py-3 text-center">Status</th>
                                <th class="border px-4 py-3 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-100 transition">
                                <td class="border px-4 py-3">{{ user.name }}</td>
                                <td class="border px-4 py-3">{{ user.email }}</td>
                                <td class="border px-4 py-3 capitalize">{{ user.role }}</td>
                                <td class="border px-4 py-3 text-center">
                                    <span v-if="user.status" class="text-green-600 font-bold">Ativo</span>
                                    <span v-else class="text-red-600 font-bold">Bloqueado</span>
                                </td>
                                <td class="border px-4 py-3 text-center space-x-2">
                                    <WarningButton @click="confirmEdit(user)">
                                        <Pencil class="w-4 h-4 mr-1" /> Editar
                                    </WarningButton>
                                    <PrimaryButton @click="toggleUserStatus(user)" :class="user.status ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600'">
                                        <Ban v-if="user.status" class="w-4 h-4 mr-1" />
                                        <CheckCircle v-else class="w-4 h-4 mr-1" />
                                        {{ user.status ? 'Bloquear' : 'Desbloquear' }}
                                    </PrimaryButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal de Criação -->
        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Novo Usuário</h2>
                <form @submit.prevent="createUser">
                    <InputLabel value="Nome" />
                    <TextInput v-model="form.name" type="text" class="mt-1 block w-full" required />

                    <InputLabel value="Email" />
                    <TextInput v-model="form.email" type="email" class="mt-1 block w-full" required />

                    <InputLabel value="Senha" />
                    <TextInput v-model="form.password" type="password" class="mt-1 block w-full" required />

                    <InputLabel value="Função" />
                    <select v-model="form.role" class="border w-full p-2 rounded-lg shadow-sm mt-1">
                        <option value="seller">Vendedor</option>
                        <option value="admin">Admin</option>
                    </select>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="showCreateModal = false">Cancelar</SecondaryButton>
                        <PrimaryButton type="submit">Salvar</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
