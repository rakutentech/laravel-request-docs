import { defineConfig } from 'astro/config';
import compress from "astro-compress";
import react from '@astrojs/react';

export default defineConfig({
  integrations: [
    compress(),
    react()
  ],
  outDir: '../resources/dist',
  base: '/request-docs',
});
