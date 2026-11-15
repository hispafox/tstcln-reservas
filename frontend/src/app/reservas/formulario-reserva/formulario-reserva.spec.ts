import { salaNorte } from '../../../testing/datos-de-prueba';
import { FormularioReservaHarness } from './formulario-reserva.harness';

describe('FormularioReserva', () => {
  it.todo('desactiva el botón mientras falten datos');
  it.todo('desactiva el botón si la hora de fin es anterior a la de inicio');
  it.todo('enseña el motivo si la API rechaza la reserva');
  it.todo('emite la reserva creada');
});
