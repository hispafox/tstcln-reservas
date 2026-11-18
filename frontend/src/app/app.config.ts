import { provideHttpClient } from '@angular/common/http';
import { ApplicationConfig, provideBrowserGlobalErrorListeners } from '@angular/core';
import { provideRouter, withComponentInputBinding } from '@angular/router';
import { routes } from './app.routes';
import { ApiReservas, ApiReservasHttp } from './reservas';
import { ApiSalas } from './salas';
import { ApiSalasHttp } from './salas/datos/api-salas-http';

export const appConfig: ApplicationConfig = {
  providers: [
    provideBrowserGlobalErrorListeners(),
    provideRouter(routes, withComponentInputBinding()),
    provideHttpClient(),
    { provide: ApiReservas, useClass: ApiReservasHttp },
    { provide: ApiSalas, useClass: ApiSalasHttp },
  ],
};
