import { Component, model } from '@angular/core';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-filtros-reservas',
  imports: [FormsModule],
  templateUrl: './filtros-reservas.html',
  styleUrl: './filtros-reservas.css',
})
export class FiltrosReservas {
  readonly soloConfirmadas = model(true);
  readonly orden = model('fecha');
}
