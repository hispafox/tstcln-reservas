import { Observable, of } from 'rxjs';
import { ApiReservas } from '../app/reservas/datos/api-reservas';
import { PeticionReserva, Reserva } from '../app/reservas/dominio/reserva';

export class ApiReservasEnMemoria implements ApiReservas {
  private readonly reservas: Reserva[];
  private readonly enEspera: PeticionReserva[] = [];
  private siguienteId: number;

  constructor(iniciales: Reserva[] = []) {
    this.reservas = [...iniciales];
    this.siguienteId = iniciales.length + 1;
  }

  crear(peticion: PeticionReserva): Observable<Reserva> {
    const reserva: Reserva = { ...peticion, id: this.siguienteId++, estado: 'confirmada' };
    this.reservas.push(reserva);
    return of(reserva);
  }

  deUnaPersona(persona: string): Observable<Reserva[]> {
    return of(
      this.reservas
        .filter((r) => r.persona === persona)
        .sort((a, b) => (a.inicio < b.inicio ? -1 : 1)),
    );
  }

  cancelar(id: number): Observable<Reserva> {
    const indice = this.reservas.findIndex((r) => r.id === id);
    const cancelada: Reserva = { ...this.reservas[indice], estado: 'cancelada_a_tiempo' };
    this.reservas[indice] = cancelada;
    return of(cancelada);
  }

  apuntarEnEspera(peticion: PeticionReserva): Observable<number> {
    this.enEspera.push(peticion);
    return of(
      this.enEspera.filter(
        (p) => p.sala === peticion.sala && p.inicio === peticion.inicio && p.fin === peticion.fin,
      ).length,
    );
  }
}
