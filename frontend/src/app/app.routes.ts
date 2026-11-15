import { Routes } from '@angular/router';
import { BuscadorSalas } from './componentes/buscador-salas/buscador-salas';
import { FormularioReserva } from './componentes/formulario-reserva/formulario-reserva';
import { ListaReservas } from './componentes/lista-reservas/lista-reservas';
import { PanelOcupacion } from './componentes/panel-ocupacion/panel-ocupacion';

export const routes: Routes = [
  { path: '', component: BuscadorSalas, title: 'Buscar salas' },
  { path: 'reservar/:sala', component: FormularioReserva, title: 'Reservar' },
  { path: 'mis-reservas', component: ListaReservas, title: 'Mis reservas' },
  { path: 'panel-ocupacion', component: PanelOcupacion, title: 'Ocupación' },
  { path: '**', redirectTo: '' },
];
