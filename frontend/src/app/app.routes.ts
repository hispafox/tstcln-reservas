import { Routes } from '@angular/router';
import { BuscadorSalas } from './salas/buscador-salas/buscador-salas';

export const routes: Routes = [
  { path: '', component: BuscadorSalas, title: 'Buscar salas' },
  { path: '', loadChildren: () => import('./reservas/reservas.routes').then((m) => m.rutasReservas) },
  {
    path: 'panel-ocupacion',
    loadComponent: () => import('./ocupacion/panel-ocupacion/panel-ocupacion').then((m) => m.PanelOcupacion),
    title: 'Ocupación',
  },
  { path: '**', redirectTo: '' },
];
