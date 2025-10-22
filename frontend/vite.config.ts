import { reactRouter } from "@react-router/dev/vite";
import tailwindcss from "@tailwindcss/vite";
import { defineConfig } from "vite";
import tsconfigPaths from "vite-tsconfig-paths";

export default defineConfig({
  plugins: [tailwindcss(), reactRouter(), tsconfigPaths()],
  server: {
    host: true,
    port: 5173,
    proxy: {
      "/api": "http://127.0.0.1:8080", // 라라벨 개발 서버
    },
  },
});
