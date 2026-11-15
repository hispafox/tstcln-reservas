import { HttpErrorResponse, provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { firstValueFrom } from 'rxjs';
import { ApiReservas } from './api-reservas';
import { ReservaRechazadaError, SinConexionError } from './errores';
import { PeticionReserva, Reserva } from './reserva';

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
      providers: [provideHttpClient(), provideHttpClientTesting()],
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
});
