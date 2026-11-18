import { ordenar, soloLasProximas } from './reservas-visibles';

describe('soloLasProximas', () => {
  it('deja las confirmadas que todavía no han empezado', () => {
    const reservas = [
      { id: 1, estado: 'confirmada', pendiente: true },
      { id: 2, estado: 'confirmada', pendiente: false },
      { id: 3, estado: 'cancelada_tarde', pendiente: false },
    ];

    expect(soloLasProximas(reservas).map((reserva) => reserva.id)).toEqual([1]);
  });
});

describe('ordenar', () => {
  const reservas = () => [
    { id: 1, inicio: '2027-03-05T10:00:00+01:00', nombreSala: 'Sala Sur', duracion: 60 },
    { id: 2, inicio: '2027-03-04T10:00:00+01:00', nombreSala: 'Sala Norte', duracion: 30 },
    { id: 3, inicio: '2027-03-04T12:00:00+01:00', nombreSala: 'Sala Grande', duracion: 90 },
  ];

  it('por fecha, de la más próxima a la más lejana', () => {
    expect(ordenar(reservas(), 'fecha').map((reserva) => reserva.id)).toEqual([2, 3, 1]);
  });

  it('por sala, por orden alfabético del nombre', () => {
    expect(ordenar(reservas(), 'sala').map((reserva) => reserva.id)).toEqual([3, 2, 1]);
  });

  it('por duración, de la más larga a la más corta', () => {
    expect(ordenar(reservas(), 'duracion').map((reserva) => reserva.id)).toEqual([3, 1, 2]);
  });

  it('con un criterio que no conoce, las deja como llegan', () => {
    expect(ordenar(reservas(), 'persona').map((reserva) => reserva.id)).toEqual([1, 2, 3]);
  });
});
