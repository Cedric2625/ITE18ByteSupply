# ByteSupply Frontend (Nuxt.js)

This is the Nuxt.js frontend for the ByteSupply application.

## Setup

1. Install dependencies:
```bash
npm install
```

2. Create `.env` file:
```env
API_BASE_URL=http://127.0.0.1:8001/api
```

3. Run development server:
```bash
npm run dev
```

The frontend will be available at `http://localhost:3000`

## Project Structure

```
frontend/
├── assets/          # CSS, images, etc.
├── components/      # Vue components
├── composables/     # Composable functions (useApi, useAuth)
├── layouts/         # Layout components
├── middleware/      # Route middleware
├── pages/           # Pages (auto-routed)
├── plugins/         # Nuxt plugins
├── stores/          # Pinia stores
└── nuxt.config.ts   # Nuxt configuration
```

## Key Features

- **Authentication**: Laravel Sanctum token-based auth
- **API Communication**: Centralized API service with interceptors
- **State Management**: Pinia stores
- **Styling**: Tailwind CSS
- **Type Safety**: TypeScript support

## Migration from Blade

### Blade → Nuxt Mapping:

| Blade View | Nuxt Page |
|------------|-----------|
| `resources/views/layouts/app.blade.php` | `layouts/default.vue` |
| `resources/views/auth/login.blade.php` | `pages/auth/login.vue` |
| `resources/views/categories/index.blade.php` | `pages/categories/index.vue` |
| `resources/views/categories/show.blade.php` | `pages/categories/[id].vue` |
| `resources/views/categories/create.blade.php` | `pages/categories/create.vue` |
| `resources/views/categories/edit.blade.php` | `pages/categories/[id]/edit.vue` |

### API Calls

Instead of Laravel's `route()` helper, use the API composable:

```vue
<script setup>
const { api } = useApi()

// GET request
const { data } = await useAsyncData('categories', () =>
  api('/categories', { method: 'GET' })
)

// POST request
const result = await api('/categories', {
  method: 'POST',
  body: { category_name: 'New Category' }
})
</script>
```

## Next Steps

1. Migrate remaining Blade views to Nuxt pages
2. Create reusable components for common UI elements
3. Add form validation
4. Implement error handling
5. Add loading states
6. Set up testing

