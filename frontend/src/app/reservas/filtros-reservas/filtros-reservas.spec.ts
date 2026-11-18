import { render, screen } from '@testing-library/angular';
import userEvent from '@testing-library/user-event';
import { FiltrosReservas } from './filtros-reservas';

describe('FiltrosReservas', () => {
  it('cambia el orden cuando se elige otro criterio', async () => {
    const { fixture } = await render(FiltrosReservas);

    await userEvent.setup().selectOptions(screen.getByRole('combobox'), 'sala');

    expect(fixture.componentInstance.orden()).toBe('sala');
  });
});
