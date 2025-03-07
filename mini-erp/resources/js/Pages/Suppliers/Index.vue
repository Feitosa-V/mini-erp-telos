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
import {Users, Plus, Pencil, Trash2 } from 'lucide-vue-next';

// Máscara CNPJ
const formatCNPJ = (value) => {
    value = value.replace(/\D/g, "");
    value = value.replace(/^(\d{2})(\d)/, "$1.$2");
    value = value.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
    value = value.replace(/\.(\d{3})(\d)/, ".$1/$2");
    value = value.replace(/(\d{4})(\d)/, "$1-$2");
    return value.slice(0, 18);
};

// Recebendo fornecedores do backend
defineProps({
    suppliers: Array,
    users: Array,
});

// Controle dos modais
const showModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const supplierToDelete = ref(null);
const supplierToEdit = ref(null);
const showVincularModal = ref(false);
const vinculo = ref({ supplier_id: '', supplier_name: '', user_id: '' });

const openVincularModal = (supplier) => {
    vinculo.value.supplier_id = supplier.id;
    vinculo.value.supplier_name = supplier.name;
    showVincularModal.value = true;
};

const confirmDelete = (id) => {
    supplierToDelete.value = id;
    showDeleteModal.value = true;
};

const confirmEdit = (supplier) => {
    supplierToEdit.value = { ...supplier };
    showEditModal.value = true;
};

// Formulário de criação e edição
const form = ref({
    name: '',
    cnpj: '',
    zip_code: '',
    status: ''
});

const vincularVendedor = async () => {
    try {
        await axios.post('/api/suppliers/attach-user', vinculo.value, {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
        });

        showVincularModal.value = false;
        router.reload({ only: ['suppliers'] });
    } catch (error) {
        console.error('Erro ao vincular vendedor:', error.response.data);
    }
};

const createSupplier = async () => {
    try {
        await axios.post('/api/suppliers', form.value, {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
        });
        showModal.value = false;
        form.value = { name: '', cnpj: '', zip_code: '' };
        router.reload({ only: ['suppliers'] });
    } catch (error) {
        console.error('Erro ao criar fornecedor:', error.response.data);
    }
};

const updateSupplier = async () => {
    try {
        await axios.put(`/api/suppliers/${supplierToEdit.value.id}`, supplierToEdit.value, {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
        });
        showEditModal.value = false;
        router.reload({ only: ['suppliers'] });
    } catch (error) {
        console.error('Erro ao atualizar fornecedor:', error.response.data);
    }
};

