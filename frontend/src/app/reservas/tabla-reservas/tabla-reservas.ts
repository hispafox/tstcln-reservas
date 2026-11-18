import { Component, input, output } from '@angular/core';
import { EstadoReserva, textoDeEstado } from '../reserva';

@Component({
  selector: 'app-tabla-reservas',
  templateUrl: './tabla-reservas.html',
  styleUrl: './tabla-reservas.css',
})
export class TablaReservas {
  readonly reservas = input.required<any[]>();
  readonly cancelacionPedida = output<any>();

  protected textoDelEstado(estado: EstadoReserva) {
    return textoDeEstado(estado);
  }
}
