<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ShoppingCart, Users, Truck, DollarSign } from 'lucide-vue-next';

defineProps({
    totalOrders: Number,
    totalUsers: Number,
    totalSuppliers: Number,
    totalSales: Number
});
</script>

<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h1 class="text-2xl font-bold">Resumo Geral</h1>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                        <div class="bg-blue-500 text-white p-4 rounded-lg text-center">
                            <ShoppingCart class="h-8 w-8 mx-auto mb-2" />
                            <h3 class="text-xl font-bold">{{ totalOrders }}</h3>
                            <p>Pedidos</p>
                        </div>
                        <div class="bg-red-500 text-white p-4 rounded-lg text-center">
                            <Users class="h-8 w-8 mx-auto mb-2" />
                            <h3 class="text-xl font-bold">{{ totalUsers }}</h3>
                            <p>Usuários</p>
                        </div>
                        <div class="bg-yellow-500 text-white p-4 rounded-lg text-center">
                            <Truck class="h-8 w-8 mx-auto mb-2" />
                            <h3 class="text-xl font-bold">{{ totalSuppliers }}</h3>
                            <p>Fornecedores</p>
                        </div>
                        <div class="bg-green-500 text-white p-4 rounded-lg text-center">
                            <DollarSign class="h-8 w-8 mx-auto mb-2" />
                            <h3 class="text-xl font-bold">R$ {{ Number(totalSales).toFixed(2) }}</h3>
                            <p>Vendas Totais</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
