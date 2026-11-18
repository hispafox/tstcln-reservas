import { aliasedInput, fireEvent, render, screen } from '@testing-library/angular';
import userEvent from '@testing-library/user-event';
import { throwError } from 'rxjs';
import { ApiSalasEnMemoria } from '../../../testing/api-salas-en-memoria';
import { salaNorte } from '../../../testing/datos-de-prueba';
import { ApiSalas } from '../../salas';
import { ApiReservas } from '../datos/api-reservas';
import { ReservaRechazadaError } from '../errores';
import { PantallaReserva } from './pantalla-reserva';

describe('PantallaReserva', () => {
  it('ofrece la lista de espera cuando la sala está ocupada, y no con otro rechazo', async () => {
    const crear = vi
      .fn()
      .mockReturnValueOnce(
        throwError(
          () =>
            new ReservaRechazadaError(
              'La sala Norte no admite una reserva de 1 asistentes en esa franja',
              'reserva_no_admitida',
            ),
        ),
      )
      .mockReturnValueOnce(
        throwError(
          () =>
            new ReservaRechazadaError(
              'La sala Norte ya está reservada en esa franja',
              'sala_no_disponible',
            ),
        ),
      );
    await render(PantallaReserva, {
      inputs: { ...aliasedInput('sala', salaNorte.id) },
      providers: [
        { provide: ApiSalas, useValue: new ApiSalasEnMemoria([salaNorte]) },
        { provide: ApiReservas, useValue: { crear } },
      ],
    });
    const usuario = userEvent.setup();
    await usuario.type(await screen.findByLabelText('Persona'), 'Ana');
    fireEvent.input(screen.getByLabelText('Hora de inicio'), {
      target: { value: '2027-03-04T10:00' },
    });
    fireEvent.input(screen.getByLabelText('Hora de fin'), {
      target: { value: '2027-03-04T11:00' },
    });

    await usuario.click(screen.getByRole('button', { name: 'Reservar' }));
    await screen.findByText('La sala Norte no admite una reserva de 1 asistentes en esa franja');
    expect(screen.queryByRole('button', { name: 'Apuntarme a la lista de espera' })).toBeNull();

    await usuario.click(screen.getByRole('button', { name: 'Reservar' }));
    expect(
      await screen.findByRole('button', { name: 'Apuntarme a la lista de espera' }),
    ).toBeTruthy();
  });
});
