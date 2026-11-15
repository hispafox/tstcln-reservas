import { HttpClient } from '@angular/common/http';
import { ChangeDetectorRef, Component, OnInit, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { interval } from 'rxjs';

@Component({
  selector: 'app-lista-reservas',
  imports: [FormsModule],
  templateUrl: './lista-reservas.html',
})
export class ListaReservas implements OnInit {
  http = inject(HttpClient);
  cdr = inject(ChangeDetectorRef);

  persona = 'Ana';
  reservas: any[] = [];
  reservasFiltradas: any[] = [];
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
        (res) => {
          this.reservas = res.map((r) => {
            const sala = this.salas.find((s) => s.id == r.sala);
            return {
              ...r,
              nombreSala: sala ? sala.nombre : '¿?',
              duracion: (new Date(r.fin).getTime() - new Date(r.inicio).getTime()) / 60000,
              pasada: new Date(r.inicio) < new Date(),
            };
          });
          this.aplicarFiltros();
          this.cargando = false;
          this.cdr.detectChanges();
        },
        (err) => {
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
      lista = lista.filter((r) => r.estado == 'confirmada' && !r.pasada);
    }
    if (this.orden == 'fecha') {
      lista = lista.sort((a, b) => (a.inicio < b.inicio ? -1 : 1));
    } else if (this.orden == 'sala') {
      lista = lista.sort((a, b) => a.nombreSala.localeCompare(b.nombreSala));
    } else if (this.orden == 'duracion') {
      lista = lista.sort((a, b) => b.duracion - a.duracion);
    }
    this.reservasFiltradas = lista;
  }

  cancelar(r: any) {
    if (!confirm('¿Cancelar la reserva de ' + r.nombreSala + '?')) return;
    this.http.delete('/reservas/' + r.id).subscribe((res: any) => {
      r.estado = res.estado;
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
