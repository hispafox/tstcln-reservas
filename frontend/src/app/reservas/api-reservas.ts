import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { PeticionReserva, Reserva } from './reserva';

@Injectable({ providedIn: 'root' })
export class ApiReservas {
  private readonly http = inject(HttpClient);

  crear(peticion: PeticionReserva): Observable<Reserva> {
    return this.http.post<Reserva>('/reservas', peticion);
  }
}
