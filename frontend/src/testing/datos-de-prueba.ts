import { Sala } from '../app/salas/sala';

let siguienteId = 1;

/** Una sala con valores por defecto; solo cambia lo que se le pasa. */
export function sala(cambios: Partial<Sala> = {}): Sala {
  return {
    id: siguienteId++,
    nombre: 'Sala',
    capacidadMaxima: 10,
    capacidadMinima: null,
    equipamiento: [],
    ...cambios,
  };
}

/** Los nombres de una lista de salas, para que la aserción se lea de un vistazo. */
export function nombres(salas: Sala[]): string[] {
  return salas.map((s) => s.nombre);
}
