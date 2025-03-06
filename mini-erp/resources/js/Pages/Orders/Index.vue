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
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';

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

const form = ref({
    supplier_id: '',
    products: [{ id: '', quantity: 1, unit_price: 0 }],
    notes: '',
});

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
</script>

<template>
    <Head title="Pedidos" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pedidos</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between mb-4">
                        <h1 class="text-2xl font-bold">Lista de Pedidos</h1>
                        <PrimaryButton @click="showModal = true">
                            <Plus class="w-4 h-4 mr-2" /> Novo Pedido
                        </PrimaryButton>
                    </div>
                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-4 py-2">Fornecedor</th>
                                <th class="border px-4 py-2">Data</th>
                                <th class="border px-4 py-2">Total</th>
                                <th class="border px-4 py-2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="order in orders" :key="order.id">
                                <td class="border px-4 py-2">{{ order.supplier.name }}</td>
                                <td class="border px-4 py-2">{{ order.order_date }}</td>
                                <td class="border px-4 py-2">R$ {{ order.total_value }}</td>
                                <td class="border px-4 py-2 space-x-2">
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
                <h2 class="text-lg font-medium text-gray-900">Novo Pedido</h2>

                <form @submit.prevent="createOrder" class="mt-4">
                    <label>Fornecedor:</label>
                    <select v-model="form.supplier_id" class="block w-full mt-1 border-gray-300 rounded-lg">
                        <option value="" disabled>Selecione um fornecedor</option>
                        <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                            {{ supplier.name }}
                        </option>
                    </select>

                    <h3 class="mt-4">Produtos:</h3>
                    <div v-for="(product, index) in form.products" :key="index" class="mt-2">
                        <label>Produto:</label>
                        <select v-model="product.id" @change="updateUnitPrice(index)" class="block w-full border-gray-300 rounded-lg">
                            <option :value="''" disabled>{{ productPlaceholder }}</option>
                            <option v-for="p in filteredProducts" :key="p.id" :value="p.id">{{ p.name }}</option>
                        </select>

                        <label>Quantidade:</label>
                        <TextInput v-model="product.quantity" type="number" class="mt-2 block w-full" required />

                        <label>Preço Unitário:</label>
                        <TextInput v-model="product.unit_price" type="number" class="mt-2 block w-full" disabled />

                        <button type="button" @click="removeProduct(index)" class="bg-red-500 text-white px-2 py-1 rounded mt-2">Remover</button>
                    </div>

                    <button type="button" @click="addProduct" class="bg-green-500 text-white px-2 py-1 rounded mt-2">Adicionar Produto</button>

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
