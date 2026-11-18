import { Injectable, inject, signal } from '@angular/core';
import { Observable, tap } from 'rxjs';
import { ApiReservas } from '../datos/api-reservas';
import { Reserva } from '../dominio/reserva';

@Injectable({ providedIn: 'root' })
export class EstadoReservas {
  private readonly api = inject(ApiReservas);

  private readonly lista = signal<Reserva[]>([]);
  private readonly enCurso = signal(false);
  private readonly conError = signal(false);
  private persona = '';

  readonly reservas = this.lista.asReadonly();
  readonly cargando = this.enCurso.asReadonly();
  readonly hayError = this.conError.asReadonly();

  cargar(persona: string): void {
    this.persona = persona;
    this.enCurso.set(true);
    this.conError.set(false);
    this.api.deUnaPersona(persona).subscribe({
      next: (reservas) => {
        this.lista.set(reservas);
        this.enCurso.set(false);
      },
      error: () => {
        this.conError.set(true);
        this.enCurso.set(false);
      },
    });
  }

  cancelar(id: number): Observable<Reserva> {
    return this.api.cancelar(id).pipe(
      tap((cancelada) => this.lista.update((lista) => lista.map((r) => (r.id === cancelada.id ? cancelada : r)))),
    );
  }

  anadir(reserva: Reserva): void {
    if (reserva.persona === this.persona) {
      this.lista.update((lista) => [...lista, reserva]);
    }
  }
}
