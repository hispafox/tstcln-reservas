import { HttpClient } from '@angular/common/http';
import { ChangeDetectorRef, Component, OnInit, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { interval } from 'rxjs';
import { duracionEnMinutos } from '../duracion';
import { EstadoReserva, textoDeEstado } from '../reserva';

@Component({
  selector: 'app-lista-reservas',
  imports: [FormsModule],
  templateUrl: './lista-reservas.html',
  styleUrl: './lista-reservas.css',
})
export class ListaReservas implements OnInit {
  http = inject(HttpClient);
  cdr = inject(ChangeDetectorRef);

  persona = 'Ana';
  reservas: any[] = [];
  reservasVisibles: any[] = [];
  salas: any[] = [];
  soloConfirmadas = true;
  orden = 'fecha';
  cargando = false;
  error = '';
  aviso = '';

  ngOnInit() {
    this.cargar();
    this.refrescarCadaMinuto();
  }

  private refrescarCadaMinuto() {
    interval(60_000).subscribe(() => this.cargar());
  }

  cargar() {
    this.cargando = true;
    this.error = '';
    this.http.get<any[]>('/salas').subscribe((salas) => {
      this.salas = salas;
      this.http.get<any[]>('/reservas?persona=' + this.persona).subscribe(
        (recibidas) => this.mostrar(recibidas),
        () => this.mostrarError(),
      );
    });
  }

  private mostrar(recibidas: any[]) {
    this.reservas = recibidas.map((reserva) => this.paraLaTabla(reserva));
    this.actualizarLista();
    this.terminarCarga();
  }

  private mostrarError() {
    this.error = 'Error al cargar las reservas';
    this.terminarCarga();
  }

  private terminarCarga() {
    this.cargando = false;
    this.cdr.detectChanges();
  }

  private paraLaTabla(reserva: any) {
    const sala = this.salas.find((s) => s.id == reserva.sala);
    return {
      ...reserva,
      nombreSala: sala ? sala.nombre : '¿?',
      duracion: duracionEnMinutos(reserva.inicio, reserva.fin),
      yaHaEmpezado: new Date(reserva.inicio) < new Date(),
    };
  }

  actualizarLista() {
    let lista = this.reservas;
    if (this.soloConfirmadas) {
      lista = this.soloLasProximas(lista);
    }
    this.reservasVisibles = this.ordenar(lista);
  }

  private soloLasProximas(reservas: any[]): any[] {
    return reservas.filter((reserva) => reserva.estado == 'confirmada' && !reserva.yaHaEmpezado);
  }

  private ordenar(reservas: any[]): any[] {
    if (this.orden == 'fecha') {
      return reservas.sort((a, b) => (a.inicio < b.inicio ? -1 : 1));
    } else if (this.orden == 'sala') {
      return reservas.sort((a, b) => a.nombreSala.localeCompare(b.nombreSala));
    } else if (this.orden == 'duracion') {
      return reservas.sort((a, b) => b.duracion - a.duracion);
    }
    return reservas;
  }

  protected textoDelEstado(estado: EstadoReserva) {
    return textoDeEstado(estado);
  }

  cancelar(reserva: any) {
    if (!confirm('¿Cancelar la reserva de ' + reserva.nombreSala + '?')) return;
    this.http.delete('/reservas/' + reserva.id).subscribe((res: any) => {
      reserva.estado = res.estado;
      if (res.estado == 'cancelada_tarde') {
        this.aviso = 'Cancelación tardía: quedan menos de dos horas';
        setTimeout(() => {
          this.aviso = '';
          this.cdr.detectChanges();
        }, 5000);
      }
      this.actualizarLista();
      this.cdr.detectChanges();
    });
  }
}
