import { Observable } from 'rxjs';
import { PeticionReserva, Reserva } from './reserva';

export abstract class ApiReservas {
  abstract crear(peticion: PeticionReserva): Observable<Reserva>;
  abstract deUnaPersona(persona: string): Observable<Reserva[]>;
  abstract cancelar(id: number): Observable<Reserva>;
}
