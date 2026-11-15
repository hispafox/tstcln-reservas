import { Component, input, output } from '@angular/core';
import { Sala } from '../../salas/sala';
import { Reserva } from '../reserva';

@Component({
  selector: 'app-formulario-reserva',
  templateUrl: './formulario-reserva.html',
  styleUrl: './formulario-reserva.css',
})
export class FormularioReserva {
  readonly sala = input.required<Sala>();
  readonly reservaCreada = output<Reserva>();
}
