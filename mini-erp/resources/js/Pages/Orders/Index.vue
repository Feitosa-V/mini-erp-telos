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
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import {Clock, CheckCircle, XCircle, Plus, Pencil, Trash2,PlusCircle, X, ClipboardList } from 'lucide-vue-next';

const props = defineProps({
    orders: Array,
    suppliers: Array,
    products: Array,
});

const showModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const selectedOrder = ref(null);
const selectedOrderId = ref(null);
const showProductsModal = ref(false);
const selectedOrderProducts = ref([]);

const form = ref({
    supplier_id: '',
    products: [{ id: '', quantity: 1, unit_price: 0 }],
    notes: '',
});

// Mapeamento dos status para exibição
const statusMap = {
    pending: { label: 'Pendente', color: 'bg-yellow-100 text-yellow-700 border border-yellow-300', icon: Clock },
    completed: { label: 'Concluído', color: 'bg-green-100 text-green-700 border border-green-300', icon: CheckCircle },
    canceled: { label: 'Cancelado', color: 'bg-red-100 text-red-700 border border-red-300', icon: XCircle }
};

const confirmDelete = (id) => {
    selectedOrderId.value = id;
    showDeleteModal.value = true;
};

const confirmEdit = (order) => {
    selectedOrder.value = { ...order };
    showEditModal.value = true;
};

const filteredProducts = computed(() => {
    if (!form.value.supplier_id) return [];
    return props.products.filter(product => product.supplier_id == form.value.supplier_id);
});

watch(() => form.value.supplier_id, (newValue, oldValue) => {
    if (newValue !== oldValue) {
        form.value.products = [{ id: '', quantity: 1, unit_price: 0 }];
    }
});

const updateUnitPrice = (index) => {
    const selectedProduct = form.value.products[index];
    const product = filteredProducts.value.find(p => p.id === parseInt(selectedProduct.id));
    if (product) {
        form.value.products.splice(index, 1, { ...selectedProduct, unit_price: product.price });
    }
};

const totalValue = computed(() => {
    return form.value.products.reduce((total, product) => {
        return total + (product.quantity * product.unit_price);
    }, 0);
});

const addProduct = () => {
    form.value.products.push({ id: '', quantity: 1, unit_price: 0 });
};

const removeProduct = (index) => {
    form.value.products.splice(index, 1);
};

const createOrder = () => {
    axios.post('/api/orders', { ...form.value, total_value: totalValue.value }, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    }).then(() => {
        showModal.value = false;
        form.value = { supplier_id: '', products: [{ id: '', quantity: 1, unit_price: 0 }], notes: '' };
        router.reload();
    });
};

const updateOrder = () => {
    axios.put(`/api/orders/${selectedOrder.value.id}`, selectedOrder.value, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    }).then(() => {
        showEditModal.value = false;
        router.reload();
    });
};

const deleteOrder = () => {
    axios.delete(`/api/orders/${selectedOrderId.value}`, {
        headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
    }).then(() => {
        showDeleteModal.value = false;
        router.reload();
    });
};

// Abre o modal e define os produtos do pedido
const openProductsModal = (order) => {
    selectedOrderProducts.value = order.products; 
    showProductsModal.value = true;
};
</script>

