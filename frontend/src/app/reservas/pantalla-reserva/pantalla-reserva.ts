import {
  ChangeDetectionStrategy,
  Component,
  computed,
  inject,
  input,
  numberAttribute,
  signal,
} from '@angular/core';
import { toSignal } from '@angular/core/rxjs-interop';
import { Avisos } from '../../avisos/avisos';
import { ApiSalas } from '../../salas/api-salas';
import { TarjetaSala } from '../../salas/tarjeta-sala/tarjeta-sala';
import { FormularioReserva } from '../formulario-reserva/formulario-reserva';
import { Reserva } from '../reserva';

@Component({
  selector: 'app-pantalla-reserva',
  imports: [TarjetaSala, FormularioReserva],
  templateUrl: './pantalla-reserva.html',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class PantallaReserva {
  readonly idSala = input.required<number, unknown>({ alias: 'sala', transform: numberAttribute });

  private readonly salas = toSignal(inject(ApiSalas).listar());
  protected readonly sala = computed(() => this.salas()?.find((sala) => sala.id === this.idSala()));
  protected readonly confirmada = signal<Reserva | null>(null);
  private readonly avisos = inject(Avisos);

  protected creada(reserva: Reserva): void {
    this.confirmada.set(reserva);
    this.avisos.mostrar('Reserva creada');
  }
}
