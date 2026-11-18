import { HttpErrorResponse, provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { firstValueFrom } from 'rxjs';
import { ApiReservas } from './api-reservas';
import { ApiReservasHttp } from './api-reservas-http';
import { DatosNoValidosError, ReservaRechazadaError, SinConexionError } from '../errores';
import { PeticionReserva, Reserva } from '../dominio/reserva';

// Copiadas de lo que lee y responde el controlador de reservas del backend.
const peticion: PeticionReserva = {
  sala: 3,
  inicio: '2027-03-04T10:00',
  fin: '2027-03-04T11:00',
  persona: 'Ana',
  asistentes: 4,
};

const reservaCreada: Reserva = {
  id: 7,
  sala: 3,
  inicio: '2027-03-04T10:00:00+01:00',
  fin: '2027-03-04T11:00:00+01:00',
  persona: 'Ana',
  asistentes: 4,
  estado: 'confirmada',
};

describe('ApiReservas', () => {
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

  it('envía la reserva a POST /reservas con el formato de la API', () => {
    const peticion: PeticionReserva = {
      sala: 3,
      inicio: '2027-03-04T10:00',
      fin: '2027-03-04T11:00',
      persona: 'Ana',
      asistentes: 4,
    };

    api.crear(peticion).subscribe();

    const req = centralita.expectOne({ method: 'POST', url: '/reservas' });
    expect(req.request.body).toEqual(peticion);
    req.flush(reservaCreada);
  });

  it('devuelve la reserva que responde la API', async () => {
    const respuesta = firstValueFrom(api.crear(peticion));

    centralita
      .expectOne('/reservas')
      .flush(reservaCreada, { status: 201, statusText: 'Created' });

    expect(await respuesta).toEqual(reservaCreada);
  });

  it('convierte el 409 en un error de negocio con el mensaje de la API', async () => {
    const respuesta = firstValueFrom(api.crear(peticion));

    centralita
      .expectOne('/reservas')
      .flush(
        { error: 'La sala ya está reservada en esa franja' },
        { status: 409, statusText: 'Conflict' },
      );

    await expect(respuesta).rejects.toThrow(ReservaRechazadaError);
    await expect(respuesta).rejects.toThrow(
      'La sala ya está reservada en esa franja',
    );
  });

  it('convierte el 400 en un error de datos con el mensaje de la API', async () => {
    const respuesta = firstValueFrom(api.crear(peticion));

    centralita
      .expectOne('/reservas')
      .flush(
        { error: 'Una reserva necesita al menos un asistente' },
        { status: 400, statusText: 'Bad Request' },
      );

    await expect(respuesta).rejects.toThrow(DatosNoValidosError);
    await expect(respuesta).rejects.toThrow(
      'Una reserva necesita al menos un asistente',
    );
  });

  it('avisa de que no hay conexión cuando la petición no llega', async () => {
    const respuesta = firstValueFrom(api.crear(peticion));

    centralita.expectOne('/reservas').error(new ProgressEvent('error'));

    await expect(respuesta).rejects.toThrow(SinConexionError);
  });

  it('deja pasar sin traducir cualquier otro error', async () => {
    const respuesta = firstValueFrom(api.crear(peticion));

    centralita
      .expectOne('/reservas')
      .flush(null, { status: 500, statusText: 'Internal Server Error' });

    await expect(respuesta).rejects.toBeInstanceOf(HttpErrorResponse);
    await expect(respuesta).rejects.toHaveProperty('status', 500);
  });

  it('pide las reservas de una persona con su nombre como parámetro', async () => {
    const respuesta = firstValueFrom(api.deUnaPersona('Ana María'));

    centralita
      .expectOne((peticion) => peticion.url === '/reservas' && peticion.params.get('persona') === 'Ana María')
      .flush([reservaCreada]);

    expect(await respuesta).toEqual([reservaCreada]);
  });

  it('falla si la API devuelve un estado que el dominio no conoce', async () => {
    const respuesta = firstValueFrom(api.deUnaPersona('Ana'));

    centralita.expectOne((peticion) => peticion.url === '/reservas').flush([{ ...reservaCreada, estado: 'pendiente_de_pago' }]);

    await expect(respuesta).rejects.toThrow('estado de reserva desconocido');
  });

  it('cancela con DELETE /reservas/{id} y devuelve la reserva cancelada', async () => {
    const cancelada: Reserva = { ...reservaCreada, estado: 'cancelada_a_tiempo' };
    const respuesta = firstValueFrom(api.cancelar(7));

    centralita.expectOne({ method: 'DELETE', url: '/reservas/7' }).flush(cancelada);

    expect(await respuesta).toEqual(cancelada);
  });
});
