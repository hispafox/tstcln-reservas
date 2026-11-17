export class Capacidad {
  private constructor(readonly valor: number) {}

  static leer(texto: string): Capacidad | null {
    const valor = Number(texto);
    return Number.isInteger(valor) && valor >= 1 ? new Capacidad(valor) : null;
  }
}
