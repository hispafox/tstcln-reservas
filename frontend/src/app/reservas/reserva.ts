import { FranjaHoraria } from './franja-horaria';

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

export function estaPendiente(reserva: Reserva, ahora: Date): boolean {
  return reserva.estado === 'confirmada' && !FranjaHoraria.desde(reserva.inicio, reserva.fin).haEmpezado(ahora);
}

export interface PeticionReserva {
  sala: number;
  inicio: string;
  fin: string;
  persona: string;
  asistentes: number;
}