<template>
    <Head title="Pedidos" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center">Pedidos</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between mb-4 items-center">
                        <h1 class="text-2xl font-bold text-gray-800">Lista de Pedidos</h1>
                        <PrimaryButton @click="showModal = true">
                            <Plus class="w-4 h-4 mr-2" /> Novo Pedido
                        </PrimaryButton>
                    </div>
                    <table class="w-full border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-200 text-gray-700">
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
                                <td class="border px-4 py-2">
                                    <span 
                                        :class="`flex items-center px-3 py-1 rounded-full text-sm font-medium ${statusMap[order.status?.toLowerCase()]?.color || 'bg-gray-100 text-gray-800 border border-gray-300'}`"
                                    >
                                        <component :is="statusMap[order.status?.toLowerCase()]?.icon" class="w-4 h-4 mr-1" />
                                        {{ statusMap[order.status?.toLowerCase()]?.label || 'Desconhecido' }}
                                    </span>
                                </td>
                                <td class="border px-4 py-2 text-center space-x-2">
                                    <PrimaryButton @click="openProductsModal(order)">
                                        <ClipboardList class="w-4 h-4 mr-1" /> Ver Produtos
                                    </PrimaryButton>
                                    <WarningButton @click="confirmEdit(order)">
                                        <Pencil class="w-4 h-4 mr-1" /> Editar
                                    </WarningButton>
                                    <DangerButton @click="confirmDelete(order.id)" class="ms-3">
                                        <Trash2 class="w-4 h-4 mr-1" /> Excluir
                                    </DangerButton>
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
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-medium text-gray-900 flex items-center">
                        <ClipboardList class="w-5 h-5 mr-2 text-gray-700" />
                        Novo Pedido
                    </h2>
                    <button @click="showModal = false" class="text-gray-500 hover:text-gray-700">
                        <X class="w-6 h-6" />
                    </button>
                </div>

                <form @submit.prevent="createOrder">
                    <!-- Fornecedor -->
                    <div class="mb-4">
                        <InputLabel for="supplier" value="Fornecedor:" />
                        <select 
                            id="supplier" 
                            v-model="form.supplier_id" 
                            class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-300"
                            required
                        >
                            <option value="" disabled>Selecione um fornecedor</option>
                            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                {{ supplier.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Produtos -->
                    <h3 class="font-semibold text-gray-800 mt-4">Produtos:</h3>
                    <div v-for="(product, index) in form.products" :key="index" class="bg-gray-50 p-4 rounded-lg shadow-sm mt-2">
                        <div class="grid grid-cols-12 gap-4 items-end">
                            <div class="col-span-5">
                                <InputLabel for="product" value="Produto" />
                                <select 
                                    v-model="product.id" 
                                    @change="updateUnitPrice(index)" 
                                    class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-indigo-300"
                                    required
                                >
                                    <option :value="''" disabled>Selecione um produto</option>
                                    <option v-for="p in filteredProducts" :key="p.id" :value="p.id">{{ p.name }}</option>
                                </select>
                            </div>
                            <div class="col-span-3">
                                <InputLabel for="quantity" value="Quantidade" />
                                <TextInput v-model="product.quantity" type="number" min="1" class="block w-full" required />
                            </div>
                            <div class="col-span-3">
                                <InputLabel for="unit_price" value="Preço Unitário (R$)" />
                                <TextInput v-model="product.unit_price" type="number" class="block w-full" disabled />
                            </div>
                            <div class="col-span-1 flex justify-end">
                                <button 
                                    type="button" 
                                    @click="removeProduct(index)" 
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg flex items-center transition"
                                >
                                    <Trash2 class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Botão de Adicionar Produto -->
                    <button 
                        type="button" 
                        @click="addProduct" 
                        class="mt-4 flex items-center bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition"
                    >
                        <PlusCircle class="w-5 h-5 mr-2" />
                        Adicionar Produto
                    </button>

                    <!-- Total -->
                    <p class="mt-4 text-lg font-semibold text-gray-900">Total: R$ {{ totalValue.toFixed(2) }}</p>

                    <!-- Ações -->
                    <div class="mt-6 flex justify-end space-x-2">
                        <SecondaryButton @click="showModal = false">
                            <X class="w-4 h-4 mr-2" /> Cancelar
                        </SecondaryButton>
                        <PrimaryButton type="submit">
                            <PlusCircle class="w-4 h-4 mr-2" /> Salvar Pedido
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal de Edição -->
        <Modal :show="showEditModal" @close="showEditModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Editar Pedido</h2>
                <form @submit.prevent="updateOrder">
                    <div class="mt-4">
                        <InputLabel for="status" value="Status" />
                        <select v-model="selectedOrder.status" class="block w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="Pending">Pendente</option>
                            <option value="Completed">Concluído</option>
                            <option value="Canceled">Cancelado</option>
                        </select>
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
                <p class="mt-2 text-sm text-gray-600">Tem certeza que deseja excluir este pedido?</p>
                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="showDeleteModal = false">Cancelar</SecondaryButton>
                    <PrimaryButton @click="deleteOrder" class="ms-3 bg-red-600 hover:bg-red-700">Excluir</PrimaryButton>
                </div>
            </div>
        </Modal>

        <Modal :show="showProductsModal" @close="showProductsModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 flex items-center">
                    <ClipboardList class="w-5 h-5 mr-2 text-gray-700" />
                    Produtos do Pedido
                </h2>
                <div v-if="selectedOrderProducts.length > 0" class="mt-4">
                    <table class="w-full border-collapse border border-gray-300 text-sm">
                        <thead>
                            <tr class="bg-gray-200 text-gray-700">
                                <th class="border px-4 py-2">Produto</th>
                                <th class="border px-4 py-2">Quantidade</th>
                                <th class="border px-4 py-2">Preço Unitário</th>
                                <th class="border px-4 py-2">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in selectedOrderProducts" :key="product.id">
                                <td class="border px-4 py-2">{{ product.name }}</td>
                                <td class="border px-4 py-2">{{ product.pivot.quantity }}</td>
                                <td class="border px-4 py-2">R$ {{ product.pivot.unit_price }}</td>
                                <td class="border px-4 py-2">R$ {{ (product.pivot.quantity * product.pivot.unit_price).toFixed(2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-else class="mt-4 text-gray-600">Nenhum produto encontrado neste pedido.</p>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="showProductsModal = false">
                        <X class="w-4 h-4 mr-2" /> Fechar
                    </SecondaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
