export interface Sala {
  id: number;
  nombre: string;
  capacidadMaxima: number;
  capacidadMinima: number | null;
  equipamiento: string[];
}
