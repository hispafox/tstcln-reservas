// Prueba heredada, arreglada: la original tenía la aserción dentro del subscribe y sin respuesta.
import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { firstValueFrom } from 'rxjs';
import { ApiReservas } from './api-reservas';
import { ApiReservasHttp } from './api-reservas-http';
import { ReservaRechazadaError } from './errores';
import { PeticionReserva } from './reserva';

const peticion: PeticionReserva = {
  sala: 3,
  inicio: '2027-03-04T10:00',
  fin: '2027-03-04T11:00',
  persona: 'Ana',
  asistentes: 4,
};

describe('ApiReservas (heredada)', () => {
  let api: ApiReservas;
  let centralita: HttpTestingController;

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [provideHttpClient(), provideHttpClientTesting(), { provide: ApiReservas, useClass: ApiReservasHttp }],
    });
    api = TestBed.inject(ApiReservas);
    centralita = TestBed.inject(HttpTestingController);
  });

  afterEach(() => centralita.verify());

  // Primer arreglo: contar las aserciones. Si la de dentro no llega, la prueba falla.
  it('convierte el 409 en un error de negocio (contando las aserciones)', () => {
    expect.assertions(1);

    api.crear(peticion).subscribe({
      error: (error) => expect(error).toBeInstanceOf(ReservaRechazadaError),
    });

    centralita
      .expectOne('/reservas')
      .flush(
        { error: 'Ocupada' },
        { status: 409, statusText: 'Conflict' },
      );
  });

  // Segundo arreglo: la promesa, que no deja terminar la prueba sin comprobar.
  it('convierte el 409 en un error de negocio', async () => {
    const respuesta = firstValueFrom(api.crear(peticion));

    centralita
      .expectOne('/reservas')
      .flush(
        { error: 'Ocupada' },
        { status: 409, statusText: 'Conflict' },
      );

    await expect(respuesta).rejects.toThrow(ReservaRechazadaError);
  });
});
