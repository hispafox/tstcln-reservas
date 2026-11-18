import { FranjaHoraria } from './franja-horaria';

describe('FranjaHoraria', () => {
  const deDiezAOnce = FranjaHoraria.desde('2027-03-04T10:00', '2027-03-04T11:00');

  it('no se construye si el fin va antes del inicio', () => {
    expect(() => FranjaHoraria.desde('2027-03-04T11:00', '2027-03-04T10:00')).toThrow(RangeError);
  });

  it('no se construye con una fecha vacía', () => {
    expect(() => FranjaHoraria.desde('', '2027-03-04T11:00')).toThrow(RangeError);
  });

  it('cuenta sus minutos', () => {
    expect(FranjaHoraria.desde('2027-03-04T09:30', '2027-03-04T11:00').minutos()).toBe(90);
  });

  it('ha empezado cuando su inicio ya ha pasado', () => {
    expect(deDiezAOnce.haEmpezado(new Date('2027-03-04T10:30'))).toBe(true);
  });

  it('no ha empezado un minuto antes de su inicio', () => {
    expect(deDiezAOnce.haEmpezado(new Date('2027-03-04T09:59'))).toBe(false);
  });

  it('se solapa con otra que empieza antes de que ella acabe', () => {
    expect(
      deDiezAOnce.seSolapaCon(FranjaHoraria.desde('2027-03-04T10:59', '2027-03-04T12:00')),
    ).toBe(true);
  });

  it('no se solapa con otra que empieza justo cuando ella acaba', () => {
    expect(
      deDiezAOnce.seSolapaCon(FranjaHoraria.desde('2027-03-04T11:00', '2027-03-04T12:00')),
    ).toBe(false);
  });
});
