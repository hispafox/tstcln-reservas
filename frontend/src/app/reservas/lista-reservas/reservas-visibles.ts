import { FilaReserva } from '../tabla-reservas/tabla-reservas';

export function soloLasProximas(reservas: FilaReserva[]): FilaReserva[] {
  return reservas.filter((reserva) => reserva.pendiente);
}

export function ordenar(reservas: FilaReserva[], orden: string): FilaReserva[] {
  if (orden == 'fecha') {
    return [...reservas].sort((a, b) => (a.inicio < b.inicio ? -1 : 1));
  } else if (orden == 'sala') {
    return [...reservas].sort((a, b) => a.nombreSala.localeCompare(b.nombreSala));
  } else if (orden == 'duracion') {
    return [...reservas].sort((a, b) => b.duracion - a.duracion);
  }
  return [...reservas];
}
