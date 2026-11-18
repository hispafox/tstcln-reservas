// @ts-check
const eslint = require('@eslint/js');
const { defineConfig } = require('eslint/config');
const tseslint = require('typescript-eslint');
const angular = require('angular-eslint');

// Primero, los conjuntos de reglas recomendadas de ESLint, typescript-eslint y Angular.
// Detrás, los límites entre funcionalidades y la regla del dominio.
module.exports = defineConfig([
  {
    files: ['**/*.ts'],
    extends: [
      eslint.configs.recommended,
      tseslint.configs.recommended,
      tseslint.configs.stylistic,
      angular.configs.tsRecommended,
    ],
    processor: angular.processInlineTemplates,
    rules: {
      '@angular-eslint/directive-selector': [
        'error',
        {
          type: 'attribute',
          prefix: 'app',
          style: 'camelCase',
        },
      ],
      '@angular-eslint/component-selector': [
        'error',
        {
          type: 'element',
          prefix: 'app',
          style: 'kebab-case',
        },
      ],
    },
  },
  {
    files: ['**/*.html'],
    extends: [angular.configs.templateRecommended, angular.configs.templateAccessibility],
    rules: {},
  },
  {
    files: ['src/app/salas/**/*.ts'],
    rules: {
      'no-restricted-imports': [
        'error',
        {
          patterns: [
            {
              group: ['**/reservas/**', '**/ocupacion/**'],
              message: 'Desde salas, de otra funcionalidad solo se importa su index.ts.',
            },
          ],
        },
      ],
    },
  },
  {
    files: ['src/app/reservas/**/*.ts'],
    rules: {
      'no-restricted-imports': [
        'error',
        {
          patterns: [
            {
              group: ['**/salas/**', '**/ocupacion/**'],
              message: 'Desde reservas, de otra funcionalidad solo se importa su index.ts.',
            },
          ],
        },
      ],
    },
  },
  {
    files: ['src/app/ocupacion/**/*.ts'],
    rules: {
      'no-restricted-imports': [
        'error',
        {
          patterns: [
            {
              group: ['**/salas/**', '**/reservas/**'],
              message: 'Desde ocupación, de otra funcionalidad solo se importa su index.ts.',
            },
          ],
        },
      ],
    },
  },
  {
    files: ['src/app/compartido/**/*.ts'],
    rules: {
      'no-restricted-imports': [
        'error',
        {
          patterns: [
            {
              group: [
                '**/salas',
                '**/salas/**',
                '**/reservas',
                '**/reservas/**',
                '**/ocupacion',
                '**/ocupacion/**',
              ],
              message: 'Lo compartido no importa nada de ninguna funcionalidad.',
            },
          ],
        },
      ],
    },
  },
  {
    files: ['src/app/*/dominio/**/*.ts'],
    rules: {
      'no-restricted-imports': [
        'error',
        {
          patterns: [
            {
              group: ['@angular/*', 'rxjs', 'rxjs/*'],
              message: 'El dominio es TypeScript sin Angular: aquí no entran Angular ni RxJS.',
            },
          ],
        },
      ],
    },
  },
  {
    files: ['src/app/*/dominio/**/*.ts'],
    rules: {
      'no-restricted-syntax': [
        'error',
        {
          selector: "NewExpression[callee.name='Date'][arguments.length=0]",
          message: 'El dominio no lee la hora: la recibe como parámetro.',
        },
        {
          selector: "CallExpression[callee.object.name='Date'][callee.property.name='now']",
          message: 'El dominio no lee la hora: la recibe como parámetro.',
        },
      ],
    },
  },
  // Las reglas recomendadas que el código de hoy aún no cumple, de error a aviso.
  // Va el último porque, cuando dos bloques dan valor a la misma regla, manda el último.
  // Cuando una regla deja de dar avisos, sale de aquí y vuelve a ser un error.
  {
    files: ['src/**/*.ts'],
    rules: {
      '@angular-eslint/no-input-rename': 'warn',
      '@typescript-eslint/no-explicit-any': 'warn',
    },
  },
]);
