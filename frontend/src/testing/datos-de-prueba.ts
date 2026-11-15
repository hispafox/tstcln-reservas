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

export const salaNorte = sala({ nombre: 'Norte', capacidadMaxima: 4 });
export const salaSur = sala({ nombre: 'Sur', capacidadMaxima: 10, equipamiento: ['pantalla'] });

/** Los nombres de una lista de salas, para que la aserción se lea de un vistazo. */
export function nombres(salas: Sala[]): string[] {
  return salas.map((s) => s.nombre);
}
