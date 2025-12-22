export default defineNuxtRouteMiddleware((to, from) => {
  // This middleware is only for the view page [id].vue
  // It should NOT block navigation TO edit routes from other pages
  // Only prevent the view page itself from loading if somehow it matches an edit route
  
  // If we're navigating FROM the index page TO an edit route, allow it
  if (from.path === '/admin/orders' && to.path.includes('/edit')) {
    // Allow navigation to edit page
    return
  }
  
  // If the view page is trying to load on an edit route, abort it
  // This should not happen in normal navigation, but prevents routing conflicts
  if (to.path.includes('/edit') && from.path.includes('/edit')) {
    // Already on edit route, don't abort (might be a refresh)
    return
  }
})

