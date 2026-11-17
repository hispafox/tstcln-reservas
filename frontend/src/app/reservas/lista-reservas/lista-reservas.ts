import { HttpClient } from '@angular/common/http';
import { ChangeDetectorRef, Component, OnInit, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { interval } from 'rxjs';

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
    // refresca cada minuto
    interval(60000).subscribe(() => {
      this.cargar();
    });
  }

  cargar() {
    this.cargando = true;
    this.error = '';
    this.http.get<any[]>('/salas').subscribe((salas) => {
      this.salas = salas;
      this.http.get<any[]>('/reservas?persona=' + this.persona).subscribe(
        (recibidas) => {
          this.reservas = recibidas.map((reserva) => {
            const sala = this.salas.find((s) => s.id == reserva.sala);
            return {
              ...reserva,
              nombreSala: sala ? sala.nombre : '¿?',
              duracion: (new Date(reserva.fin).getTime() - new Date(reserva.inicio).getTime()) / 60000,
              yaHaEmpezado: new Date(reserva.inicio) < new Date(),
            };
          });
          this.aplicarFiltros();
          this.cargando = false;
          this.cdr.detectChanges();
        },
        () => {
          this.error = 'Error al cargar las reservas';
          this.cargando = false;
          this.cdr.detectChanges();
        },
      );
    });
  }

  aplicarFiltros() {
    let lista = this.reservas;
    if (this.soloConfirmadas) {
      lista = lista.filter((reserva) => reserva.estado == 'confirmada' && !reserva.yaHaEmpezado);
    }
    if (this.orden == 'fecha') {
      lista = lista.sort((a, b) => (a.inicio < b.inicio ? -1 : 1));
    } else if (this.orden == 'sala') {
      lista = lista.sort((a, b) => a.nombreSala.localeCompare(b.nombreSala));
    } else if (this.orden == 'duracion') {
      lista = lista.sort((a, b) => b.duracion - a.duracion);
    }
    this.reservasVisibles = lista;
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
      this.aplicarFiltros();
      this.cdr.detectChanges();
    });
  }
}
