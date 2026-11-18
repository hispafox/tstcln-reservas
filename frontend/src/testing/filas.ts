import { screen } from '@testing-library/angular';

/** El texto de cada fila de la tabla, sin la cabecera. */
export function filas(): string[] {
  return screen.getAllByRole('row').slice(1).map((fila) => fila.textContent!.replace(/\s+/g, ' ').trim());
}
