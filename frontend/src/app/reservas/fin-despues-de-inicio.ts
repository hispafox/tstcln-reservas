import { ValidatorFn } from '@angular/forms';

/** Error de grupo si la hora de fin no va después de la de inicio. Calla mientras falte alguna. */
export const finDespuesDeInicio: ValidatorFn = (grupo) => {
  const inicio: string = grupo.get('inicio')?.value;
  const fin: string = grupo.get('fin')?.value;
  if (!inicio || !fin) return null;
  return fin > inicio ? null : { finAntesDeInicio: true };
};
