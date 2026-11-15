import { ChangeDetectionStrategy, Component, inject, signal } from '@angular/core';
import { OcupacionSala } from '../ocupacion';
import { ApiOcupacion } from '../api-ocupacion';

@Component({
  selector: 'app-panel-ocupacion',
  templateUrl: './panel-ocupacion.html',
  styleUrl: './panel-ocupacion.css',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class PanelOcupacion {
  private readonly ocupacion = inject(ApiOcupacion);

  protected readonly desde = signal('');
  protected readonly hasta = signal('');
  protected readonly salas = signal<OcupacionSala[]>([]);
  protected readonly cargando = signal(false);
  protected readonly consultado = signal(false);
  protected readonly error = signal(false);

  protected consultar(): void {
    this.cargando.set(true);
    this.error.set(false);
    this.ocupacion.dePeriodo(this.desde(), this.hasta()).subscribe({
      next: (salas) => {
        this.salas.set(salas);
        this.cargando.set(false);
        this.consultado.set(true);
      },
      error: () => {
        this.cargando.set(false);
        this.error.set(true);
      },
    });
  }
}
