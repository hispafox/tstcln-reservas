import { HttpClient } from '@angular/common/http';
import { ChangeDetectorRef, Component, OnInit, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { interval } from 'rxjs';
import { FranjaHoraria } from '../franja-horaria';
import { EstadoReserva, textoDeEstado } from '../reserva';
import { ordenar, soloLasProximas } from './reservas-visibles';

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
    const franja = FranjaHoraria.desde(reserva.inicio, reserva.fin);
    return {
      ...reserva,
      nombreSala: sala ? sala.nombre : '¿?',
      duracion: franja.minutos(),
      yaHaEmpezado: franja.haEmpezado(new Date()),
    };
  }

  actualizarLista() {
    let lista = this.reservas;
    if (this.soloConfirmadas) {
      lista = soloLasProximas(lista);
    }
    this.reservasVisibles = ordenar(lista, this.orden);
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
