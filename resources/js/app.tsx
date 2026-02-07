import '../css/app.css';
import '../css/browser-fixes.css';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { initializeTheme } from './hooks/use-appearance';
import { RootProvider } from './providers/root-provider';
import axios from 'axios';
import React from 'react';

// Configure axios for Laravel Sanctum
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx', { eager: false })),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            <RootProvider>
                <App {...props} />
            </RootProvider>
        );
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on load...
initializeTheme();
