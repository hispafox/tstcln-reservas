import { HttpClient } from '@angular/common/http';
import { Component, DestroyRef, OnInit, computed, inject, signal } from '@angular/core';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { FormsModule } from '@angular/forms';
import { interval } from 'rxjs';
import { Sala } from '../../salas/sala';
import { ApiReservas } from '../api-reservas';
import { FiltrosReservas } from '../filtros-reservas/filtros-reservas';
import { FranjaHoraria } from '../franja-horaria';
import { Reserva, estaPendiente } from '../reserva';
import { FilaReserva, TablaReservas } from '../tabla-reservas/tabla-reservas';
import { ordenar, soloLasProximas } from './reservas-visibles';

@Component({
  selector: 'app-lista-reservas',
  imports: [FormsModule, FiltrosReservas, TablaReservas],
  templateUrl: './lista-reservas.html',
  styleUrl: './lista-reservas.css',
})
export class ListaReservas implements OnInit {
  http = inject(HttpClient);
  private readonly reservasApi = inject(ApiReservas);
  private readonly destruccion = inject(DestroyRef);

  persona = 'Ana';
  salas: Sala[] = [];

  protected readonly reservas = signal<FilaReserva[]>([]);
  protected readonly soloConfirmadas = signal(true);
  protected readonly orden = signal('fecha');

  protected readonly visibles = computed(() => {
    const lista = this.soloConfirmadas() ? soloLasProximas(this.reservas()) : this.reservas();
    return ordenar(lista, this.orden());
  });

  protected readonly cargando = signal(false);
  protected readonly error = signal('');
  protected readonly aviso = signal('');

  ngOnInit() {
    this.cargar();
    this.refrescarCadaMinuto();
  }

  private refrescarCadaMinuto() {
    interval(60_000).pipe(takeUntilDestroyed(this.destruccion)).subscribe(() => this.cargar());
  }

  cargar() {
    this.cargando.set(true);
    this.error.set('');
    this.http.get<Sala[]>('/salas').subscribe((salas) => {
      this.salas = salas;
      this.reservasApi.deUnaPersona(this.persona).subscribe({
        next: (recibidas) => this.mostrar(recibidas),
        error: () => this.mostrarError(),
      });
    });
  }

  private mostrar(recibidas: Reserva[]) {
    this.reservas.set(recibidas.map((reserva) => this.paraLaTabla(reserva)));
    this.terminarCarga();
  }

  private mostrarError() {
    this.error.set('Error al cargar las reservas');
    this.terminarCarga();
  }

  private terminarCarga() {
    this.cargando.set(false);
  }

  private paraLaTabla(reserva: Reserva): FilaReserva {
    const sala = this.salas.find((s) => s.id == reserva.sala);
    const franja = FranjaHoraria.desde(reserva.inicio, reserva.fin);
    return {
      ...reserva,
      nombreSala: sala ? sala.nombre : '¿?',
      duracion: franja.minutos(),
      pendiente: estaPendiente(reserva, new Date()),
    };
  }

  cancelar(reserva: FilaReserva) {
    if (!confirm('¿Cancelar la reserva de ' + reserva.nombreSala + '?')) return;
    this.reservasApi.cancelar(reserva.id).subscribe((cancelada) => {
      this.reservas.update((lista) => lista.map((r) => (r.id === cancelada.id ? this.paraLaTabla(cancelada) : r)));
      if (cancelada.estado == 'cancelada_tarde') {
        this.aviso.set('Cancelación tardía: quedan menos de dos horas');
        setTimeout(() => this.aviso.set(''), 5000);
      }
    });
  }
}
