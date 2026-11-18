import { Observable } from 'rxjs';
import { PeticionReserva, Reserva } from '../dominio/reserva';

export abstract class ApiReservas {
  abstract crear(peticion: PeticionReserva): Observable<Reserva>;
  abstract deUnaPersona(persona: string): Observable<Reserva[]>;
  abstract cancelar(id: number): Observable<Reserva>;
  abstract apuntarEnEspera(peticion: PeticionReserva): Observable<number>;
}
