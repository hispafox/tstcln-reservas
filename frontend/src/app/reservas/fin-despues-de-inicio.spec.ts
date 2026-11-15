import { FormControl, FormGroup } from '@angular/forms';
import { finDespuesDeInicio } from './fin-despues-de-inicio';

describe('finDespuesDeInicio', () => {
  const franja = (inicio: string, fin: string) =>
    new FormGroup({
      inicio: new FormControl(inicio),
      fin: new FormControl(fin),
    });

  it('admite un fin posterior al inicio', () => {
    expect(
      finDespuesDeInicio(franja('2027-03-04T10:00', '2027-03-04T11:00')),
    ).toBeNull();
  });

  it('rechaza un fin igual al inicio', () => {
    expect(
      finDespuesDeInicio(franja('2027-03-04T10:00', '2027-03-04T10:00')),
    ).toEqual({ finAntesDeInicio: true });
  });

  it('rechaza un fin anterior al inicio', () => {
    expect(
      finDespuesDeInicio(franja('2027-03-04T11:00', '2027-03-04T10:00')),
    ).toEqual({ finAntesDeInicio: true });
  });

  it('no se pronuncia mientras falte una de las dos horas', () => {
    expect(finDespuesDeInicio(franja('2027-03-04T10:00', ''))).toBeNull();
  });
});
