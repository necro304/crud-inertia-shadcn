<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import ProductForm from './Form.vue'

interface ProductFormData {
  name: string
  description?: string
  price: number
  stock: number
  is_active: boolean
}

const form = useForm<ProductFormData>({
  name: '',
  description: '',
  price: 0,
  stock: 0,
  is_active: false
})

const submit = () => {
  form.post(route('products.store'))
}
</script>

<template>
  <Head title="Create Product" />

  <div class="container mx-auto py-10">
    <div class="mb-6">
      <h1 class="text-3xl font-bold">Create Product</h1>
    </div>

    <ProductForm
      :form="form"
      :is-editing="false"
      @submit="submit"
    />
  </div>
</template>
