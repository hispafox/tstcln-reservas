import { TestBed } from '@angular/core/testing';
import { Avisos } from './avisos';

describe('Avisos', () => {
  beforeEach(() => vi.useFakeTimers());
  afterEach(() => vi.useRealTimers());

  it('muestra el aviso que se le pide', () => {
    const avisos = TestBed.inject(Avisos);

    avisos.mostrar('Reserva creada');

    expect(avisos.actual()).toBe('Reserva creada');
    expect(avisos.hayAviso()).toBe(true);
  });

  it('quita el aviso a los cinco segundos', () => {
    const avisos = TestBed.inject(Avisos);

    avisos.mostrar('Reserva creada');
    vi.advanceTimersByTime(4999);
    expect(avisos.actual()).toBe('Reserva creada');

    vi.advanceTimersByTime(1);
    expect(avisos.actual()).toBeNull();
  });

  it('cada aviso dura sus cinco segundos aunque llegue otro', () => {
    const avisos = TestBed.inject(Avisos);

    avisos.mostrar('Reserva creada');
    vi.advanceTimersByTime(3000);
    avisos.mostrar('Reserva cancelada');
    vi.advanceTimersByTime(2000);

    expect(avisos.actual()).toBe('Reserva cancelada');
  });
});
