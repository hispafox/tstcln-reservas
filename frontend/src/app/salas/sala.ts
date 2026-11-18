export interface Sala {
  id: number;
  nombre: string;
  capacidadMaxima: number;
  capacidadMinima: number | null;
  equipamiento: string[];
}

export function admite(sala: Sala, asistentes: number): boolean {
  return asistentes >= (sala.capacidadMinima ?? 1) && asistentes <= sala.capacidadMaxima;
}
