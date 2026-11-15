import { HttpClient, HttpParams } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { OcupacionSala } from '../modelos/ocupacion';

@Injectable({ providedIn: 'root' })
export class ServicioOcupacion {
  private readonly http = inject(HttpClient);

  dePeriodo(desde: string, hasta: string): Observable<OcupacionSala[]> {
    const parametros = new HttpParams().set('desde', desde).set('hasta', hasta);
    return this.http.get<OcupacionSala[]>('/ocupacion', { params: parametros });
  }
}
