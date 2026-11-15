import { Component, computed, inject, input, output, signal } from '@angular/core';
import { toSignal } from '@angular/core/rxjs-interop';
import {
  FormControl,
  ReactiveFormsModule,
  UntypedFormGroup,
  Validators,
} from '@angular/forms';
import { Sala } from '../../salas/sala';
import { ApiReservas } from '../api-reservas';
import { duracionEnMinutos, textoDeDuracion } from '../duracion';
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

  protected readonly inicio = toSignal(this.formulario.controls['inicio'].valueChanges, {
    initialValue: '',
  });
  protected readonly fin = toSignal(this.formulario.controls['fin'].valueChanges, {
    initialValue: '',
  });

  protected readonly duracion = computed(() =>
    textoDeDuracion(duracionEnMinutos(this.inicio(), this.fin())),
  );

  protected reservar(): void {
    this.error.set(null);
    this.api.crear({ sala: this.sala().id, ...this.formulario.getRawValue() }).subscribe({
      next: (reserva) => this.reservaCreada.emit(reserva),
      error: () => this.error.set('No se ha podido hacer la reserva'),
    });
  }
}
