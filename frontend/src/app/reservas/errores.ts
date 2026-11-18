export class ReservaRechazadaError extends Error {
  constructor(
    message: string,
    readonly codigo?: string,
  ) {
    super(message);
  }
}
export class SinConexionError extends Error {}
export class DatosNoValidosError extends Error {}
