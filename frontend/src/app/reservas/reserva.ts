export type EstadoReserva = 'confirmada' | 'cancelada_a_tiempo' | 'cancelada_tarde';

export interface Reserva {
  id: number;
  sala: number;
  inicio: string;
  fin: string;
  persona: string;
  asistentes: number;
  estado: EstadoReserva;
}

export interface PeticionReserva {
  sala: number;
  inicio: string;
  fin: string;
  persona: string;
  asistentes: number;
}
