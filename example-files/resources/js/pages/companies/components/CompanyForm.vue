<script setup lang="ts">
import { Link, Form } from '@inertiajs/vue3';
import ImageUpload from '@/components/ImageUpload.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import {
    Building2,
    FileText,
    Hash,
    ToggleLeft,
    Save,
    X,
} from 'lucide-vue-next';
import { Company } from '@/types';
import { FormField } from '@/components/ui/form';

interface Props {
    formProps: any;
    company?: Company;
    submitLabel?: string;
    processingLabel?: string;
    cancelUrl?: string;
}

const props = withDefaults(defineProps<Props>(), {
    submitLabel: 'Crear Empresa',
    processingLabel: 'Creando...',
});
</script>

<template>
    <Form
        v-bind="formProps"
        class="space-y-6"
        enctype="multipart/form-data"
    >
        <template #default="{ errors, processing, recentlySuccessful }: any">
            <!-- Two-column layout for desktop -->
            <div class="grid gap-6 lg:grid-cols-[1fr_400px]">
                <!-- Left Column: Form Fields -->
                <div class="space-y-6">
                    <!-- Basic Information Card -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Building2 class="size-5" />
                                Información Básica
                            </CardTitle>
                            <CardDescription>
                                Datos principales de identificación de la empresa
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <!-- Company Name -->
                                <div class="space-y-2 sm:col-span-2">
                                    <Label for="name" class="required flex items-center gap-2">
                                        <Building2 class="size-4 text-muted-foreground" />
                                        Nombre de la Empresa
                                    </Label>
                                    <Input
                                        id="name"
                                        name="name"
                                        type="text"
                                        required
                                        autocomplete="organization"
                                        placeholder="Ej: Mi Empresa S.A."
                                        :model-value="company?.name"
                                    />
                                    <InputError :message="errors.name" />
                                </div>

                                <!-- NIT -->
                                <div class="space-y-2">
                                    <Label for="nit" class="required flex items-center gap-2">
                                        <Hash class="size-4 text-muted-foreground" />
                                        NIT
                                    </Label>
                                    <Input
                                        id="nit"
                                        name="nit"
                                        type="text"
                                        required
                                        placeholder="900123456-7"
                                        :model-value="company?.nit"
                                    />
                                    <InputError :message="errors.nit" />
                                    <p class="text-xs text-muted-foreground">
                                        Número único de identificación
                                    </p>
                                </div>

                                <!-- Legal Name -->
                                <div class="space-y-2">
                                    <Label for="legal_name" class="flex items-center gap-2">
                                        <FileText class="size-4 text-muted-foreground" />
                                        Razón Social
                                    </Label>
                                    <Input
                                        id="legal_name"
                                        name="legal_name"
                                        type="text"
                                        placeholder="Razón social completa (opcional)"
                                        :model-value="company?.legal_name ?? undefined"
                                    />
                                    <InputError :message="errors.legal_name" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Configuration Card -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <ToggleLeft class="size-5" />
                                Configuración
                            </CardTitle>
                            <CardDescription>
                                Opciones de estado y disponibilidad
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="flex items-center justify-between rounded-lg border bg-muted/30 p-4">
                                <div class="space-y-1">
                                    <Label for="active" class="flex items-center gap-2 text-base">
                                        <ToggleLeft class="size-4 text-muted-foreground" />
                                        Empresa Activa
                                    </Label>
                                    <p class="text-sm text-muted-foreground">
                                        La empresa estará disponible para usar en el sistema
                                    </p>
                                </div>
                                <FormField 
                                    :model-value="company?.active ?? true"
                                    @update:model-value="(value) => { if (company) company.active = value }"
                                    v-slot="{ value, handleChange }" 
                                    name="active"
                                >
                                    <!-- Hidden input to submit boolean value -->
                                    <input type="hidden" name="active" :value="value ? '1' : '0'" />
                                    <Switch
                                        id="active"
                                        :model-value="value"
                                        @update:model-value="handleChange"
                                    />
                                </FormField>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right Column: Logo Upload -->
                <div class="space-y-6">
                    <Card class="lg:sticky lg:top-6">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                Logo de la Empresa
                            </CardTitle>
                            <CardDescription>
                                Imagen representativa de la empresa
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <ImageUpload
                                name="logo"
                                :error="errors.logo"
                                :help-text="[
                                    'Formatos: PNG, JPG, WEBP',
                                    'Tamaño máximo: 2MB',
                                    'Dimensión recomendada: 512x512px'
                                ]"
                            />
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Form Actions - Full width -->
            <Card>
                <CardContent class="pt-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <Button
                            v-if="cancelUrl"
                            type="button"
                            variant="outline"
                            size="lg"
                            as-child
                        >
                            <Link :href="cancelUrl">
                                <X class="mr-2 size-4" />
                                Cancelar
                            </Link>
                        </Button>

                        <div class="flex items-center gap-3">
                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p
                                    v-show="recentlySuccessful"
                                    class="text-sm text-green-600 dark:text-green-400"
                                >
                                    ✓ Guardado
                                </p>
                            </Transition>
                            <Button
                                type="submit"
                                size="lg"
                                :disabled="processing"
                            >
                                <Save class="mr-2 size-4" />
                                {{ processing ? processingLabel : submitLabel }}
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </template>
    </Form>
</template>

<style scoped>
.required::after {
    content: ' *';
    color: hsl(var(--destructive));
}
</style>
