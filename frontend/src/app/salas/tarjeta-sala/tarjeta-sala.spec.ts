import { render, screen } from '@testing-library/angular';
import { salaNorte, salaSur } from '../../../testing/datos-de-prueba';
import { TarjetaSala } from './tarjeta-sala';

describe('TarjetaSala', () => {
  it('enseña el nombre, la capacidad y el equipamiento de la sala', async () => {
    await render(TarjetaSala, { inputs: { sala: salaSur } });

    expect(screen.getByRole('heading', { name: 'Sur' })).toBeTruthy();
    expect(screen.getByText('Hasta 10 personas')).toBeTruthy();
    expect(screen.getByText('pantalla')).toBeTruthy();
  });

  it('no enseña la línea del equipamiento si la sala no tiene', async () => {
    const { container } = await render(TarjetaSala, { inputs: { sala: salaNorte } });

    expect(screen.getByText('Hasta 4 personas')).toBeTruthy();
    expect(container.querySelector('.equipamiento')).toBeNull();
  });
});
