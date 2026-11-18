import { Observable } from 'rxjs';
import { Sala } from './sala';

export abstract class ApiSalas {
  abstract listar(): Observable<Sala[]>;
}
