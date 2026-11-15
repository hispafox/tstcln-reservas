import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { NuevaReserva, Reserva } from '../modelos/reserva';

@Injectable({ providedIn: 'root' })
export class ServicioReservas {
  private readonly http = inject(HttpClient);

  crear(reserva: NuevaReserva): Observable<Reserva> {
    return this.http.post<Reserva>('/reservas', reserva);
  }
}
