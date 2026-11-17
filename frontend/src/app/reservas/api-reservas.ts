import { HttpClient, HttpErrorResponse } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable, catchError, throwError } from 'rxjs';
import { DatosNoValidosError, ReservaRechazadaError, SinConexionError } from './errores';
import { PeticionReserva, Reserva } from './reserva';

@Injectable({ providedIn: 'root' })
export class ApiReservas {
  private readonly http = inject(HttpClient);

  crear(peticion: PeticionReserva): Observable<Reserva> {
    return this.http.post<Reserva>('/reservas', peticion).pipe(
      catchError((error: HttpErrorResponse) =>
        throwError(() => traducir(error)),
      ),
    );
  }
}

function traducir(error: HttpErrorResponse): unknown {
  if (error.status === 409) {
    return new ReservaRechazadaError(
      error.error?.error ?? 'La reserva no se ha podido hacer',
    );
  }
  if (error.status === 400) {
    return new DatosNoValidosError(
      error.error?.error ?? 'Los datos de la reserva no son válidos',
    );
  }
  if (error.status === 0) return new SinConexionError();
  return error;
}
