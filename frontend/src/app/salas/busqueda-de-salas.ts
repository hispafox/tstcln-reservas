import { Injectable, inject } from '@angular/core';
import { Subject, debounceTime, distinctUntilChanged, map, switchMap } from 'rxjs';
import { ApiSalas } from './datos/api-salas';
import { conNombre } from './dominio/filtro-salas';

@Injectable({ providedIn: 'root' })
export class BusquedaDeSalas {
  private readonly api = inject(ApiSalas);
  private readonly texto = new Subject<string>();

  readonly resultados = this.texto.pipe(
    debounceTime(300),
    distinctUntilChanged(),
    switchMap((texto) => this.api.listar().pipe(map((salas) => conNombre(salas, texto)))),
  );

  escribir(texto: string): void {
    this.texto.next(texto);
  }
}
