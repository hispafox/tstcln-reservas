import { sala } from '../../testing/datos-de-prueba';
import { admite } from './sala';

describe('admite', () => {
  const mediana = sala({ capacidadMinima: 4, capacidadMaxima: 10 });

  it('admite de la capacidad mínima a la máxima, las dos incluidas', () => {
    expect([admite(mediana, 4), admite(mediana, 10)]).toEqual([true, true]);
  });

  it('no admite menos que la mínima ni más que la máxima', () => {
    expect([admite(mediana, 3), admite(mediana, 11)]).toEqual([false, false]);
  });

  it('sin capacidad mínima, admite desde una persona', () => {
    expect(admite(sala({ capacidadMinima: null, capacidadMaxima: 4 }), 1)).toBe(true);
  });
});
