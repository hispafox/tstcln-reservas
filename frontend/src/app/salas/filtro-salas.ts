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
      criterio.equipamiento.every((elemento) =>
        sala.equipamiento.includes(elemento),
      ),
  );
}

export function conNombre(salas: Sala[], texto: string): Sala[] {
  const buscado = texto.trim().toLowerCase();
  return salas.filter((sala) => sala.nombre.toLowerCase().includes(buscado));
}
