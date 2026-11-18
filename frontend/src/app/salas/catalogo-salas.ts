import { Injectable, inject } from '@angular/core';
import { Observable, map } from 'rxjs';
import { ApiSalas } from './datos/api-salas';
import { CriterioBusqueda, filtrarSalasDisponibles } from './dominio/filtro-salas';
import { Sala } from './dominio/sala';

@Injectable({ providedIn: 'root' })
export class CatalogoSalas {
  private readonly api = inject(ApiSalas);

  disponibles(criterio: CriterioBusqueda): Observable<Sala[]> {
    return this.api
      .listar()
      .pipe(map((salas) => filtrarSalasDisponibles(salas, criterio)));
  }
}
