import '../css/app.css'

import { createInertiaApp, router } from '@inertiajs/react'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createRoot } from 'react-dom/client'
import { route } from 'ziggy-js'

const appName = (window as unknown as Record<string, unknown>)['appName'] as string || 'HeavenlyMatch'

createInertiaApp({
  title: (title) => title ? `${title} — ${appName}` : appName,

  resolve: (name) =>
    resolvePageComponent(
      `./pages/${name}.tsx`,
      import.meta.glob('./pages/**/*.tsx'),
    ),

  setup({ el, App, props }) {
    // Seed Ziggy with the server-provided route config so route() works globally.
    // HandleInertiaRequests shares the full route table as props.ziggy on every request.
    ;(window as any).Ziggy = (props.initialPage.props as any).ziggy

    // Keep Ziggy's location in sync on each Inertia SPA navigation.
    router.on('navigate', (event) => {
      ;(window as any).Ziggy = (event.detail.page.props as any).ziggy ?? (window as any).Ziggy
    })

    // Expose route() as a global — matches the declare function route() in types/ziggy.d.ts
    ;(window as any).route = route

    createRoot(el).render(<App {...props} />)
  },

  progress: {
    color: '#2563eb',
    showSpinner: true,
  },
})
