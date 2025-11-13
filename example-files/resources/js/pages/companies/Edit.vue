<script setup lang="ts">
import { computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import CompanyForm from './components/CompanyForm.vue';
import { Button } from '@/components/ui/button';
import { ArrowLeft } from 'lucide-vue-next';
import type { BreadcrumbItem, Company } from '@/types';

// Wayfinder actions
import CompanyController from '@/actions/App/Http/Controllers/CompanyController';



interface Props {
    company: Company;
}

const props = defineProps<Props>();

const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
    { title: 'Empresas', href: CompanyController.index.url() },
    { title: props.company?.name ?? 'Editar', href: CompanyController.edit.url(props.company?.id ?? 0) },
]);

const formProps = computed(() => CompanyController.update.form(props.company?.id ?? 0));

// mounted hook
onMounted(() => {
    console.log('Edit Company Page Mounted');
    console.log('Company:', props.company);
});
</script>

<template>
    <AppLayout :title="`Editar ${company.name}`" :breadcrumbs="breadcrumbItems">
        <div class="mx-auto w-full space-y-6 p-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" as-child>
                    <Link :href="CompanyController.index.url()">
                        <ArrowLeft class="size-4" />
                    </Link>
                </Button>
                <Heading
                    :title="`Editar ${company.name}`"
                    description="Actualizar información de la empresa"
                />
            </div>

            <!-- Form -->
            <CompanyForm
                :form-props="formProps"
                :company="company"
                :cancel-url="CompanyController.index.url()"
                submit-label="Guardar Cambios"
                processing-label="Guardando..."
            />
        </div>
    </AppLayout>
</template>
