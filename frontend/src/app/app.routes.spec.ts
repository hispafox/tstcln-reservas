import { TestBed } from '@angular/core/testing';
import { provideRouter, withComponentInputBinding } from '@angular/router';
import { RouterTestingHarness } from '@angular/router/testing';
import { ApiReservasEnMemoria } from '../testing/api-reservas-en-memoria';
import { ApiSalasEnMemoria } from '../testing/api-salas-en-memoria';
import { sala } from '../testing/datos-de-prueba';
import { routes } from './app.routes';
import { ApiReservas } from './reservas';
import { ApiSalas } from './salas';

describe('Las rutas de la aplicación', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [
        provideRouter(routes, withComponentInputBinding()),
        {
          provide: ApiSalas,
          useValue: new ApiSalasEnMemoria([sala({ id: 3, nombre: 'Sala Este' })]),
        },
        { provide: ApiReservas, useValue: new ApiReservasEnMemoria() },
      ],
    });
  });

  it('/mis-reservas abre la lista de reservas', async () => {
    const pantalla = await RouterTestingHarness.create();

    await pantalla.navigateByUrl('/mis-reservas');

    expect(pantalla.routeNativeElement?.querySelector('h1')?.textContent).toBe('Mis reservas');
  });

  it('/reservar/3 abre la pantalla de reservar con la sala 3', async () => {
    const pantalla = await RouterTestingHarness.create();

    await pantalla.navigateByUrl('/reservar/3');

    expect(pantalla.routeNativeElement?.querySelector('h2')?.textContent).toBe('Sala Este');
  });
});
