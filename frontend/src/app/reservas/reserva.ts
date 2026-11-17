export type EstadoReserva = 'confirmada' | 'cancelada_a_tiempo' | 'cancelada_tarde';

export function textoDeEstado(estado: EstadoReserva): string {
  switch (estado) {
    case 'confirmada':
      return 'Confirmada';
    case 'cancelada_a_tiempo':
      return 'Cancelada';
    case 'cancelada_tarde':
      return 'Cancelada (tarde)';
  }
}

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
