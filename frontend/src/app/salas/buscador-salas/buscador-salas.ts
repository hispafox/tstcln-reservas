import { ChangeDetectionStrategy, Component, inject, signal } from '@angular/core';
import { Capacidad } from '../capacidad';
import { CatalogoSalas } from '../catalogo-salas';
import { Sala } from '../sala';
import { TarjetaSala } from '../tarjeta-sala/tarjeta-sala';

@Component({
  selector: 'app-buscador-salas',
  imports: [TarjetaSala],
  templateUrl: './buscador-salas.html',
  styleUrl: './buscador-salas.css',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class BuscadorSalas {
  private readonly catalogo = inject(CatalogoSalas);

  protected readonly equipamientos = ['pantalla', 'videoconferencia'];
  protected readonly asistentes = signal(1);
  protected readonly equipamiento = signal<string[]>([]);
  protected readonly resultados = signal<Sala[]>([]);
  protected readonly buscando = signal(false);
  protected readonly buscado = signal(false);
  protected readonly error = signal(false);

  constructor() {
    this.buscar();
  }

  protected cambiarAsistentes(valor: string): void {
    const capacidad = Capacidad.leer(valor);
    if (capacidad) this.asistentes.set(capacidad.valor);
  }

  protected alternarEquipamiento(elemento: string, marcado: boolean): void {
    this.equipamiento.update((actual) =>
      marcado ? [...actual, elemento] : actual.filter((e) => e !== elemento),
    );
  }

  protected buscar(): void {
    this.buscando.set(true);
    this.error.set(false);
    this.catalogo
      .disponibles({ asistentes: this.asistentes(), equipamiento: this.equipamiento() })
      .subscribe({
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
