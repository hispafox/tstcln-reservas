import { Sala } from '../modelos/sala';

export interface FiltroSalas {
  asistentes: number | null;
  equipamiento: string[];
}

export function filtrarSalas(salas: Sala[], filtro: FiltroSalas): Sala[] {
  return salas.filter((sala) => {
    if (filtro.asistentes !== null) {
      if (filtro.asistentes > sala.capacidadMaxima) {
        return false;
      }
      if (sala.capacidadMinima !== null && filtro.asistentes < sala.capacidadMinima) {
        return false;
      }
    }

    return filtro.equipamiento.every((elemento) => sala.equipamiento.includes(elemento));
  });
}
