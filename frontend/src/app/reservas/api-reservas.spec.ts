import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { ApiReservas } from './api-reservas';
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

  it.todo('envía la reserva a POST /reservas con el formato de la API');
  it.todo('devuelve la reserva que responde la API');
  it.todo('convierte el 409 en un error de negocio con el mensaje de la API');
  it.todo('avisa de que no hay conexión cuando la petición no llega');
  it.todo('deja pasar sin traducir cualquier otro error');
});
