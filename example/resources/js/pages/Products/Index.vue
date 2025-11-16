<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog'

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
  products: {
    data: Product[]
    meta: {
      current_page: number
      last_page: number
      per_page: number
      total: number
    }
  }
}

const props = defineProps<Props>()

const search = ref('')
const deleteDialogOpen = ref(false)
const itemToDelete = ref<Product | null>(null)

const handleSearch = () => {
  router.get(route('products.index'), { search: search.value }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const confirmDelete = (item: Product) => {
  itemToDelete.value = item
  deleteDialogOpen.value = true
}

const handleDelete = () => {
  if (itemToDelete.value) {
    router.delete(route('products.destroy', itemToDelete.value.id), {
      onSuccess: () => {
        deleteDialogOpen.value = false
        itemToDelete.value = null
      },
    })
  }
}
</script>

<template>
  <Head title="Products" />

  <div class="container mx-auto py-10">
    <div class="mb-6 flex items-center justify-between">
      <h1 class="text-3xl font-bold">Products</h1>
      <Button as-child>
        <Link :href="route('products.create')">
          Create Product
        </Link>
      </Button>
    </div>

    <div class="mb-4">
      <Input
        v-model="search"
        type="text"
        placeholder="Search..."
        @input="handleSearch"
        class="max-w-sm"
      />
    </div>

    <div class="rounded-md border">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead>ID</TableHead>
            <TableHead>Name</TableHead>
            <TableHead>Description</TableHead>
            <TableHead>Price</TableHead>
            <TableHead>Stock</TableHead>
            <TableHead>Is Active</TableHead>
            <TableHead>Created</TableHead>
            <TableHead class="text-right">Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="item in props.products.data" :key="item.id">
            <TableCell>{{ item.id }}</TableCell>
            <TableCell>{{ item.name }}</TableCell>
            <TableCell>{{ item.description }}</TableCell>
            <TableCell>{{ item.price }}</TableCell>
            <TableCell>{{ item.stock }}</TableCell>
            <TableCell>{{ item.is_active ? 'Yes' : 'No' }}</TableCell>
            <TableCell>{{ new Date(item.created_at).toLocaleDateString() }}</TableCell>
            <TableCell class="text-right">
              <div class="flex justify-end gap-2">
                <Button variant="outline" size="sm" as-child>
                  <Link :href="route('products.edit', item.id)">
                    Edit
                  </Link>
                </Button>
                <Button
                  variant="destructive"
                  size="sm"
                  @click="confirmDelete(item)"
                >
                  Delete
                </Button>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>

    <AlertDialog :open="deleteDialogOpen" @update:open="deleteDialogOpen = $event">
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Are you sure?</AlertDialogTitle>
          <AlertDialogDescription>
            This action cannot be undone. This will permanently delete the product.
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>Cancel</AlertDialogCancel>
          <AlertDialogAction @click="handleDelete">Delete</AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  </div>
</template>
