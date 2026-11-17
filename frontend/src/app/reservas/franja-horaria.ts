export class FranjaHoraria {
  private constructor(
    private readonly inicio: number,
    private readonly fin: number,
  ) {}

  static desde(inicio: string, fin: string): FranjaHoraria {
    const desde = Date.parse(inicio);
    const hasta = Date.parse(fin);
    if (!(hasta > desde)) {
      throw new RangeError('Una franja tiene que terminar después de empezar');
    }
    return new FranjaHoraria(desde, hasta);
  }

  minutos(): number {
    return (this.fin - this.inicio) / 60_000;
  }

  haEmpezado(ahora: Date): boolean {
    return this.inicio < ahora.getTime();
  }

  seSolapaCon(otra: FranjaHoraria): boolean {
    return this.inicio < otra.fin && otra.inicio < this.fin;
  }
}
