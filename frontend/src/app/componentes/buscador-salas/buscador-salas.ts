import { ChangeDetectionStrategy, Component, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { Sala } from '../../modelos/sala';
import { ServicioSalas } from '../../servicios/servicio-salas';

@Component({
  selector: 'app-buscador-salas',
  imports: [RouterLink],
  templateUrl: './buscador-salas.html',
  styleUrl: './buscador-salas.css',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class BuscadorSalas {
  private readonly salas = inject(ServicioSalas);

  protected readonly equipamientos = ['pantalla', 'videoconferencia'];
  protected readonly asistentes = signal<number | null>(null);
  protected readonly equipamiento = signal<string[]>([]);
  protected readonly resultados = signal<Sala[]>([]);
  protected readonly buscando = signal(false);
  protected readonly buscado = signal(false);
  protected readonly error = signal(false);

  protected cambiarAsistentes(valor: string): void {
    this.asistentes.set(valor === '' ? null : Number(valor));
  }

  protected alternarEquipamiento(elemento: string, marcado: boolean): void {
    this.equipamiento.update((actual) =>
      marcado ? [...actual, elemento] : actual.filter((e) => e !== elemento),
    );
  }

  protected buscar(): void {
    this.buscando.set(true);
    this.error.set(false);
    this.salas.buscar({ asistentes: this.asistentes(), equipamiento: this.equipamiento() }).subscribe({
      next: (salas) => {
        this.resultados.set(salas);
        this.buscando.set(false);
        this.buscado.set(true);
      },
      error: () => {
        this.buscando.set(false);
        this.error.set(true);
      },
    });
  }
}
