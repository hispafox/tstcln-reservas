import { HttpClient, HttpErrorResponse, HttpParams } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable, catchError, map, throwError } from 'rxjs';
import { ApiReservas } from './api-reservas';
import { DatosNoValidosError, ReservaRechazadaError, SinConexionError } from '../errores';
import { PeticionReserva, Reserva, esEstadoReserva } from '../dominio/reserva';

@Injectable()
export class ApiReservasHttp implements ApiReservas {
  private readonly http = inject(HttpClient);

  crear(peticion: PeticionReserva): Observable<Reserva> {
    return this.http.post<ReservaJson>('/reservas', peticion).pipe(
      catchError((error: HttpErrorResponse) => throwError(() => traducir(error))),
      map(aReserva),
    );
  }

  deUnaPersona(persona: string): Observable<Reserva[]> {
    const parametros = new HttpParams().set('persona', persona);
    return this.http.get<ReservaJson[]>('/reservas', { params: parametros }).pipe(
      catchError((error: HttpErrorResponse) => throwError(() => traducir(error))),
      map((lista) => lista.map(aReserva)),
    );
  }

  cancelar(id: number): Observable<Reserva> {
    return this.http.delete<ReservaJson>(`/reservas/${id}`).pipe(
      catchError((error: HttpErrorResponse) => throwError(() => traducir(error))),
      map(aReserva),
    );
  }

  apuntarEnEspera(peticion: PeticionReserva): Observable<number> {
    return this.http.post<{ posicion: number }>('/lista-espera', peticion).pipe(
      catchError((error: HttpErrorResponse) => throwError(() => traducir(error))),
      map((respuesta) => respuesta.posicion),
    );
  }
}

interface ReservaJson {
  id: number;
  sala: number;
  inicio: string;
  fin: string;
  persona: string;
  asistentes: number;
  estado: string;
}

function aReserva(json: ReservaJson): Reserva {
  if (!esEstadoReserva(json.estado)) {
    throw new Error(`La API ha devuelto un estado de reserva desconocido: ${json.estado}`);
  }
  return { ...json, estado: json.estado };
}

function traducir(error: HttpErrorResponse): unknown {
  if (error.status === 409) {
    return new ReservaRechazadaError(
      error.error?.error ?? 'La reserva no se ha podido hacer',
      error.error?.codigo,
    );
  }
  if (error.status === 400) {
    return new DatosNoValidosError(error.error?.error ?? 'Los datos de la reserva no son válidos');
  }
  if (error.status === 0) return new SinConexionError();
  return error;
}
