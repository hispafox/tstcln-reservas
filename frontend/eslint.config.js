// @ts-check
const { defineConfig } = require('eslint/config');
const tseslint = require('typescript-eslint');
const angular = require('angular-eslint');

// Solo los límites entre funcionalidades y la regla del dominio.
// Los conjuntos de reglas recomendadas de ESLint, typescript-eslint y Angular llegan más adelante.
module.exports = defineConfig([
  {
    files: ['**/*.ts'],
    extends: [tseslint.configs.base],
  },
  {
    files: ['**/*.html'],
    languageOptions: { parser: angular.templateParser },
  },
  {
    files: ['src/app/salas/**/*.ts'],
    rules: {
      'no-restricted-imports': ['error', {
        patterns: [{
          group: ['**/reservas/**', '**/ocupacion/**'],
          message: 'Desde salas, de otra funcionalidad solo se importa su index.ts.',
        }],
      }],
    },
  },
  {
    files: ['src/app/reservas/**/*.ts'],
    rules: {
      'no-restricted-imports': ['error', {
        patterns: [{
          group: ['**/salas/**', '**/ocupacion/**'],
          message: 'Desde reservas, de otra funcionalidad solo se importa su index.ts.',
        }],
      }],
    },
  },
  {
    files: ['src/app/ocupacion/**/*.ts'],
    rules: {
      'no-restricted-imports': ['error', {
        patterns: [{
          group: ['**/salas/**', '**/reservas/**'],
          message: 'Desde ocupación, de otra funcionalidad solo se importa su index.ts.',
        }],
      }],
    },
  },
  {
    files: ['src/app/compartido/**/*.ts'],
    rules: {
      'no-restricted-imports': ['error', {
        patterns: [{
          group: ['**/salas', '**/salas/**', '**/reservas', '**/reservas/**', '**/ocupacion', '**/ocupacion/**'],
          message: 'Lo compartido no importa nada de ninguna funcionalidad.',
        }],
      }],
    },
  },
  {
    files: ['src/app/*/dominio/**/*.ts'],
    rules: {
      'no-restricted-imports': ['error', {
        patterns: [{
          group: ['@angular/*', 'rxjs', 'rxjs/*'],
          message: 'El dominio es TypeScript sin Angular: aquí no entran Angular ni RxJS.',
        }],
      }],
    },
  },
]);
