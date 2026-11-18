export function soloLasProximas(reservas: any[]): any[] {
  return reservas.filter((reserva) => reserva.pendiente);
}

export function ordenar(reservas: any[], orden: string): any[] {
  if (orden == 'fecha') {
    return [...reservas].sort((a, b) => (a.inicio < b.inicio ? -1 : 1));
  } else if (orden == 'sala') {
    return [...reservas].sort((a, b) => a.nombreSala.localeCompare(b.nombreSala));
  } else if (orden == 'duracion') {
    return [...reservas].sort((a, b) => b.duracion - a.duracion);
  }
  return [...reservas];
}
