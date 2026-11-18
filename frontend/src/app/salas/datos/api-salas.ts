import { Observable } from 'rxjs';
import { Sala } from '../dominio/sala';

export abstract class ApiSalas {
  abstract listar(): Observable<Sala[]>;
}
