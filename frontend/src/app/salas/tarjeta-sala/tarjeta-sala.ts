import { Component, input } from '@angular/core';
import { Sala } from '../sala';

@Component({
  selector: 'app-tarjeta-sala',
  templateUrl: './tarjeta-sala.html',
  styleUrl: './tarjeta-sala.css',
})
export class TarjetaSala {
  readonly sala = input.required<Sala>();
}
