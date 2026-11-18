import { Reserva, estaPendiente } from './reserva';

describe('estaPendiente', () => {
  const ahora = new Date('2027-03-04T09:00:00+01:00');

  it('una confirmada que todavía no ha empezado está pendiente', () => {
    expect(estaPendiente(reserva({ inicio: '2027-03-04T10:00:00+01:00' }), ahora)).toBe(true);
  });

  it('una confirmada que ya ha empezado no lo está', () => {
    expect(estaPendiente(reserva({ inicio: '2027-03-04T08:30:00+01:00' }), ahora)).toBe(false);
  });

  it('una cancelada no lo está, aunque no haya empezado', () => {
    expect(estaPendiente(reserva({ estado: 'cancelada_a_tiempo' }), ahora)).toBe(false);
  });
});

function reserva(cambios: Partial<Reserva> = {}): Reserva {
  return {
    id: 1,
    sala: 1,
    inicio: '2027-03-04T10:00:00+01:00',
    fin: '2027-03-04T11:00:00+01:00',
    persona: 'Ana',
    asistentes: 4,
    estado: 'confirmada',
    ...cambios,
  };
}
