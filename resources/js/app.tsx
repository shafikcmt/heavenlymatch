import '../css/app.css'

import { createInertiaApp } from '@inertiajs/react'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createRoot } from 'react-dom/client'

const appName = (window as unknown as Record<string, unknown>)['appName'] as string || 'HeavenlyMatch'

createInertiaApp({
  title: (title) => title ? `${title} — ${appName}` : appName,

  resolve: (name) =>
    resolvePageComponent(
      `./pages/${name}.tsx`,
      import.meta.glob('./pages/**/*.tsx'),
    ),

  setup({ el, App, props }) {
    createRoot(el).render(<App {...props} />)
  },

  progress: {
    color: '#2563eb',
    showSpinner: true,
  },
})