const deleteSupplier = async () => {
    if (!supplierToDelete.value) return;
    try {
        await axios.delete(`/api/suppliers/${supplierToDelete.value}`, {
            headers: { Authorization: `Bearer ${localStorage.getItem("token")}` }
        });
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
                <div class="bg-white shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between mb-4">
                        <h1 class="text-2xl font-bold text-gray-800">Lista de Fornecedores</h1>
                        <PrimaryButton @click="showModal = true">
                            <Plus class="w-4 h-4 mr-2" /> Novo Fornecedor
                        </PrimaryButton>
                    </div>

                    <table class="w-full border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-200 text-gray-700">
                                <th class="border px-4 py-2">Nome</th>
                                <th class="border px-4 py-2">CNPJ</th>
                                <th class="border px-4 py-2">Endereço</th>
                                <th class="border px-4 py-2">Status</th>
                                <th class="border px-4 py-2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="supplier in suppliers" :key="supplier.id" class="text-center">
                                <td class="border px-4 py-2">{{ supplier.name }}</td>
                                <td class="border px-4 py-2">{{ supplier.cnpj }}</td>
                                <td class="border px-4 py-2">{{ supplier.address }}</td>
                                <td class="border px-4 py-3 text-center">
                                    <span v-if="supplier.status" class="text-green-600 font-bold">Ativo</span>
                                    <span v-else class="text-red-600 font-bold">Bloqueado</span>
                                </td>
                                <td class="border px-4 py-2 space-x-2">
                                    <PrimaryButton @click="openVincularModal(supplier)">
                                        <Users class="w-4 h-4 mr-1" /> Vincular Vendedor
                                    </PrimaryButton>
                                    <WarningButton @click="confirmEdit(supplier)">
                                        <Pencil class="w-4 h-4 mr-1" /> Editar
                                    </WarningButton>
                                    <DangerButton
                                        class="ms-3"
                                        :class="{ 'opacity-25': form.processing }"
                                        :disabled="form.processing"
                                        @click="confirmDelete(supplier.id)"
                                    >
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
                <h2 class="text-lg font-medium text-gray-900">Novo Fornecedor</h2>
                <form @submit.prevent="createSupplier">
                    <div class="mt-4">
                        <InputLabel for="name" value="Nome:" />
                        <TextInput v-model="form.name" type="text" class="block w-full"  placeholder="Informe o nome" required />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="cnpj" value="CNPJ:" />
                        <TextInput 
                            v-model="form.cnpj" 
                            type="text" 
                            class="block w-full" 
                            placeholder="Informe o CNPJ" 
                            required 
                            @input="form.cnpj = formatCNPJ(form.cnpj)" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="zip_code" value="CEP:" />
                        <TextInput v-model="form.zip_code" type="text" class="block w-full" placeholder="Informe o CEP" required />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="showModal = false">Cancelar</SecondaryButton>
                        <PrimaryButton type="submit" class="ms-3">Salvar</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal de Edição -->
        <Modal :show="showEditModal" @close="showEditModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Editar Fornecedor</h2>
                <form @submit.prevent="updateSupplier">
                    <div class="mt-4">
                        <InputLabel for="name" value="Nome:" />
                        <TextInput v-model="supplierToEdit.name" type="text" class="block w-full" required />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="cnpj" value="CNPJ:" />
                        <TextInput 
                            v-model="supplierToEdit.cnpj" 
                            type="text" 
                            class="block w-full" 
                            required 
                            @input="supplierToEdit.cnpj = formatCNPJ(supplierToEdit.cnpj)"/>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="zip_code" value="CEP:" />
                        <TextInput v-model="supplierToEdit.zip_code" type="text" class="block w-full" required />
                    </div>

                    <div class="mt-4">
                        <div class="mt-4">
                        <InputLabel for="status" value="Status:" />
                        <select v-model="supplierToEdit.status" class="block w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
</div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <SecondaryButton @click="showEditModal = false">Cancelar</SecondaryButton>
                        <PrimaryButton type="submit" class="ms-3">Salvar</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal de Exclusão -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Confirmar Exclusão</h2>
                <p class="mt-2 text-sm text-gray-600">Tem certeza que deseja excluir este fornecedor?</p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="showDeleteModal = false">Cancelar</SecondaryButton>
                    <PrimaryButton @click="deleteSupplier" class="ms-3 bg-red-600 hover:bg-red-700">Excluir</PrimaryButton>
                </div>
            </div>
        </Modal>

        <Modal :show="showVincularModal" @close="showVincularModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Vincular Vendedor</h2>
                <p class="text-sm text-gray-600">Selecione um vendedor para associá-lo ao fornecedor.</p>

                <form @submit.prevent="vincularVendedor">
                    <div class="mt-4">
                        <InputLabel value="Fornecedor:" />
                        <TextInput v-model="vinculo.supplier_name" type="text" class="block w-full bg-gray-100" disabled />
                    </div>

                    <div class="mt-4">
                        <InputLabel value="Vendedor:" />
                        <select v-model="vinculo.user_id" class="block w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="" disabled>Selecione um vendedor</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">
                                {{ user.name }}
                            </option>
                        </select>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">
                        <SecondaryButton @click="showVincularModal = false">Cancelar</SecondaryButton>
                        <PrimaryButton type="submit">Vincular</PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
