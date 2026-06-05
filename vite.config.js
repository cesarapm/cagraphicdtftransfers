import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const port = Number(env.VITE_PORT || 5173);
    const hmrHost = env.VITE_HMR_HOST || '192.168.1.50';
    const hmrProtocol = env.VITE_HMR_PROTOCOL || (hmrHost === '192.168.1.50' ? 'ws' : 'wss');
    const hmrClientPort = Number(env.VITE_HMR_CLIENT_PORT || (hmrProtocol === 'wss' ? 443 : port));

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
            vue(),
        ],
        server: {
            host: '0.0.0.0',
            port,
            strictPort: true,
            cors: true,
            allowedHosts: hmrHost === '192.168.1.50' ? [] : [hmrHost],
            origin: env.VITE_DEV_SERVER_URL || undefined,
            hmr: {
                host: hmrHost,
                protocol: hmrProtocol,
                clientPort: hmrClientPort,
            },
        },
    };
});
