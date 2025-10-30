import tseslint from "@typescript-eslint/eslint-plugin";
import tsparser from "@typescript-eslint/parser";
import react from "eslint-plugin-react";
import reactHooks from "eslint-plugin-react-hooks";

export default [
  { ignores: ["build/**", "dist/**", "node_modules/**"] },
  {
    files: ["**/*.{ts,tsx,js,jsx}"],
    languageOptions: { parser: tsparser, ecmaVersion: "latest", sourceType: "module" },
    plugins: { "@typescript-eslint": tseslint, react, "react-hooks": reactHooks },
    rules: {
      "react-hooks/rules-of-hooks": "error",
      "react-hooks/exhaustive-deps": "warn",
    },
  },
];
