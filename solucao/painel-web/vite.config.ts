import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";

// Configuração do Vite — nome do arquivo é um contrato da ferramenta (Vite procura
// "vite.config.ts" por padrão), não pode ser renomeado sem flag --config manual.
export default defineConfig({
  plugins: [react()],
  server: {
    host: true, // permite acesso via IP local (teste em celular na mesma rede)
    proxy: {
      "/api": "http://127.0.0.1:8000",
    },
  },
});
