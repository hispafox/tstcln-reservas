import { Sala } from './sala';

export interface CriterioBusqueda {
  asistentes: number;
  equipamiento: string[];
}

export function filtrarSalasDisponibles(
  salas: Sala[],
  criterio: CriterioBusqueda,
): Sala[] {
  return salas.filter(
    (sala) =>
      criterio.asistentes >= (sala.capacidadMinima ?? 1) &&
      criterio.asistentes <= sala.capacidadMaxima &&
      criterio.equipamiento.every((cosa) =>
        sala.equipamiento.includes(cosa),
      ),
  );
}
