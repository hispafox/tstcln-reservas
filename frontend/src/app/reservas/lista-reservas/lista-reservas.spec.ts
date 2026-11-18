import { render, screen } from '@testing-library/angular';
import userEvent from '@testing-library/user-event';
import { ApiReservasEnMemoria } from '../../../testing/api-reservas-en-memoria';
import { ApiSalasEnMemoria } from '../../../testing/api-salas-en-memoria';
import { sala } from '../../../testing/datos-de-prueba';
import { filas } from '../../../testing/filas';
import { ApiSalas } from '../../salas/api-salas';
import { ApiReservas } from '../api-reservas';
import { EstadoReserva, Reserva } from '../reserva';
import { ListaReservas } from './lista-reservas';

describe('ListaReservas', () => {
  it('enseña las próximas confirmadas de Ana, ordenadas por fecha', async () => {
    await render(ListaReservas, {
      providers: [
        { provide: ApiSalas, useValue: new ApiSalasEnMemoria([sala({ id: 1, nombre: 'Sala Norte' }), sala({ id: 2, nombre: 'Sala Sur' })]) },
        {
          provide: ApiReservas,
          useValue: new ApiReservasEnMemoria([
            reserva(1, 2, '2027-03-05T10:00:00+01:00', 'confirmada'),
            reserva(2, 1, '2027-03-04T10:00:00+01:00', 'confirmada'),
            reserva(3, 1, '2027-03-04T12:00:00+01:00', 'cancelada_a_tiempo'),
          ]),
        },
      ],
    });

    expect(filas()).toEqual(['Sala Norte2027-03-04 10:0011:0060 min4ConfirmadaCancelar', 'Sala Sur2027-03-05 10:0011:0060 min4ConfirmadaCancelar']);
  });

  it('sin el filtro y ordenada por sala, enseña todas', async () => {
    const { fixture } = await render(ListaReservas, {
      providers: [
        { provide: ApiSalas, useValue: new ApiSalasEnMemoria([sala({ id: 1, nombre: 'Sala Norte' }), sala({ id: 2, nombre: 'Sala Sur' })]) },
        {
          provide: ApiReservas,
          useValue: new ApiReservasEnMemoria([
            reserva(1, 2, '2027-03-05T10:00:00+01:00', 'confirmada'),
            reserva(2, 1, '2027-03-04T10:00:00+01:00', 'confirmada'),
            reserva(3, 1, '2027-03-04T12:00:00+01:00', 'cancelada_a_tiempo'),
          ]),
        },
      ],
    });

    await userEvent.click(screen.getByRole('checkbox'));
    await userEvent.selectOptions(screen.getByRole('combobox'), 'sala');
    await fixture.whenStable();

    expect(filas()).toEqual([
      'Sala Norte2027-03-04 10:0011:0060 min4ConfirmadaCancelar',
      'Sala Norte2027-03-04 12:0013:0060 min4Cancelada',
      'Sala Sur2027-03-05 10:0011:0060 min4ConfirmadaCancelar',
    ]);
  });

  it('vuelve a ordenar la tabla al cambiar el orden sin el filtro', async () => {
    const { fixture } = await render(ListaReservas, {
      providers: [
        { provide: ApiSalas, useValue: new ApiSalasEnMemoria([sala({ id: 1, nombre: 'Sala Norte' }), sala({ id: 2, nombre: 'Sala Sur' })]) },
        {
          provide: ApiReservas,
          useValue: new ApiReservasEnMemoria([
            reserva(1, 2, '2027-03-04T10:00:00+01:00', 'confirmada'),
            reserva(2, 1, '2027-03-05T10:00:00+01:00', 'confirmada'),
          ]),
        },
      ],
    });
    const usuario = userEvent.setup();

    await usuario.click(screen.getByRole('checkbox'));
    await fixture.whenStable();
    await usuario.selectOptions(screen.getByRole('combobox'), 'sala');
    await fixture.whenStable();

    expect(salasEnLaTabla()).toEqual(['Sala Norte', 'Sala Sur']);
  });

  it('después de cancelar, la reserva sale de las próximas confirmadas', async () => {
    const { fixture } = await render(ListaReservas, {
      providers: [
        { provide: ApiSalas, useValue: new ApiSalasEnMemoria([sala({ id: 1, nombre: 'Sala Norte' }), sala({ id: 2, nombre: 'Sala Sur' })]) },
        {
          provide: ApiReservas,
          useValue: new ApiReservasEnMemoria([
            reserva(1, 2, '2027-03-05T10:00:00+01:00', 'confirmada'),
            reserva(2, 1, '2027-03-04T10:00:00+01:00', 'confirmada'),
          ]),
        },
      ],
    });
    vi.spyOn(window, 'confirm').mockReturnValue(true);

    await userEvent.click(screen.getAllByRole('button', { name: 'Cancelar' })[0]);
    await fixture.whenStable();

    expect(filas()).toEqual(['Sala Sur2027-03-05 10:0011:0060 min4ConfirmadaCancelar']);
  });

  describe('con el reloj falso', () => {
    beforeEach(() => vi.useFakeTimers());
    afterEach(() => vi.useRealTimers());

    it('al salir de la pantalla deja de pedir las reservas cada minuto', async () => {
      const salas = new ApiSalasEnMemoria();
      const listar = vi.spyOn(salas, 'listar');
      const { fixture } = await render(ListaReservas, {
        providers: [
          { provide: ApiSalas, useValue: salas },
          { provide: ApiReservas, useValue: new ApiReservasEnMemoria() },
        ],
      });

      fixture.destroy();
      vi.advanceTimersByTime(60_000);

      expect(listar).toHaveBeenCalledTimes(1);
    });
  });
});

function reserva(id: number, sala: number, inicio: string, estado: EstadoReserva): Reserva {
  return { id, sala, inicio, fin: inicio.replace('T10', 'T11').replace('T12', 'T13'), persona: 'Ana', asistentes: 4, estado };
}

function salasEnLaTabla(): string[] {
  return screen.getAllByRole('row').slice(1).map((fila) => fila.querySelector('td')!.textContent!.trim());
}
