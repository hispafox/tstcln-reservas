// Prueba heredada: llegó así de otro proyecto y lleva meses en verde.
import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { ApiReservas } from './api-reservas';
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
      providers: [provideHttpClient(), provideHttpClientTesting()],
    });
    api = TestBed.inject(ApiReservas);
    centralita = TestBed.inject(HttpTestingController);
  });

  afterEach(() => centralita.verify());

  it('convierte el 409 en un error de negocio', () => {
    api.crear(peticion).subscribe({
      error: (error) => expect(error).toBeInstanceOf(ReservaRechazadaError),
    });

    centralita.expectOne('/reservas');
  });
});
