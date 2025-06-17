import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
export default defineConfig({
    plugins: [laravel({
        input: [
          'Modules/BillingManager/Resources/js/front.js',
          'Modules/BillingManager/Resources/js/admin.js',
          'Modules/BillingManager/Resources/sass/front.scss',
          'Modules/BillingManager/Resources/sass/admin.scss',
        ],
        refresh: true,
    })],
    publicDir: 'public/modules/billingmanager',
});
