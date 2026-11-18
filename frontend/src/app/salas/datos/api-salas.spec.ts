import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { firstValueFrom } from 'rxjs';
import { salaNorte, salaSur } from '../../../testing/datos-de-prueba';
import { ApiSalas } from './api-salas';
import { ApiSalasHttp } from './api-salas-http';

describe('ApiSalas', () => {
  let apiSalas: ApiSalas;
  let centralita: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [
        provideHttpClient(),
        provideHttpClientTesting(),
        { provide: ApiSalas, useClass: ApiSalasHttp },
      ],
    });
    apiSalas = TestBed.inject(ApiSalas);
    centralita = TestBed.inject(HttpTestingController);
  });

  afterEach(() => centralita.verify());

  it('pide las salas a GET /salas', async () => {
    const respuesta = firstValueFrom(apiSalas.listar());

    centralita.expectOne({ method: 'GET', url: '/salas' }).flush([salaNorte, salaSur]);

    expect(await respuesta).toEqual([salaNorte, salaSur]);
  });
});
