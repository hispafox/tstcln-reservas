import { Capacidad } from './capacidad';

describe('Capacidad.leer', () => {
  it('lee un número entero de asistentes', () => {
    expect(Capacidad.leer('4')?.valor).toBe(4);
  });

  it('no lee el campo vacío', () => {
    expect(Capacidad.leer('')).toBeNull();
  });

  it('no lee cero asistentes', () => {
    expect(Capacidad.leer('0')).toBeNull();
  });

  it('no lee un número con decimales', () => {
    expect(Capacidad.leer('2.5')).toBeNull();
  });

  it('no lee un número negativo', () => {
    expect(Capacidad.leer('-1')).toBeNull();
  });
});
