import {
  ChangeDetectionStrategy,
  Component,
  inject,
  input,
  numberAttribute,
  output,
  signal,
} from '@angular/core';
import { Reserva } from '../../modelos/reserva';
import { ServicioReservas } from '../../servicios/servicio-reservas';

@Component({
  selector: 'app-formulario-reserva',
  templateUrl: './formulario-reserva.html',
  styleUrl: './formulario-reserva.css',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class FormularioReserva {
  private readonly reservas = inject(ServicioReservas);

  readonly sala = input.required<number, unknown>({ transform: numberAttribute });
  readonly reservaCreada = output<Reserva>();

  protected readonly fecha = signal('');
  protected readonly horaInicio = signal('');
  protected readonly horaFin = signal('');
  protected readonly persona = signal('');
  protected readonly asistentes = signal<number | null>(null);
  protected readonly enviando = signal(false);
  protected readonly error = signal<string | null>(null);
  protected readonly confirmada = signal<Reserva | null>(null);

  protected cambiarAsistentes(valor: string): void {
    this.asistentes.set(valor === '' ? null : Number(valor));
  }

  protected reservar(): void {
    this.error.set(null);
    this.enviando.set(true);
    this.reservas
      .crear({
        sala: this.sala(),
        inicio: `${this.fecha()}T${this.horaInicio()}`,
        fin: `${this.fecha()}T${this.horaFin()}`,
        persona: this.persona(),
        asistentes: this.asistentes() ?? 0,
      })
      .subscribe({
        next: (reserva) => {
          this.enviando.set(false);
          this.confirmada.set(reserva);
          this.reservaCreada.emit(reserva);
        },
        error: () => {
          this.enviando.set(false);
          this.error.set('No se ha podido crear la reserva.');
        },
      });
  }
}
