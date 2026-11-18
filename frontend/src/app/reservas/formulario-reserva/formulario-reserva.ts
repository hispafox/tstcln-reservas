import { Component, computed, inject, input, output, signal } from '@angular/core';
import { toSignal } from '@angular/core/rxjs-interop';
import { FormControl, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { Sala, admite } from '../../salas';
import { ApiReservas } from '../datos/api-reservas';
import { duracionEnMinutos, textoDeDuracion } from '../dominio/duracion';
import { ReservaRechazadaError } from '../errores';
import { finDespuesDeInicio } from '../fin-despues-de-inicio';
import { PeticionReserva, Reserva } from '../dominio/reserva';

/** La petición que la API ha rechazado, con el código del rechazo (por ejemplo, sala_no_disponible). */
export interface RechazoDeReserva {
  peticion: PeticionReserva;
  codigo: string | undefined;
}

@Component({
  selector: 'app-formulario-reserva',
  imports: [ReactiveFormsModule],
  templateUrl: './formulario-reserva.html',
  styleUrl: './formulario-reserva.css',
})
export class FormularioReserva {
  readonly sala = input.required<Sala>();
  readonly reservaCreada = output<Reserva>();
  readonly reservaRechazada = output<RechazoDeReserva>();

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
    const peticion: PeticionReserva = { sala: this.sala().id, ...this.formulario.getRawValue() };
    this.api.crear(peticion).subscribe({
      next: (reserva) => this.reservaCreada.emit(reserva),
      error: (error) => {
        if (error instanceof ReservaRechazadaError) {
          this.error.set(error.message);
          this.reservaRechazada.emit({ peticion, codigo: error.codigo });
        } else {
          this.error.set('No se ha podido hacer la reserva');
        }
      },
    });
  }
}
