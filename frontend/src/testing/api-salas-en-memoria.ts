import { Observable, of } from 'rxjs';
import { ApiSalas } from '../app/salas/api-salas';
import { Sala } from '../app/salas/sala';

export class ApiSalasEnMemoria implements ApiSalas {
  private readonly salas: Sala[];

  constructor(iniciales: Sala[] = []) {
    this.salas = [...iniciales];
  }

  listar(): Observable<Sala[]> {
    return of([...this.salas]);
  }
}
