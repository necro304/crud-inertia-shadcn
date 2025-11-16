<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import ProductForm from './Form.vue'

interface Product {
  id: number
  name: string
  description?: string
  price: number
  stock: number
  is_active: boolean
  created_at: string
  updated_at: string
}

interface Props {
  product: Product
}

const props = defineProps<Props>()

interface ProductFormData {
  name: string
  description?: string
  price: number
  stock: number
  is_active: boolean
}

const form = useForm<ProductFormData>({
  name: props.product.name,
  description: props.product.description,
  price: props.product.price,
  stock: props.product.stock,
  is_active: props.product.is_active
})

const submit = () => {
  form.put(route('products.update', props.product.id))
}
</script>

<template>
  <Head title="Edit Product" />

  <div class="container mx-auto py-10">
    <div class="mb-6">
      <h1 class="text-3xl font-bold">Edit Product</h1>
    </div>

    <ProductForm
      :form="form"
      :is-editing="true"
      @submit="submit"
    />
  </div>
</template>
