<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Checkbox } from '@/components/ui/checkbox'
import { cn } from '@/lib/utils'

interface ProductFormData {
  name: string
  description?: string
  price: number
  stock: number
  is_active: boolean
}

interface Props {
  form: {
    data: ProductFormData
    errors: Record<string, string>
    processing: boolean
  }
  isEditing: boolean
}

defineProps<Props>()

const emit = defineEmits<{
  submit: []
}>()

const handleSubmit = (e: Event) => {
  e.preventDefault()
  emit('submit')
}
</script>

<template>
  <form @submit="handleSubmit">
    <Card>
      <CardHeader>
        <CardTitle>{{ isEditing ? 'Edit' : 'Create' }} Product</CardTitle>
      </CardHeader>

      <CardContent class="space-y-4">
        <div>
          <Label for="name">Name</Label>
          <Input
            id="name"
            v-model="form.data.name"
            type="text"
            :class="cn(form.errors.name && 'border-destructive')"
          />
          <p v-if="form.errors.name" class="text-sm text-destructive">
            {{ form.errors.name }}
          </p>
        </div>

        <div>
          <Label for="description">Description</Label>
          <Textarea
            id="description"
            v-model="form.data.description"
            :class="cn(form.errors.description && 'border-destructive')"
          />
          <p v-if="form.errors.description" class="text-sm text-destructive">
            {{ form.errors.description }}
          </p>
        </div>

        <div>
          <Label for="price">Price</Label>
          <Input
            id="price"
            v-model="form.data.price"
            type="text"
            :class="cn(form.errors.price && 'border-destructive')"
          />
          <p v-if="form.errors.price" class="text-sm text-destructive">
            {{ form.errors.price }}
          </p>
        </div>

        <div>
          <Label for="stock">Stock</Label>
          <Input
            id="stock"
            v-model="form.data.stock"
            type="text"
            :class="cn(form.errors.stock && 'border-destructive')"
          />
          <p v-if="form.errors.stock" class="text-sm text-destructive">
            {{ form.errors.stock }}
          </p>
        </div>

        <div class="flex items-center space-x-2">
          <Checkbox
            id="is_active"
            :checked="form.data.is_active"
            @update:checked="form.data.is_active = $event"
          />
          <Label for="is_active">Is Active</Label>
          <p v-if="form.errors.is_active" class="text-sm text-destructive">
            {{ form.errors.is_active }}
          </p>
        </div>
      </CardContent>

      <CardFooter class="flex justify-between">
        <Button variant="outline" as-child>
          <Link :href="route('products.index')">
            Cancel
          </Link>
        </Button>
        <Button type="submit" :disabled="form.processing">
          {{ isEditing ? 'Update' : 'Create' }}
        </Button>
      </CardFooter>
    </Card>
  </form>
</template>
