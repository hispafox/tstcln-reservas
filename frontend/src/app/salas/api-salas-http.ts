import { HttpClient } from '@angular/common/http';
import { Injectable, inject } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiSalas } from './api-salas';
import { Sala } from './sala';

@Injectable()
export class ApiSalasHttp implements ApiSalas {
  private readonly http = inject(HttpClient);

  listar(): Observable<Sala[]> {
    return this.http.get<Sala[]>('/salas');
  }
}
