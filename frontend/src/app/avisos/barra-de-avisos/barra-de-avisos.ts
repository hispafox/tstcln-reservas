import { ChangeDetectionStrategy, Component, inject } from '@angular/core';
import { Avisos } from '../avisos';

@Component({
  selector: 'app-barra-de-avisos',
  templateUrl: './barra-de-avisos.html',
  styleUrl: './barra-de-avisos.css',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class BarraDeAvisos {
  protected readonly avisos = inject(Avisos);
}
