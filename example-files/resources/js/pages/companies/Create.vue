<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import CompanyForm from './components/CompanyForm.vue';
import { Button } from '@/components/ui/button';
import { ArrowLeft } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

// Wayfinder actions
import CompanyController from '@/actions/App/Http/Controllers/CompanyController';

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Empresas', href: CompanyController.index.url() },
    { title: 'Crear', href: '#' },
];
</script>

<template>
    <AppLayout title="Crear Empresa" :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" as-child>
                    <Link :href="CompanyController.index.url()">
                        <ArrowLeft class="size-4" />
                    </Link>
                </Button>
                <Heading
                    title="Crear Empresa"
                    description="Agregar una nueva empresa al sistema"
                />
            </div>

            <!-- Form -->
            <CompanyForm
                :form-props="CompanyController.store.form()"
                :cancel-url="CompanyController.index.url()"
                submit-label="Crear Empresa"
                processing-label="Creando..."
            />
        </div>
    </AppLayout>
</template>
