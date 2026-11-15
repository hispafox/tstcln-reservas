import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { Sala } from './sala';

@Injectable({ providedIn: 'root' })
export class ApiSalas {
  private readonly http = inject(HttpClient);

  listar(): Observable<Sala[]> {
    return this.http.get<Sala[]>('/salas');
  }
}
