import { Component, computed, inject, input, output, signal } from '@angular/core';
import { toSignal } from '@angular/core/rxjs-interop';
import {
  FormControl,
  FormGroup,
  ReactiveFormsModule,
  Validators,
} from '@angular/forms';
import { Sala, admite } from '../../salas/dominio/sala';
import { ApiReservas } from '../datos/api-reservas';
import { duracionEnMinutos, textoDeDuracion } from '../dominio/duracion';
import { ReservaRechazadaError } from '../errores';
import { finDespuesDeInicio } from '../fin-despues-de-inicio';
import { Reserva } from '../dominio/reserva';

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

  protected readonly formulario = new FormGroup(
    {
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
    },
    { validators: finDespuesDeInicio },
  );

  protected readonly inicio = toSignal(this.formulario.controls.inicio.valueChanges, {
    initialValue: '',
  });
  protected readonly fin = toSignal(this.formulario.controls.fin.valueChanges, {
    initialValue: '',
  });

  protected readonly duracion = computed(() =>
    textoDeDuracion(duracionEnMinutos(this.inicio(), this.fin())),
  );

  protected readonly asistentes = toSignal(this.formulario.controls.asistentes.valueChanges, {
    initialValue: 1,
  });

  protected readonly fueraDeCapacidad = computed(() => !admite(this.sala(), this.asistentes()));

  protected reservar(): void {
    this.error.set(null);
    this.api.crear({ sala: this.sala().id, ...this.formulario.getRawValue() }).subscribe({
      next: (reserva) => this.reservaCreada.emit(reserva),
      error: (error) =>
        this.error.set(error instanceof ReservaRechazadaError ? error.message : 'No se ha podido hacer la reserva'),
    });
  }
}
