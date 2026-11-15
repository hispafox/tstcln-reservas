export function duracionEnMinutos(inicio: string, fin: string): number {
  return (new Date(fin).getTime() - new Date(inicio).getTime()) / 60_000;
}
