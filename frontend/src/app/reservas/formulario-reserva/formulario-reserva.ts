import { Component, inject, input, output, signal } from '@angular/core';
import {
  FormControl,
  ReactiveFormsModule,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
import { Sala } from '../../salas/sala';
import { ApiReservas } from '../api-reservas';
import { Reserva } from '../reserva';

@Component({
  selector: 'app-formulario-reserva',
  imports: [ReactiveFormsModule],
  templateUrl: './formulario-reserva.html',
  styleUrl: './formulario-reserva.css',
})
export class FormularioReserva {
  readonly sala = input.required<Sala>();
  readonly reservaCreada = output<Reserva>();

  private readonly api = inject(ApiReservas);
  protected readonly error = signal<string | null>(null);

  protected readonly formulario = new UntypedFormGroup({
    persona: new FormControl('', {
      nonNullable: true,
      validators: Validators.required,
    }),
    asistentes: new FormControl(1, {
      nonNullable: true,
      validators: [Validators.required, Validators.min(1)],
    }),
    inicio: new FormControl('', {
      nonNullable: true,
      validators: Validators.required,
    }),
    fin: new FormControl('', {
      nonNullable: true,
      validators: Validators.required,
    }),
  });

  get duracion(): string {
    const inicio = new Date(this.formulario.value.inicio);
    const fin = new Date(this.formulario.value.fin);
    return `${fin.getHours() - inicio.getHours()} h`;
  }

  protected reservar(): void {
    this.error.set(null);
    this.api.crear({ sala: this.sala().id, ...this.formulario.getRawValue() }).subscribe({
      next: (reserva) => this.reservaCreada.emit(reserva),
      error: () => this.error.set('No se ha podido hacer la reserva'),
    });
  }
}
