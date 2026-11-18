import { TestBed } from '@angular/core/testing';
import { firstValueFrom } from 'rxjs';
import { ApiReservasEnMemoria } from '../../../testing/api-reservas-en-memoria';
import { reserva } from '../../../testing/datos-de-prueba';
import { ApiReservas } from '../datos/api-reservas';
import { EstadoReservas } from './estado-reservas';

describe('EstadoReservas', () => {
  let estado: EstadoReservas;

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [
        {
          provide: ApiReservas,
          useValue: new ApiReservasEnMemoria([
            reserva(1, 1, '2027-03-04T10:00:00+01:00', 'confirmada'),
            reserva(2, 2, '2027-03-05T10:00:00+01:00', 'confirmada'),
          ]),
        },
      ],
    });
    estado = TestBed.inject(EstadoReservas);
  });

  it('carga las reservas de la persona', () => {
    estado.cargar('Ana');

    expect(estado.reservas().map((r) => r.id)).toEqual([1, 2]);
  });

  it('cambia la reserva cancelada sin volver a pedir la lista', async () => {
    estado.cargar('Ana');

    await firstValueFrom(estado.cancelar(1));

    expect(estado.reservas().map((r) => r.estado)).toEqual(['cancelada_a_tiempo', 'confirmada']);
  });

  it('añade una reserva nueva de la persona que se está viendo', () => {
    estado.cargar('Ana');

    estado.anadir(reserva(3, 1, '2027-03-06T10:00:00+01:00', 'confirmada'));

    expect(estado.reservas().map((r) => r.id)).toEqual([1, 2, 3]);
  });

  it('no añade una reserva de otra persona', () => {
    estado.cargar('Ana');

    estado.anadir({ ...reserva(3, 1, '2027-03-06T10:00:00+01:00', 'confirmada'), persona: 'Luis' });

    expect(estado.reservas()).toHaveLength(2);
  });
});
