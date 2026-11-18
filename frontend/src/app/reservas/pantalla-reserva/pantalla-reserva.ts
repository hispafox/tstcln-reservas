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
import { Avisos } from '../../compartido';
import { ApiSalas, TarjetaSala } from '../../salas';
import { EstadoReservas } from '../estado/estado-reservas';
import { FormularioReserva, RechazoDeReserva } from '../formulario-reserva/formulario-reserva';
import { OfertaListaDeEspera } from '../oferta-lista-de-espera/oferta-lista-de-espera';
import { PeticionReserva, Reserva } from '../dominio/reserva';

@Component({
  selector: 'app-pantalla-reserva',
  imports: [TarjetaSala, FormularioReserva, OfertaListaDeEspera],
  templateUrl: './pantalla-reserva.html',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class PantallaReserva {
  readonly idSala = input.required<number, unknown>({ alias: 'sala', transform: numberAttribute });

  private readonly salas = toSignal(inject(ApiSalas).listar());
  protected readonly sala = computed(() => this.salas()?.find((sala) => sala.id === this.idSala()));
  protected readonly confirmada = signal<Reserva | null>(null);
  protected readonly ofertaDeEspera = signal<PeticionReserva | null>(null);
  private readonly avisos = inject(Avisos);
  private readonly estado = inject(EstadoReservas);

  protected creada(reserva: Reserva): void {
    this.confirmada.set(reserva);
    this.ofertaDeEspera.set(null);
    this.estado.anadir(reserva);
    this.avisos.mostrar('Reserva creada');
  }

  protected rechazada(rechazo: RechazoDeReserva): void {
    this.ofertaDeEspera.set(rechazo.codigo === 'sala_no_disponible' ? rechazo.peticion : null);
  }

  protected apuntarme(peticion: PeticionReserva): void {
    this.estado.apuntarEnEspera(peticion);
    this.ofertaDeEspera.set(null);
  }
}
