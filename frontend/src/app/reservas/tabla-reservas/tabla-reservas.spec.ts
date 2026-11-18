import { render, screen } from '@testing-library/angular';
import userEvent from '@testing-library/user-event';
import { filas } from '../../../testing/filas';
import { TablaReservas } from './tabla-reservas';

describe('TablaReservas', () => {
  it('enseña cada reserva con su sala, su hora, su duración y su estado', async () => {
    await render(TablaReservas, { inputs: { reservas: [fila()] } });

    expect(filas()).toEqual(['Sala Norte2027-03-04 10:0011:0060 min4ConfirmadaCancelar']);
  });

  it('no ofrece cancelar una reserva que no está pendiente', async () => {
    await render(TablaReservas, { inputs: { reservas: [fila({ pendiente: false })] } });

    expect(screen.queryByRole('button', { name: 'Cancelar' })).toBeNull();
  });

  it('avisa de la reserva que se quiere cancelar', async () => {
    const reserva = fila({ id: 7 });
    const { fixture } = await render(TablaReservas, { inputs: { reservas: [reserva] } });
    const pedidas: any[] = [];
    fixture.componentInstance.cancelacionPedida.subscribe((pedida) => pedidas.push(pedida));

    await userEvent.setup().click(screen.getByRole('button', { name: 'Cancelar' }));

    expect(pedidas).toEqual([reserva]);
  });
});

function fila(cambios: Record<string, unknown> = {}) {
  return {
    id: 1,
    sala: 1,
    nombreSala: 'Sala Norte',
    inicio: '2027-03-04T10:00:00+01:00',
    fin: '2027-03-04T11:00:00+01:00',
    persona: 'Ana',
    asistentes: 4,
    estado: 'confirmada',
    duracion: 60,
    pendiente: true,
    ...cambios,
  };
}
