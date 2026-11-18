import { render, screen } from '@testing-library/angular';
import userEvent from '@testing-library/user-event';
import { OfertaListaDeEspera } from './oferta-lista-de-espera';

describe('OfertaListaDeEspera', () => {
  it('enseña la franja y emite apuntarme al pulsar el botón', async () => {
    const { fixture } = await render(OfertaListaDeEspera, {
      inputs: { franja: { inicio: '2027-03-04T10:00', fin: '2027-03-04T11:00' } },
    });
    const pulsaciones: unknown[] = [];
    fixture.componentInstance.apuntarme.subscribe((pulsacion) => pulsaciones.push(pulsacion));

    expect(screen.getByText(/ocupada el 2027-03-04 10:00 hasta las 11:00/)).toBeTruthy();
    await userEvent
      .setup()
      .click(screen.getByRole('button', { name: 'Apuntarme a la lista de espera' }));

    expect(pulsaciones).toHaveLength(1);
  });
});
