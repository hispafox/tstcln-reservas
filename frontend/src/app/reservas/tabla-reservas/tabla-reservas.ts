import { Component, input, output } from '@angular/core';
import { EstadoReserva, Reserva, textoDeEstado } from '../reserva';

export interface FilaReserva extends Reserva {
  nombreSala: string;
  duracion: number;
  pendiente: boolean;
}

@Component({
  selector: 'app-tabla-reservas',
  templateUrl: './tabla-reservas.html',
  styleUrl: './tabla-reservas.css',
})
export class TablaReservas {
  readonly reservas = input.required<FilaReserva[]>();
  readonly cancelacionPedida = output<FilaReserva>();

  protected textoDelEstado(estado: EstadoReserva) {
    return textoDeEstado(estado);
  }
}
