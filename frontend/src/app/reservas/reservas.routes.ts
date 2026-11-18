import { Routes } from '@angular/router';
import { ListaReservas } from './lista-reservas/lista-reservas';
import { PantallaReserva } from './pantalla-reserva/pantalla-reserva';

export const rutasReservas: Routes = [
  { path: 'reservar/:sala', component: PantallaReserva, title: 'Reservar' },
  { path: 'mis-reservas', component: ListaReservas, title: 'Mis reservas' },
];
