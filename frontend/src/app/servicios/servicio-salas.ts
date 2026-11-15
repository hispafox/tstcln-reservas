import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable, map } from 'rxjs';
import { FiltroSalas, filtrarSalas } from '../logica/filtro-salas';
import { Sala } from '../modelos/sala';

@Injectable({ providedIn: 'root' })
export class ServicioSalas {
  private readonly http = inject(HttpClient);

  buscar(filtro: FiltroSalas): Observable<Sala[]> {
    return this.http.get<Sala[]>('/salas').pipe(map((salas) => filtrarSalas(salas, filtro)));
  }
}
