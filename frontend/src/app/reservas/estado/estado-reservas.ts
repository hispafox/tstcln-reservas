import { Injectable, inject, signal } from '@angular/core';
import { Observable, tap } from 'rxjs';
import { Avisos } from '../../compartido';
import { ApiReservas } from '../datos/api-reservas';
import { PeticionReserva, Reserva } from '../dominio/reserva';

@Injectable({ providedIn: 'root' })
export class EstadoReservas {
  private readonly api = inject(ApiReservas);
  private readonly avisos = inject(Avisos);

  private readonly lista = signal<Reserva[]>([]);
  private readonly enCurso = signal(false);
  private readonly conError = signal(false);
  private readonly esperas = signal<PeticionReserva[]>([]);
  private persona = '';

  readonly reservas = this.lista.asReadonly();
  readonly cargando = this.enCurso.asReadonly();
  readonly hayError = this.conError.asReadonly();
  readonly enEspera = this.esperas.asReadonly();

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
    return this.api
      .cancelar(id)
      .pipe(
        tap((cancelada) =>
          this.lista.update((lista) => lista.map((r) => (r.id === cancelada.id ? cancelada : r))),
        ),
      );
  }

  anadir(reserva: Reserva): void {
    if (reserva.persona === this.persona) {
      this.lista.update((lista) => [...lista, reserva]);
    }
  }

  apuntarEnEspera(peticion: PeticionReserva): void {
    this.api.apuntarEnEspera(peticion).subscribe((posicion) => {
      this.esperas.update((esperas) => [...esperas, peticion]);
      this.avisos.mostrar(`Te has apuntado a la lista de espera: vas en el puesto ${posicion}`);
    });
  }
}
