<script setup lang="ts">
import { ref, h, computed, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import DataTable from '@/components/DataTable.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Plus, X, Filter, Building2, FileText, CircleCheck, CircleX, ListFilter } from 'lucide-vue-next';
import { useCrudColumns } from '@/composables/useCrudColumns';
import { useDebounceFn } from '@vueuse/core';
import type { BreadcrumbItem, Company, PaginatedResponse } from '@/types';
import type { ColumnDef } from '@tanstack/vue-table';

// Wayfinder actions
import CompanyController from '@/actions/App/Http/Controllers/CompanyController';
import { formatDate } from '@/lib/utils';



interface Props {
    companies: PaginatedResponse<Company>;
    filters: {
        filter?: {
            name?: string;
            nit?: string;
            active?: string;
        };
        sort?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Empresas', href: CompanyController.index.url() },
];

// Search filters
const searchName = ref(props.filters?.filter?.name || '');
const searchNit = ref(props.filters?.filter?.nit || '');
const filterActive = ref<string | undefined>(props.filters?.filter?.active || undefined);

// Popover state
const showFiltersPopover = ref(false);

// Delete dialog
const showDeleteDialog = ref(false);
const companyToDelete = ref<Company | null>(null);

// Selection state
const selectedCompanies = ref<Company[]>([]);

// Computed property to count active filters (only filters in the popover)
const activeFiltersCount = computed(() => {
    let count = 0;
    // searchName is handled by DataTable's search field, not in popover
    if (searchNit.value) count++;
    if (filterActive.value) count++;
    return count;
});

// Check if any filter is active
const hasActiveFilters = computed(() => activeFiltersCount.value > 0);

// Filter companies
const applyFilters = () => {
    router.get(
        CompanyController.index.url(),
        {
            filter: {
                name: searchName.value || undefined,
                nit: searchNit.value || undefined,
                active: filterActive.value || undefined,
            },
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
    showFiltersPopover.value = false;
};

// Debounced filter application for text inputs
const debouncedApplyFilters = useDebounceFn(() => {
    applyFilters();
}, 500);

// Watch for changes in text inputs to auto-apply filters with debounce
watch([searchNit], () => {
    debouncedApplyFilters();
});

const clearFilters = () => {
    // Only clear filters from the popover, keep searchName (handled by DataTable)
    searchNit.value = '';
    filterActive.value = undefined;
    router.get(
        CompanyController.index.url(),
        {
            filter: {
                name: searchName.value || undefined,
            },
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
    showFiltersPopover.value = false;
};

// Delete company
const confirmDelete = (company: Company) => {
    companyToDelete.value = company;
    showDeleteDialog.value = true;
};

const deleteCompany = () => {
    if (!companyToDelete.value) return;

    router.delete(CompanyController.destroy.url(companyToDelete.value.id), {
        onSuccess: () => {
            showDeleteDialog.value = false;
            companyToDelete.value = null;
        },
    });
};



// DataTable columns configuration
const { buildColumns } = useCrudColumns<Company>();

const columns: ColumnDef<Company>[] = buildColumns(
    [
        {
            key: 'name',
            header: 'Nombre',
            sortable: true,
            type: 'text',
            class: 'font-medium',
        },
        {
            key: 'legal_name',
            header: 'Razón Social',
            type: 'text',
            formatter: (value) => value || '-',
        },
        {
            key: 'nit',
            header: 'NIT',
            type: 'text',
        },
        {
            key: 'active',
            header: 'Estado',
            type: 'custom',
            render: (value) => h(Badge, {
                variant: value ? 'default' : 'secondary'
            }, () => value ? 'Activo' : 'Inactivo'),
        },
        {
            key: 'created_at',
            header: 'Creado',
            type: 'date',
            formatter: formatDate,
        },
    ],
    {
        editPermission: 'companies.edit',
        deletePermission: 'companies.delete',
        additionalActions: [
            
        ],
        onEdit: (company: Company) => {
            router.visit(CompanyController.edit.url(company.id));
        },
        onDelete: (id: string) => {
            const company = props.companies.data.find(c => c.id.toString() === id);
            if (company) {
                confirmDelete(company);
            }
        },
    },
    false
);

</script>

<template>
    <AppLayout title="Empresas" :breadcrumbs="breadcrumbItems">
        <div class="space-y-6 p-6">
            <DataTable
                :data="companies.data"
                :columns="columns"
                :pagination="companies.meta"
                :route-path="CompanyController.index.url()"
                :filters="filters"
                search-placeholder="Buscar empresas..."
                @selection-change="(v) => { selectedCompanies = v }"
            
            >
                <!-- Custom filters slot -->
                <template #filters>
                    <Popover v-model:open="showFiltersPopover">
                        <PopoverTrigger as-child>
                            <Button variant="outline" size="sm" class="relative">
                                <ListFilter class="mr-2 size-4" />
                                Filtros
                                <Badge
                                    v-if="hasActiveFilters"
                                    variant="default"
                                    class="ml-2 h-5 w-5 rounded-full p-0 flex items-center justify-center"
                                >
                                    {{ activeFiltersCount }}
                                </Badge>
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-80" align="start">
                            <div class="space-y-4">
                                <div class="space-y-2">
                                    <h4 class="font-medium text-sm flex items-center gap-2">
                                        <ListFilter class="size-4" />
                                        Filtrar Empresas
                                    </h4>
                                    <p class="text-xs text-muted-foreground">
                                        Aplica filtros para refinar los resultados
                                    </p>
                                </div>

                                <div class="space-y-4">
                                    <!-- Filter by NIT -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium flex items-center gap-2">
                                            <FileText class="size-4 text-muted-foreground" />
                                            NIT
                                        </label>
                                        <Input
                                            v-model="searchNit"
                                            placeholder="Buscar por NIT..."
                                        />
                                    </div>

                                    <!-- Filter by Status -->
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <label class="text-sm font-medium flex items-center gap-2">
                                                <Filter class="size-4 text-muted-foreground" />
                                                Estado
                                            </label>
                                            <Button
                                                v-if="filterActive"
                                                variant="ghost"
                                                size="sm"
                                                @click="filterActive = undefined"
                                                class="h-auto p-1 text-xs"
                                            >
                                                <X class="size-3 mr-1" />
                                                Limpiar
                                            </Button>
                                        </div>
                                        <Select v-model="filterActive">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Todos los estados" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectItem value="1">
                                                        <div class="flex items-center gap-2">
                                                            <CircleCheck class="size-4 text-green-600" />
                                                            <span>Activos</span>
                                                        </div>
                                                    </SelectItem>
                                                    <SelectItem value="0">
                                                        <div class="flex items-center gap-2">
                                                            <CircleX class="size-4 text-muted-foreground" />
                                                            <span>Inactivos</span>
                                                        </div>
                                                    </SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2 pt-2 border-t">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        @click="clearFilters"
                                        class="flex-1"
                                        :disabled="!hasActiveFilters"
                                    >
                                        <X class="mr-2 size-4" />
                                        Limpiar
                                    </Button>
                                    <Button
                                        size="sm"
                                        @click="applyFilters"
                                        class="flex-1"
                                    >
                                        <Filter class="mr-2 size-4" />
                                        Aplicar
                                    </Button>
                                </div>
                            </div>
                        </PopoverContent>
                    </Popover>
                </template>

                <!-- Actions slot -->
                <template #actions>
                    <Button as-child>
                        <Link :href="CompanyController.create.url()">
                            <Plus class="mr-2 size-4" />
                            Nueva Empresa
                        </Link>
                    </Button>
                </template>
            </DataTable>
        </div>

        <!-- Delete Confirmation Dialog -->
        <AlertDialog v-model:open="showDeleteDialog">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>¿Está seguro?</AlertDialogTitle>
                    <AlertDialogDescription>
                        Esto eliminará la empresa "{{ companyToDelete?.name }}".
                        Esta acción no se puede deshacer.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancelar</AlertDialogCancel>
                    <AlertDialogAction @click="deleteCompany">
                        Eliminar
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AppLayout>
</template>
