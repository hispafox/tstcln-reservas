import { Component, DestroyRef, OnInit, computed, inject, signal } from '@angular/core';
import { takeUntilDestroyed, toSignal } from '@angular/core/rxjs-interop';
import { FormsModule } from '@angular/forms';
import { interval } from 'rxjs';
import { Avisos } from '../../compartido';
import { ApiSalas } from '../../salas';
import { EstadoReservas } from '../estado/estado-reservas';
import { FiltrosReservas } from '../filtros-reservas/filtros-reservas';
import { FranjaHoraria } from '../dominio/franja-horaria';
import { Reserva, estaPendiente } from '../dominio/reserva';
import { FilaReserva, TablaReservas } from '../tabla-reservas/tabla-reservas';
import { ordenar, soloLasProximas } from './reservas-visibles';

@Component({
  selector: 'app-lista-reservas',
  imports: [FormsModule, FiltrosReservas, TablaReservas],
  templateUrl: './lista-reservas.html',
  styleUrl: './lista-reservas.css',
})
export class ListaReservas implements OnInit {
  protected readonly estado = inject(EstadoReservas);
  private readonly avisos = inject(Avisos);
  private readonly salas = toSignal(inject(ApiSalas).listar(), { initialValue: [] });
  private readonly destruccion = inject(DestroyRef);

  persona = 'Ana';

  protected readonly filas = computed(() =>
    this.estado.reservas().map((reserva) => this.paraLaTabla(reserva)),
  );
  protected readonly soloConfirmadas = signal(true);
  protected readonly orden = signal('fecha');

  protected readonly visibles = computed(() => {
    const lista = this.soloConfirmadas() ? soloLasProximas(this.filas()) : this.filas();
    return ordenar(lista, this.orden());
  });

  ngOnInit() {
    this.cargar();
    this.refrescarCadaMinuto();
  }

  private refrescarCadaMinuto() {
    interval(60_000)
      .pipe(takeUntilDestroyed(this.destruccion))
      .subscribe(() => this.cargar());
  }

  cargar() {
    this.estado.cargar(this.persona);
  }

  private paraLaTabla(reserva: Reserva): FilaReserva {
    const sala = this.salas().find((s) => s.id == reserva.sala);
    const franja = FranjaHoraria.desde(reserva.inicio, reserva.fin);
    return {
      ...reserva,
      nombreSala: sala ? sala.nombre : '¿?',
      duracion: franja.minutos(),
      pendiente: estaPendiente(reserva, new Date()),
    };
  }

  protected cancelar(reserva: FilaReserva): void {
    if (!confirm('¿Cancelar la reserva de ' + reserva.nombreSala + '?')) return;
    this.estado.cancelar(reserva.id).subscribe((cancelada) => {
      if (cancelada.estado === 'cancelada_tarde') {
        this.avisos.mostrar('Cancelación tardía: quedan menos de dos horas');
      }
    });
  }
}
