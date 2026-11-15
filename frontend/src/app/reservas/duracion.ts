export function duracionEnMinutos(inicio: string, fin: string): number {
  return (Date.parse(fin) - Date.parse(inicio)) / 60_000;
}

export function textoDeDuracion(minutos: number): string {
  const horas = Math.floor(minutos / 60);
  const resto = minutos % 60;
  if (horas === 0) return `${resto} min`;
  if (resto === 0) return `${horas} h`;
  return `${horas} h ${resto} min`;
}
