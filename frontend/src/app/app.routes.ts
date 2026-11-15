import { Routes } from '@angular/router';
import { PanelOcupacion } from './ocupacion/panel-ocupacion/panel-ocupacion';
import { ListaReservas } from './reservas/lista-reservas/lista-reservas';
import { PantallaReserva } from './reservas/pantalla-reserva/pantalla-reserva';
import { BuscadorSalas } from './salas/buscador-salas/buscador-salas';

export const routes: Routes = [
  { path: '', component: BuscadorSalas, title: 'Buscar salas' },
  { path: 'reservar/:sala', component: PantallaReserva, title: 'Reservar' },
  { path: 'mis-reservas', component: ListaReservas, title: 'Mis reservas' },
  { path: 'panel-ocupacion', component: PanelOcupacion, title: 'Ocupación' },
  { path: '**', redirectTo: '' },
];
