import { render, screen } from '@testing-library/angular';
import userEvent from '@testing-library/user-event';
import { ApiSalasEnMemoria } from '../../../testing/api-salas-en-memoria';
import { sala } from '../../../testing/datos-de-prueba';
import { ApiSalas } from '../api-salas';
import { BuscadorSalas } from './buscador-salas';

describe('BuscadorSalas', () => {
  it('enseña solo las salas donde caben los asistentes que se buscan', async () => {
    const salas = [sala({ nombre: 'Norte', capacidadMaxima: 4 }), sala({ nombre: 'Sur', capacidadMaxima: 10 })];
    const { fixture } = await render(BuscadorSalas, { providers: [{ provide: ApiSalas, useValue: new ApiSalasEnMemoria(salas) }] });
    const usuario = userEvent.setup();

    await usuario.clear(screen.getByLabelText('Asistentes'));
    await usuario.type(screen.getByLabelText('Asistentes'), '8');
    await usuario.click(screen.getByRole('button', { name: 'Buscar' }));
    await fixture.whenStable();

    expect(screen.getAllByRole('heading', { level: 2 }).map((titulo) => titulo.textContent)).toEqual(['Sur']);
  });
});
