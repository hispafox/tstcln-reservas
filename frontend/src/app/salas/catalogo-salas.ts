import { Injectable, inject } from '@angular/core';
import { Observable, map } from 'rxjs';
import { ApiSalas } from './api-salas';
import { CriterioBusqueda, filtrarSalasDisponibles } from './filtro-salas';
import { Sala } from './sala';

@Injectable({ providedIn: 'root' })
export class CatalogoSalas {
  private readonly api = inject(ApiSalas);

  disponibles(criterio: CriterioBusqueda): Observable<Sala[]> {
    return this.api
      .listar()
      .pipe(map((salas) => filtrarSalasDisponibles(salas, criterio)));
  }
}
