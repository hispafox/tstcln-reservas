import { ChangeDetectionStrategy, Component, inject, signal } from '@angular/core';
import { OcupacionSala } from '../../modelos/ocupacion';
import { ServicioOcupacion } from '../../servicios/servicio-ocupacion';

@Component({
  selector: 'app-panel-ocupacion',
  templateUrl: './panel-ocupacion.html',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class PanelOcupacion {
  private readonly ocupacion = inject(ServicioOcupacion);

  protected readonly desde = signal('');
  protected readonly hasta = signal('');
  protected readonly salas = signal<OcupacionSala[]>([]);

  protected consultar(): void {
    this.ocupacion.dePeriodo(this.desde(), this.hasta()).subscribe((salas) => this.salas.set(salas));
  }
}
